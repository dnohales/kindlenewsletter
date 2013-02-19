<?php
namespace Eor\KnlBundle\GoogleReader\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Eor\KnlBundle\GoogleReader\GoogleApiTokenException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Eor\KnlBundle\GoogleReader\Client;

/**
 * This class is redirect to Google OAuth2 login page when the token
 * expiration occurs.
 *
 * @author damiannohales
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
			$response = new RedirectResponse($this->googleClient->getAuthenticator()->getLoginUrl());
			$event->setResponse($response);
		}
	}

}