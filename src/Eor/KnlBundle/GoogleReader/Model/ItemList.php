<?php
/*
 * This file is part of the KindleNewsletter.com package.
 * 
 * (c) Damián Nohales <damiannohales@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code. 
 */

namespace Eor\KnlBundle\GoogleReader\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ItemList
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class ItemList
{
	private $stream;
	
	private $continuation;
	
	private $currentContinuation;
	
	private $items;
	
	public function __construct(Stream $stream)
	{
		$this->items = new ArrayCollection();
		$this->stream = $stream;
	}
	
	public function getStream()
	{
		return $this->stream;
	}
	
	public function getContinuation()
	{
		return $this->continuation;
	}

	public function setContinuation($continuation)
	{
		$this->continuation = $continuation;
	}
	
	public function getCurrentContinuation()
	{
		return $this->currentContinuation;
	}

	public function setCurrentContinuation($currentContinuation)
	{
		$this->currentContinuation = $currentContinuation;
	}
	
	public function addItem(Item $item)
	{
		$this->items->set($item->getKey(), $item);
	}
	
	public function getItems()
	{
		return $this->items;
	}
}