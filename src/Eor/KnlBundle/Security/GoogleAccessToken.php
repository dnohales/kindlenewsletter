<?php
namespace Eor\KnlBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Eor\KnlBundle\GoogleReader\TokenInformation;

/**
 * Description of GoogleAccessToken
 *
 * @author damiannohales
 */
class GoogleAccessToken extends AbstractToken
{
	private $tokenInformation;
	
	public function __construct(TokenInformation $tokenInformation)
	{
		parent::__construct(array('ROLE_USER'));
		$this->tokenInformation = $tokenInformation;
		$this->setAuthenticated(true);
	}
	
	public function getCredentials()
	{
		return '';
	}
	
	public function getTokenInformation()
	{
		return $this->tokenInformation;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array($this->tokenInformation, parent::serialize()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($str)
	{
		list($this->tokenInformation, $parentStr) = unserialize($str);
		parent::unserialize($parentStr);
	}
}