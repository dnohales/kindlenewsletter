<?php
/*
 * This file is part of the KindleNewsletter.com package.
 * 
 * (c) Damián Nohales <damiannohales@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code. 
 */

namespace Eor\KnlBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Eor\KnlBundle\GoogleReader\TokenInformation;

/**
 * Description of GoogleAccessToken
 *
 * @author Damián Nohales <damiannohales@gmail.com>
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