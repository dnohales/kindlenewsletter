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

use Symfony\Component\HttpFoundation\Response;
use Eor\KnlBundle\GoogleReader\Client;
use Eor\KnlBundle\Readability\Client as ReadabilityClient;
use Eor\KnlBundle\Readability\ReadabilityApiException;

class ReaderController extends Controller
{
    public function indexAction()
    {
		/* @var $greader \Eor\KnlBundle\GoogleReader\Wrapper\ClientService */
		$greader = $this->get('greader_service');
		
        return $this->render('EorKnlBundle:Reader:index.html.twig', array(
			'subscriptions' => $greader->getSubscriptions()
		));
    }
	
	public function categoryFeedListAction($id)
	{
		$category = $this->get('greader_service')->getSubscriptions()->get($id);
		if($category === null){
			throw $this->createNotFoundException();
		}
		
		return $this->render('EorKnlBundle:Reader:categoryFeedList.html.twig', array(
			'category' => $category,
			'up_url' => $this->generateUrl('homepage')
		));
	}
	
	private function getItemList($id, $continuation)
	{
		/* @var $greader \Eor\KnlBundle\GoogleReader\Wrapper\ClientService */
		$greader = $this->get('greader_service');

		$subscriptions = $greader->getSubscriptions();
		$stream = $subscriptions->get($id);
		if($stream === null){
			throw $this->createNotFoundException();
		}

		$continuation = $continuation === '0'? null:$continuation;
		return $greader->getItemList($stream, \Eor\KnlBundle\GoogleReader\Client::SORT_NEW, 8, null, $continuation);
	}
	
	public function itemListAction($continuation, $id)
	{
		$itemList = $this->getItemList($id, $continuation);
		
		$firstCategory = $itemList->getStream()->getCategories()->first();
		if($firstCategory !== false){
			$upUrl = $this->generateUrl('category_feed_list', array(
				'id' => $firstCategory->getKey()
			));
		} else {
			$upUrl = $this->generateUrl('homepage');
		}
		
		return $this->render('EorKnlBundle:Reader:itemList.html.twig', array(
			'list' => $itemList,
			'up_url' => $upUrl
		));
	}
	
	public function itemDetailAction($continuation, $id, $itemKey)
	{
		$itemList = $this->getItemList($id, $continuation);
		$item = $itemList->getItems()->get($itemKey);
		if($item === null){
			throw $this->createNotFoundException();
		}
		
		/* @var $greader Client */
		$greader = $this->get('greader_client');
		$greader->setState($item->getId(), $item->getOriginId(), Client::STATE_READ, true);
		$item->setIsReaded(true);
		
		try {
			/* @var $readabilityClient ReadabilityClient */
			$readabilityClient = $this->get('readability_client');
			if($readabilityClient->isAvailable() && $item->isSummarized()){
				$item->setContent($readabilityClient->parse($item->getLink())->getContent());
			}
		} catch (ReadabilityApiException $e) {
			// Use the Google Reader content if Readability fails
		}
		
		return $this->render('EorKnlBundle:Reader:itemDetail.html.twig', array(
			'list' => $itemList,
			'item' => $item,
			'up_url' => $this->generateUrl('item_list', array(
				'id' => $id,
				'continuation' => $continuation
			))
		));
	}
	
	public function forceRefreshAction()
	{
		/* @var $greader \Eor\KnlBundle\GoogleReader\Wrapper\ClientService */
		$greader = $this->get('greader_service');
		$greader->enableForceRefresh();
		
		return new Response();
	}
	
	public function setStateAction()
	{
		$r = $this->getRequest();
		$itemId = $r->request->get('item_id');
		$originId = $r->request->get('origin_id');
		$state = $r->request->get('state');
		$set = $r->request->get('set');
		
		if(!isset($itemId, $originId, $state, $set) || !$r->isXmlHttpRequest()){
			return new Response('Bad request', 400);
		}
		
		/* @var $greader Client */
		$greader = $this->get('greader_client');
		$greader->setState($itemId, $originId, $state, $set == 1? true:false);
		
		return new Response('OK');
	}
}
