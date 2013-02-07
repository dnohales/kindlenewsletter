<?php

namespace Eor\KnlBundle\Controller;

class ReaderController extends Controller
{
    public function indexAction()
    {
		var_dump($this->get('security.context')->getToken());
        return new \Symfony\Component\HttpFoundation\Response('');
    }
}
