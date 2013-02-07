<?php
namespace Eor\KnlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * Description of Controller
 *
 * @author damiannohales
 */
abstract class Controller extends BaseController
{
	/**
	 * 
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->getDoctrine()->getEntityManager();
	}

}