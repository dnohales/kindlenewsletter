<?php
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
	
	public function addItem(Item $item)
	{
		$this->items->set($item->getId(), $item);
	}
	
	public function getItems()
	{
		return $this->items;
	}
}