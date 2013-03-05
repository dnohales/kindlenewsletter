<?php
namespace Eor\KnlBundle\GoogleReader;

/**
 * Description of TokenInformation
 *
 * @author damiannohales
 */
class TokenInformation
{
	const EXPIRE_THRESHOLD = 30;
	const ACTION_TOKEN_EXPIRE_TIME = 1800;

	private $accessToken;
	private $accessTokenTime;
	private $refreshToken;
	private $expiresIn;
	private $tokenType;
	private $actionToken;
	private $actionTokenTime;
	private $googleReaderUserId;

	function __construct($accessToken, $refreshToken, $expiresIn, $tokenType)
	{
		$this->accessToken = $accessToken;
		$this->accessTokenTime = time();
		$this->refreshToken = $refreshToken;
		$this->expiresIn = $expiresIn;
		$this->tokenType = $tokenType;
	}

	public function getAccessToken()
	{
		return $this->accessToken;
	}

	public function getAccessTokenTime()
	{
		return $this->accessTokenTime;
	}

	public function isAccessTokenExpired()
	{
		return (time() - $this->getAccessTokenTime()) > ($this->getExpiresIn() - self::EXPIRE_THRESHOLD);
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

	public function getActionToken()
	{
		return $this->actionToken;
	}

	public function setActionToken($actionToken)
	{
		$this->actionToken = $actionToken;
		$this->actionTokenTime = time();
	}

	public function getActionTokenTime()
	{
		return $this->actionTokenTime;
	}

	public function isActionTokenExpired()
	{
		return (time() - $this->getActionTokenTime()) > (self::ACTION_TOKEN_EXPIRE_TIME - self::EXPIRE_THRESHOLD);
	}
	
	public function getGoogleReaderUserId()
	{
		return $this->googleReaderUserId;
	}

	public function setGoogleReaderUserId($googleReaderUserId)
	{
		$this->googleReaderUserId = $googleReaderUserId;
	}
	
}