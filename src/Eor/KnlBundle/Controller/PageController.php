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
