<?php
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
	
	private $client;
	
	public function __construct(Client $client)
	{
		$this->client = $client;
	}
	
	public function getSubscriptions($refresh = false)
	{
		$token = $this->client->getSecurityContext()->getToken();
		
		if(!$token->hasAttribute(self::KEY_INDEX) || !$token->getAttribute(self::KEY_INDEX) instanceof Index || $refresh){
			$subscriptions = $this->client->getSubscriptions();
			$token->setAttribute(self::KEY_INDEX, $subscriptions);
			return $subscriptions;
		} else {
			return $token->getAttribute(self::KEY_INDEX);
		}
	}
	
	public function getItemList(Stream $stream, $sort, $number, $excludeTargets, $continuation, $startTime = null)
	{
		return $this->client->getItemList($stream, $sort, $number, $excludeTargets, $continuation, $startTime);
	}

}