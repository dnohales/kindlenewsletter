<?php
namespace Eor\KnlBundle\GoogleReader;

/**
 * Description of TokenInformation
 *
 * @author damiannohales
 */
class TokenInformation
{
	private $accessToken;
	private $refreshToken;
	private $expiresIn;
	private $tokenType;
	
	function __construct($accessToken, $refreshToken, $expiresIn, $tokenType)
	{
		$this->accessToken = $accessToken;
		$this->refreshToken = $refreshToken;
		$this->expiresIn = $expiresIn;
		$this->tokenType = $tokenType;
	}
	
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	public function getRefreshToken()
	{
		return $this->refreshToken;
	}

	public function getExpiresIn()
	{
		return $this->expiresIn;
	}

	public function getTokenType()
	{
		return $this->tokenType;
	}

}