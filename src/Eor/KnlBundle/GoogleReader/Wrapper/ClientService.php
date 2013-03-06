<?php
/*
 * This file is part of the KindleNewsletter.com package.
 * 
 * (c) Damián Nohales <damiannohales@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code. 
 */

namespace Eor\KnlBundle\GoogleReader\Wrapper;

use Eor\KnlBundle\GoogleReader\Client;
use Eor\KnlBundle\GoogleReader\Model\Index;
use Eor\KnlBundle\GoogleReader\Model\Stream;

/**
 * This class acts as a wrapper for the original Google Reader client
 * taking in account cache and stuff like that.
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class ClientService
{
	const KEY_INDEX = 'greader.index';
	const KEY_FORCE_REFRESH = 'greader.force_refresh';
	
	private $client;
	
	public function __construct(Client $client)
	{
		$this->client = $client;
	}
	
	private function getToken()
	{
		return $this->client->getSecurityContext()->getToken();
	}
	
	public function getSubscriptions($forceRefresh = false)
	{
		$token = $this->getToken();
		$needRefresh = false;
		
		if(!$token->hasAttribute(self::KEY_INDEX) ||
		   !$token->getAttribute(self::KEY_INDEX) instanceof Index ||
		   $forceRefresh || $this->isForceRefreshEnabled()){
			$needRefresh = true;
		} else {
			$updated = $token->getAttribute(self::KEY_INDEX)->getUpdated();
			if($updated !== null){
				$now = new \DateTime('now');
				$needRefresh = $now->getTimestamp() - $updated->getTimestamp() > 3600;
			} else {
				$needRefresh = true;
			}
		}
		
		if($needRefresh){
			$subscriptions = $this->client->getSubscriptions();
			$token->setAttribute(self::KEY_INDEX, $subscriptions);
			$this->disableForceRefresh();
			return $subscriptions;
		} else {
			return $token->getAttribute(self::KEY_INDEX);
		}
	}
	
	public function getItemList(Stream $stream, $sort, $number, $excludeTargets, $continuation, $startTime = null)
	{
		return $this->client->getItemList($stream, $sort, $number, $excludeTargets, $continuation, $startTime);
	}
	
	public function enableForceRefresh()
	{
		$this->getToken()->setAttribute(self::KEY_FORCE_REFRESH, true);
	}
	
	public function disableForceRefresh()
	{
		$this->getToken()->setAttribute(self::KEY_FORCE_REFRESH, false);
	}
	
	public function isForceRefreshEnabled()
	{
		return $this->getToken()->hasAttribute(self::KEY_FORCE_REFRESH) && $this->getToken()->getAttribute(self::KEY_FORCE_REFRESH) == true;
	}

}