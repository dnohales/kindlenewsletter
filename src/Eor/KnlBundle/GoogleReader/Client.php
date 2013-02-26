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
	const STATE_STAR = 'user/-/state/com.google/starred';
	
	const SORT_NEW = 'n';
	const SORT_OLD = 'o';
	const SORT_AUTO = 'a';
	
	private $authenticator;
	private $configuration;
	private $securityContext;
	private $logger;
	
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
	
	public function getTokenInformation()
	{
		/* @var $token Eor\KnlBundle\Security\GoogleAccessToken */
		$token = $this->getSecurityContext()->getToken();
		if($token !== null){
			return $token->getTokenInformation();
		} else {
			return null;
		}
	}
	
	public function request($url, $method = 'GET', array $getFields = array(), array $postFields = array(), TokenInformation $customTokenInformation = null)
	{
		/* @var $tokenInformation TokenInformation */
		$tokenInformation = $customTokenInformation ?: $this->getTokenInformation();
		if($tokenInformation === null){
			throw new GoogleApiException('Google client needs a SecurityContext with populated TokenInformation to make a request or you can populate the method last parameter.');
		}
		
		$curl = new Curl();
		
		$getFields['output'] = 'json';
		$url .= '?'.http_build_query($getFields);
		$curl->setUrl($url);
		
		$curl->setPostFields($postFields);
		$curl->addHeader("Authorization: {$tokenInformation->getTokenType()} {$tokenInformation->getAccessToken()}");
		
		try {
			$response = $curl->executeJson();
			$this->logger->debug("GoogleClient API success. URL: {$curl->getUrl()}. Response:\n".print_r($response, true), array('greader_client.success'));
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

	public function readerRequest($path, $method = 'GET', array $getFields = array(), array $postFields = array(), TokenInformation $customTokenInformation = null)
	{
		$getFields['ck'] = time();
		$getFields['client'] = 'scroll';
		return $this->request(self::READER_BASE_PATH.$path, $method, $getFields, $postFields, $customTokenInformation);
	}
	
	public function getSubscriptions()
	{
		$subscriptions = new Model\Index();
		$subscriptionsData = $this->readerRequest('subscription/list');
		$countData = $this->readerRequest('unread-count');
		
		$subscriptions->setUpdated(new \DateTime('now'));
		
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

}