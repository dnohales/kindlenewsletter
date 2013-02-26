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
	
	private function getItemList($id, $continuation)
	{
		/* @var $greader \Eor\KnlBundle\GoogleReader\Wrapper\ClientService */
		$greader = $this->get('greader_service');

		$subscriptions = $greader->getSubscriptions();
		$stream = $subscriptions->get(urldecode($id));
		if($stream === null){
			throw $this->createNotFoundException();
		}

		$continuation = $continuation === '0'? null:$continuation;
		return $greader->getItemList($stream, \Eor\KnlBundle\GoogleReader\Client::SORT_NEW, 8, null, $continuation);
	}
	
	public function itemListAction($continuation, $id)
	{
		$itemList = $this->getItemList($id, $continuation);
		
		return $this->render('EorKnlBundle:Reader:itemList.html.twig', array(
			'list' => $itemList
		));
	}
	
	public function itemDetailAction($continuation, $id, $itemKey)
	{
		$itemList = $this->getItemList($id, $continuation);
		if($itemList->getItems()->get($itemKey) === null){
			throw $this->createNotFoundException();
		}
		
		return $this->render('EorKnlBundle:Reader:itemDetail.html.twig', array(
			'list' => $itemList,
			'item' => $itemList->getItems()->get($itemKey)
		));
	}
}
