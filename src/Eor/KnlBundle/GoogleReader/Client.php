<?php
namespace Eor\KnlBundle\GoogleReader;

use Monolog\Logger;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Eor\KnlBundle\GoogleReader\TokenInformation;
use Eor\KnlBundle\Curl\Curl;
use Eor\KnlBundle\Curl\CurlException;

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
	
	public function getAuthenticator()
	{
		return $this->authenticator;
	}
	
	public function getConfiguration()
	{
		return $this->configuration;
	}

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
			throw $e;
		}
		
		return $response;
	}

	public function readerRequest($path, $method = 'GET', array $getFields = array(), array $postFields = array(), TokenInformation $customTokenInformation = null)
	{
		return $this->request(self::READER_BASE_PATH.$path, $method, $getFields, $postFields, $customTokenInformation);
	}

}