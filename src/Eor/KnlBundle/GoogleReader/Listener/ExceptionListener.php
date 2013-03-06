<?php
/*
 * This file is part of the KindleNewsletter.com package.
 * 
 * (c) Damián Nohales <damiannohales@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code. 
 */

namespace Eor\KnlBundle\GoogleReader\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Eor\KnlBundle\GoogleReader\GoogleApiTokenException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Eor\KnlBundle\GoogleReader\Client;

/**
 * This class is redirect to Google OAuth2 login page when the token
 * expiration occurs.
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class ExceptionListener
{
	private $googleClient;
	
	function __construct(Client $googleClient)
	{
		$this->googleClient = $googleClient;
	}
	
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		if($event->getException() instanceof GoogleApiTokenException){
			$currentUri = $event->getRequest()->getUri();
			$response = new RedirectResponse($this->googleClient->getAuthenticator()->getLoginUrl($currentUri));
			$event->setResponse($response);
		}
	}

}