<?php

namespace Eor\KnlBundle\Controller;

use Eor\KnlBundle\GoogleReader\AccessDeniedException;
use Eor\KnlBundle\Security\GoogleAccessToken;
use Eor\KnlBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Description of LoginController
 *
 * @author damiannohales
 */
class LoginController extends Controller {

	public function loginAction()
	{
		$this->checkLogin();
		return $this->render('EorKnlBundle:Login:login.html.twig', array(
			'loginUrl' => $this->get('greader_client')->getAuthenticator()->getLoginUrl()
		));
	}
	
	public function googleCheckAction()
	{
		$this->checkLogin();
		
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
			$this->getEntityManager()->persist($user);
			$this->getEntityManager()->flush();
			
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
			}
			
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
	
	public function checkLogin()
	{
		if($this->getRequest()->getSession()->has('_security_secured_area')){
			throw new HttpException(302, null, null, array(
				'Location' => $this->generateUrl('homepage')
			));
		}
	}

}