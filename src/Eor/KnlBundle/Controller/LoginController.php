<?php
/*
 * This file is part of the KindleNewsletter.com package.
 * 
 * (c) Damián Nohales <damiannohales@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code. 
 */

namespace Eor\KnlBundle\Controller;

use Eor\KnlBundle\GoogleReader\AccessDeniedException;
use Eor\KnlBundle\Security\GoogleAccessToken;
use Eor\KnlBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Description of LoginController
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class LoginController extends Controller {

	public function loginAction()
	{
		//I get the token in this ugly way until I discover how to get the token
		//from I firewall in which I am not.
		if($this->getRequest()->getSession()->has('_security_secured_area')){
			return $this->redirect($this->generateUrl('homepage'));
		}
		
		return $this->render('EorKnlBundle:Login:login.html.twig', array(
			'loginUrl' => $this->get('greader_client')->getAuthenticator()->getLoginUrl()
		));
	}
	
	/**
	 * Analyze and authorize the Google OAuth2 code and initialize the user
	 * session.
	 * @return Response
	 * @throws \Exception
	 * @todo put this logic into an AuthenticationManager/Listener or whatever
	 * needed
	 */
	public function googleCheckAction()
	{
		//If we have access token and is not expired, redirect to homepage.
		//This is because if we redirect with expired token, the homepage will
		//redirect to Google OAuth2 URL again and will cause recursion.
		if($this->getRequest()->getSession()->has('_security_secured_area')){
			/* @var $token GoogleAccessToken */
			$token = unserialize($this->getRequest()->getSession()->get('_security_secured_area'));
			if(!$token->getTokenInformation()->isAccessTokenExpired()){
				return $this->redirect($this->generateUrl('homepage'));
			}
		}
		
		/* @var $authenticator \Eor\KnlBundle\GoogleReader\Authenticator */
		$authenticator = $this->get('greader_client')->getAuthenticator();
		
		/* @var $googleClient \Eor\KnlBundle\GoogleReader\Client */
		$googleClient = $this->get('greader_client');
		
		try{
			//Get token information
			$tokenInformation = $authenticator->getTokenInformation($this->getRequest());
			
			//Get user profile
			$googleClient->setTokenInformation($tokenInformation);
			$profileData = $googleClient->request('https://www.googleapis.com/oauth2/v2/userinfo', 'GET', array(), array());
			
			if(!is_array($profileData) || !isset($profileData['id'])){
				throw new \Exception('Google profile data has not a user ID.');
			}
			
			//Create/Get the user entity against Google user profile
			$user = $this->getEntityManager()->getRepository('EorKnlBundle:User')->find($profileData['id']);
			if($user === null){
				$user = new User();
			}
			$user->setProfileData($profileData);
			
			//Create the Symfony2 token to create the session
			$token = new GoogleAccessToken($tokenInformation);
			$token->setUser($user);
			$this->getRequest()->getSession()->set('_security_secured_area',  serialize($token));
			
			$redirectUrl = $this->generateUrl('homepage');
			$state = $this->getRequest()->get('state');
			if($state !== null){
				$urlErrors = $this->get('validator')->validateValue($state, new Url());
				if(count($urlErrors) == 0){
					$redirectUrl = $state;
				}
			} else {
				$user->setSignInTime(new \DateTime());
			}
			
			$this->getEntityManager()->persist($user);
			$this->getEntityManager()->flush();
			return $this->redirect($redirectUrl);
		} catch(AccessDeniedException $e){
			$this->get('session')->setFlash('login.error', 'You must grant access to your Google Account so that the application can run.');
			$this->get('logger')->err('Login error: '.$e->getMessage());
			return $this->redirect($this->generateUrl('login'));
		} catch(\Exception $e){
			$this->get('session')->setFlash('login.error', 'An unexpected error occurred during logon.');
			$this->get('logger')->err('Login error: '.$e->getMessage());
			return $this->redirect($this->generateUrl('login'));
		}
	}

}