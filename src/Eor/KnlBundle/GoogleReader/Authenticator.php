<?php
namespace Eor\KnlBundle\GoogleReader;

use Eor\KnlBundle\Curl\Curl;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of Authenticator
 *
 * @author damiannohales
 */
class Authenticator
{
	const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/revoke';
	const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
	const OAUTH2_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
	
	private $configuration;
	private $logger;
	
	public function __construct(Configuration $configuration, Logger $logger)
	{
		$this->configuration = $configuration;
		$this->logger = $logger;
	}
	
	public function getConfiguration()
	{
		return $this->configuration;
	}
	
	public function getLoginUrl()
	{
		$scopes = array(
			"http://www.google.com/reader/api",
			"https://www.googleapis.com/auth/userinfo.profile",
			"https://www.googleapis.com/auth/userinfo.email",
		);
		
		return self::OAUTH2_AUTH_URL."?".
			   "scope=".urlencode(implode(' ', $scopes)).
			   "&redirect_uri=".urlencode($this->getConfiguration()->getRedirectUri()).
			   "&client_id=".urlencode($this->getConfiguration()->getClientId()).
			   "&response_type=code";
	}
	
	public function getTokenInformation(Request $request)
	{
		try {
			if($request->query->has('error')){
				throw new AccessDeniedException('Google has denied the access, error throwed: '.$request->query->get('error'));
			} else {
				if(!$request->query->has('code')){
					throw new UnexpectedAuthentincationException('Google does not give us any code');
				} else {
					return $this->authorizeCode($request->query->get('code'));
				}
			}
		} catch(\Exception $e){
			$this->logger->err('Google authenticaton failed: '.$e->getMessage());
			throw $e;
		}
		
	}
	
	private function authorizeCode($code)
	{
		$curl = new Curl(self::OAUTH2_TOKEN_URI, 'POST');
		
		$curl->setPostFields(array(
			'code' => $code,
			'client_id' => $this->getConfiguration()->getClientId(),
			'client_secret' => $this->getConfiguration()->getClientSecret(),
			'redirect_uri' => $this->getConfiguration()->getRedirectUri(),
			'grant_type' => 'authorization_code'
		));
		
		$tokenData = $curl->executeJson();
		
		return new TokenInformation(
			isset($tokenData['access_token'])? $tokenData['access_token']:null,
			isset($tokenData['refresh_token'])? $tokenData['refresh_token']:null,
			isset($tokenData['expires_in'])? $tokenData['expires_in']:null,
			isset($tokenData['token_type'])? $tokenData['token_type']:null
		);
	}
}