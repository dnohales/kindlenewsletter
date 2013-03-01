<?php
namespace Eor\KnlBundle\Curl;

use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\OptionsResolver\Options;

class Curl
{
	/**
	 * cURL resource
	 *
	 * @var resource
	 */
	private $resource;

	/**
	 * cURL options
	 *
	 * @var array
	 */
	private $options;
	
	/**
	 * The response header
	 */
	private $responseHeader;

	/**
	 * Get the cURL version information
	 * @param int $age The cURL version to check, by default is the current
	 * version.
	 * @return array Returns an associative array as described in curl_version
	 * PHP function.
	 * @throws \RuntimeException when cURL is not installed
	 */
	public static function getVersionInformation($age = CURLVERSION_NOW)
	{
		if(function_exists('curl_version')){
			return curl_version($age);
		} else{
			throw new \RuntimeException("cURL is not installed.");
		}
	}
	
	/**
	 * Constructor
	 * @param type $url The initial URL
	 * @param type $method The initial HTTP method
	 * @throws \RuntimeException when cURL is not installed
	 */
	public function __construct($url = null, $method = 'GET')
	{
		if(function_exists('curl_version')){
			$this->options = array();
			$this->reset();
			$this->setUrl($url);
			$this->setMethod($method);
		} else{
			throw new \RuntimeException("cURL is not installed.");
		}
	}
	
	final public function __destruct()
	{
		$this->close();
	}
	
	/**
	 * Safely close the cURL resource
	 */
	public function close()
	{
		if(is_resource($this->resource)){
			curl_close($this->resource);
		}
	}
	
	/**
	 * Close the current cURL resource and reset it options
	 */
	public function reset()
	{
		$this->close();
		$this->resource = curl_init();
		$this->setOptions(array(
			CURLOPT_URL => null,
			CURLOPT_HEADER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FAILONERROR => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true
		));
	}
	
	/**
	 * Make the request.
	 * @return string The returned content
	 * @throws CurlException When the request fail.
	 */
	public function execute()
	{
		curl_setopt_array($this->resource, $this->getOptions());
		$response = curl_exec($this->resource);
		
		$headerSize = $this->getInfo(CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $headerSize);
		$body = substr($response, $headerSize);
		
		$this->responseHeader = $header;
		
		if($this->getHttpCode() >= 400){
			throw new CurlException("Error on cURL request: #{$this->getErrno()}: {$this->getError()}. HTTP code: {$this->getHttpCode()}. URL: \"{$this->getUrl()}\"");
		}
		
		return $body;
	}
	
	public function executeJson($associative = true)
	{
		return json_decode($this->execute(), $associative);
	}
	
	
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}
	
	public function getOption($key, $default = null)
	{
		return isset($this->options[$key])? $this->options[$key]:$default;
	}
	
	public function setOptions(array $options)
	{
		$this->options = $options;
	}
	
	public function getOptions()
	{
		return $this->options;
	}
	
	/**
	 * Sets the URL
	 * @param string $url The new URL
	 * @return \Eor\KnlBundle\Curl\Curl this
	 */
	public function setUrl($url)
	{
		$this->setOption(CURLOPT_URL, $url);
	}
	
	/**
	 * Get the URL
	 * @return string The URL
	 */
	public function getUrl()
	{
		return $this->getOption(CURLOPT_URL);
	}
	
	public function setMethod($method)
	{
		$this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));
	}
	
	public function getMethod()
	{
		return $this->getOption(CURLOPT_CUSTOMREQUEST);
	}
	
	public function setPostFields(array $params)
	{
		$postQuery = http_build_query($params);
		$this->setOption(CURLOPT_POSTFIELDS, $postQuery);
	}
	
	public function getHttpCode()
	{
		return (int)curl_getinfo($this->resource, CURLINFO_HTTP_CODE);
	}
	
	public function setHeaders(array $headers)
	{
		$this->setOption(CURLOPT_HTTPHEADER, $headers);
	}
	
	public function getHeaders()
	{
		return $this->getOption(CURLOPT_HTTPHEADER, array());
	}
	
	public function addHeader($header)
	{
		$headers = $this->getHeaders();
		$headers[] = $header;
		$this->setHeaders($headers);
	}
	
	public function getResponseHeader()
	{
		return $this->responseHeader;
	}

	public function getInfo($opt = 0)
	{
		return curl_getinfo($this->resource, $opt);
	}
	
	public function hasError()
	{
		return $this->getErrno() !== 0;
	}
	
	public function getErrno()
	{
		return curl_errno($this->resource);
	}
	
	public function getError()
	{
		$err = curl_error($this->resource);
		return $err == ''? null:$err;
	}

}