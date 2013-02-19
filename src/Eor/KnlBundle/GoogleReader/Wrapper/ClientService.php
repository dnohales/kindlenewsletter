<?php
namespace Eor\KnlBundle\GoogleReader\Wrapper;

use Eor\KnlBundle\GoogleReader\Client;

/**
 * This class acts as a wrapper for the original Google Reader client
 * taking in account cache and stuff like that.
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class ClientService
{
	private $client;
	
	public function __construct(Client $client)
	{
		$this->client = $client;
	}
	
	public function getSubscriptions()
	{
		$this->client->getSecurityContext();
	}

}