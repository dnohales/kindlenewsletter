<?php
namespace Eor\KnlBundle\GoogleReader;

use Monolog\Logger;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Eor\KnlBundle\GoogleReader\TokenInformation;
use Eor\KnlBundle\Curl\Curl;
use Eor\KnlBundle\Curl\CurlException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Client
 *
 * @author damiannohales
 */
class Client 
{
	const READER_BASE_PATH = 'https://www.google.com/reader/api/0/';
	
	const STATE_READ = 'user/-/state/com.google/read';
	const STATE_READING_LIST = 'user/-/state/com.google/reading-list';
	const STATE_STAR = 'user/-/state/com.google/starred';
	
	const SORT_NEW = 'n';
	const SORT_OLD = 'o';
	const SORT_AUTO = 'a';
	
	private $authenticator;
	private $configuration;
	private $securityContext;
	private $logger;
	private $customTokenInformation;
	
	public function __construct(Configuration $configuration, SecurityContextInterface $securityContext, Logger $logger)
	{
		$this->configuration = $configuration;
		$this->authenticator = new Authenticator($configuration, $logger);
		$this->securityContext = $securityContext;
		$this->logger = $logger;
	}
	
	/**
	 * 
	 * @return Authenticator
	 */
	public function getAuthenticator()
	{
		return $this->authenticator;
	}
	
	/**
	 * 
	 * @return Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}
	
	/**
	 * 
	 * @return SecurityContextInterface
	 */
	public function getSecurityContext()
	{
		return $this->securityContext;
	}
	
	/**
	 * 
	 * @return TokenInformation
	 */
	public function getTokenInformation()
	{
		if($this->customTokenInformation !== null){
			return $this->customTokenInformation;
		}
		
		/* @var $token Eor\KnlBundle\Security\GoogleAccessToken */
		$token = $this->getSecurityContext()->getToken();
		if($token !== null){
			return $token->getTokenInformation();
		} else {
			return null;
		}
	}
	
	public function setTokenInformation(TokenInformation $tokenInformation)
	{
		$this->customTokenInformation = $tokenInformation;
	}
	
	/**
	 * Get the Google Reader state name applying the Google Reader user ID to the
	 * state string
	 * @param string $stateConstant The State template string, see the STATE_*
	 * constants.
	 * @return string A fully qualified state just like user/<user_id>/state/com.google/read
	 */
	public function getUserState($stateConstant)
	{
		$replacement = '/'.($this->getTokenInformation()->getGoogleReaderUserId() ?: '-').'/';
		return str_replace('/-/', $replacement, $stateConstant);
	}
	
	public function request($url, $method = 'GET', array $getFields = array(), array $postFields = array(), $output = 'json')
	{
		$tokenInformation = $this->getTokenInformation();
		if($tokenInformation === null){
			throw new GoogleApiException('There is no TokenInformation available');
		}
		
		if($tokenInformation->isAccessTokenExpired()){
			throw new GoogleApiTokenException('The access token is expired');
		}
		
		$curl = new Curl();
		
		if($output !== null){
			$getFields['output'] = $output;
		}
		$url .= '?'.http_build_query($getFields);
		$curl->setUrl($url);
		
		$curl->setMethod($method);
		$curl->setPostFields($postFields);
		$curl->addHeader("Authorization: {$tokenInformation->getTokenType()} {$tokenInformation->getAccessToken()}");
		
		try {
			$response = $curl->execute();
			$this->logger->debug("GoogleClient API success. URL: {$curl->getUrl()}. Response:\n".$response, array('greader_client.success'));
			if($output === 'json'){
				$response = json_decode($response, true);
			}
			$tokenInformation->setGoogleReaderUserId($this->extractGoogleReaderUserId($curl));
		} catch(CurlException $e) {
			$this->logger->err("GoogleClient API failure: {$e->getMessage()}", array('greader_client.failure'));
			if($curl->getHttpCode() == 401){
				throw new GoogleApiTokenException($e->getMessage(), $e->getCode(), $e);
			} else {
				throw new GoogleApiException($e->getMessage(), $e->getCode(), $e);
			}
		}
		
		return $response;
	}
	
	private function extractGoogleReaderUserId(Curl $curl)
	{
		$matches = null;
		preg_match('/X-Reader-User: (\d+)\r\n/i', $curl->getResponseHeader(), $matches);
		return isset($matches[1])? $matches[1]:null;
	}

	public function readerRequest($path, $method = 'GET', array $getFields = array(), array $postFields = array(), $output = 'json')
	{
		$getFields['ck'] = time();
		$getFields['client'] = 'scroll';
		return $this->request(self::READER_BASE_PATH.$path, $method, $getFields, $postFields, $output);
	}
	
	public function getActionToken()
	{
		$tokenInformation = $this->getTokenInformation();
		
		if($tokenInformation->getActionToken() === null || $tokenInformation->isActionTokenExpired()){
			$actionToken = $this->readerRequest('token', 'GET', array(), array(), null);
			$tokenInformation->setActionToken($actionToken);
		}
		
		return $tokenInformation->getActionToken();
	}
	
	public function getSubscriptions()
	{
		$subscriptions = new Model\Index();
		$subscriptionsData = $this->readerRequest('subscription/list');
		$countData = $this->readerRequest('unread-count');
		
		$subscriptions->setUpdated(new \DateTime('now'));
		
		$subscriptions->addFeed(Model\Factory::createGlobalUnreadFeed($this));
		$subscriptions->addFeed(Model\Factory::createStarredFeed($this));
		
		foreach($subscriptionsData['subscriptions'] as $s){
			$subscriptions->addFeedByData($s);
		}
		
		foreach($countData['unreadcounts'] as $c){
			$cid = Model\Stream::idToKey($c['id']);
			if( $subscriptions->has($cid) ){
				$subscriptions->get($cid)->setCount(isset($c['count'])? $c['count']:null);
				$subscriptions->get($cid)->setNewestItemTimestampUsec(isset($c['newestItemTimestampUsec'])? $c['newestItemTimestampUsec']:null);
			}
		}
		
		return $subscriptions;
	}
	
	public function getItemList(Model\Stream $stream, $sort, $number, $excludeTargets, $continuation, $startTime = null)
	{
		$id = $stream->getId();
		if(strpos($id, 'feed/') === 0){
			$id = 'feed/'.urlencode(substr($id, 5));
		} else {
			$id = urlencode($id);
		}
		
		$getFields = array(
			'r' => $sort,
			'n' => $number
		);
		
		if($excludeTargets !== null) $getFields['xt'] = $excludeTargets;
		if($continuation !== null) $getFields['c'] = $continuation;
		if($startTime !== null) $getFields['ot'] = $startTime;
		
		$itemsData = $this->readerRequest('stream/contents/'.$id, 'GET', $getFields);
		return Model\Factory::createItemList($stream, $itemsData, $continuation);
	}
	
	public function setState($itemId, $originId, $state, $set = true)
	{
		$postFields = array(
			'i' => $itemId,
			's' => $originId,
			'async' => 'true',
			'T' => $this->getActionToken()
		);
		$state = $this->getUserState($state);
		if($set){
			$postFields['a'] = $state;
		} else {
			$postFields['r'] = $state;
		}
		
		$this->readerRequest('edit-tag', 'POST', array(), $postFields);
	}

}