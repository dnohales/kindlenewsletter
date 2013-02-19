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
		$subscriptions = array();
		$all = array();
		$subscriptionsData = $this->readerRequest('subscription/list');
		
		foreach($subscriptionsData['subscriptions'] as $s){
			$feed = $this->createFeed($s);
			if(count($s['categories']) > 0){
				foreach($s['categories'] as $cat){
					if(!isset($subscriptions[$cat['id']])){
						$category = $this->createCategory($cat);
						$subscriptions[$category->getId()] = $category;
						$all[$category->getId()] = $category;
					} else {
						$category = $subscriptions[$cat['id']];
					}
					$category->addFeed($feed);
				}
			} else {
				$subscriptions[$feed->getId()] = $feed;
				$all[$feed->getId()] = $feed;
			}
		}
		
		$countData = $this->readerRequest('unread-count');
		foreach($countData['unreadcounts'] as $c){
			if( isset($all[$c['id']]) ){
				$all[$c['id']]->setCount($c['count']);
			}
		}
		
		return new ArrayCollection($subscriptions);
	}
	
	private function createFeed($s)
	{
		$feed = new Model\Stream();
		$feed->setIconType(Model\Stream::ICON_FEED);
		$feed->setId($s['id']);
		$feed->setTitle($s['title']);
		$feed->setSortId($s['sortid']);
		//$feed->setHtmlUrl($s['htmlUrl']);
		
		return $feed;
	}
	
	private function createCategory($cat)
	{
		$category = new Model\Stream();
		$category->setIconType(Model\Stream::ICON_CATEGORY);
		$category->setId($cat['id']);
		$category->setTitle($cat['label']);
		
		return $category;
	}

}