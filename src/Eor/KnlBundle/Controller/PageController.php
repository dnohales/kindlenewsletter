<?php
namespace Eor\KnlBundle\Controller;

/**
 * Description of DefaultController
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class PageController extends Controller
{
	public function contributeAction()
	{
		return $this->render('EorKnlBundle:Page:contribute.html.twig');
	}
}
