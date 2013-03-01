<?php
namespace Eor\KnlBundle\Readability;

use Eor\KnlBundle\Curl\Curl;
use Monolog\Logger;
use Symfony\Component\Serializer\Encoder\JsonEncode;

/**
 * Description of Client
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class Client
{
	const PARSER_URL = 'http://www.readability.com/api/content/v1/parser';
	
	private $token;
	
	private $logger;
	
	public function __construct($token, Logger $logger)
	{
		$this->token = $token;
		$this->logger = $logger;
	}
	
	public function isAvailable()
	{
		return $this->token != null;
	}
	
	public function parse($url)
	{
		$curl = new Curl();
		
		$url = self::PARSER_URL.'?'.http_build_query(array(
			'url' => $url,
			'token' => $this->token
		));
		$curl->setUrl($url);
		
		try {
			$articleData = $curl->executeJson();
			$this->logger->debug("ReadabilityClient API success. URL: {$curl->getUrl()}", array('readability_client.success'));
			return new Article($articleData);
		} catch(CurlException $e) {
			$this->logger->err("ReadabilityClient API failure: {$e->getMessage()}", array('readability_client.failure'));
			throw new ReadabilityApiException($e->getMessage(), $e->getCode(), $e);
		}
	}

}