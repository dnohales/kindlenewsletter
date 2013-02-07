<?php

namespace Eor\KnlBundle\GoogleReader;

/**
 * Description of ParametersConfiguration
 *
 * @author damiannohales
 */
class Configuration
{
	private $clientId;
	private $emailAddress;
	private $clientSecret;
	private $redirectUri;
	private $javascriptOrigin;
	
	public function __construct($clientId, $emailAddress, $clientSecret, $redirectUri, $javascriptOrigin)
	{
		$this->clientId = $clientId;
		$this->emailAddress = $emailAddress;
		$this->clientSecret = $clientSecret;
		$this->redirectUri = $redirectUri;
		$this->javascriptOrigin = $javascriptOrigin;
	}
	
	public function getClientId()
	{
		return $this->clientId;
	}

	public function getEmailAddress()
	{
		return $this->emailAddress;
	}

	public function getClientSecret()
	{
		return $this->clientSecret;
	}

	public function getRedirectUri()
	{
		return $this->redirectUri;
	}

	public function getJavascriptOrigin()
	{
		return $this->javascriptOrigin;
	}
	
}