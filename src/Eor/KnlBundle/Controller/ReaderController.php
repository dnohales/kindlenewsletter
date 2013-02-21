<?php

namespace Eor\KnlBundle\Controller;

class ReaderController extends Controller
{
    public function indexAction()
    {
		/* @var $greader \Eor\KnlBundle\GoogleReader\Wrapper\ClientService */
		$greader = $this->get('greader_service');
		
        return $this->render('EorKnlBundle:Reader:index.html.twig', array(
			'streams' => $greader->getSubscriptions()
		));
    }
	
	public function categoryFeedListAction($id)
	{
		$id = urldecode($id);
		$category = $this->get('greader_service')->getSubscriptions()->get($id);
		if($category === null){
			throw $this->createNotFoundException();
		}
		
		return $this->render('EorKnlBundle:Reader:categoryFeedList.html.twig', array(
			'category' => $category
		));
	}
	
	public function itemListAction($continuation, $id)
	{
		/* @var $greader \Eor\KnlBundle\GoogleReader\Wrapper\ClientService */
		$greader = $this->get('greader_service');
		
		$cacheDir = $this->get('kernel')->getCacheDir().'/greader';
		@mkdir($cacheDir);
		
		if(file_exists($cacheDir.'/items')){
			$itemList = unserialize(file_get_contents($cacheDir.'/items'));
		} else {
			$subscriptions = $greader->getSubscriptions();
			$stream = $subscriptions->get(urldecode($id));
			if($stream === null){
				throw $this->createNotFoundException();
			}

			$continuation = $continuation == 0? null:$continuation;
			$itemList = $greader->getItemList($stream, \Eor\KnlBundle\GoogleReader\Client::SORT_NEW, 20, null, $continuation);
			file_put_contents($cacheDir.'/items', serialize($itemList));
		}
		
		return $this->render('EorKnlBundle:Reader:itemList.html.twig', array(
			'list' => $itemList
		));
	}
}
