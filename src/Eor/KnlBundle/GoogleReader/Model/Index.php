<?php
namespace Eor\KnlBundle\GoogleReader\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Index
 *
 * @author damiannohales
 */
class Index
{
	private $all;
	private $categorized;
	private $updated;
	
	public function __construct()
	{
		$this->all = new ArrayCollection();
		$this->categorized = new ArrayCollection();
	}
	
	public function addFeedByData(array $feedData)
	{
		$feed = Factory::createFeed($feedData);
		if (count($feedData['categories']) > 0) {
			foreach ($feedData['categories'] as $cat) {
				$catId = Stream::idToKey($cat['id']);
				if (!$this->has($catId)) {
					$category = Factory::createCategory($cat);
					$this->all->set($catId, $category);
					$this->categorized->set($catId, $category);
				} else {
					$category = $this->get($catId);
				}
				$category->addFeed($feed);
			}
		} else {
			$this->categorized->set($feed->getKey(), $feed);
		}
		$this->all->set($feed->getKey(), $feed);
	}
	
	public function addFeed(Stream $feed)
	{
		$this->all->set($feed->getKey(), $feed);
		$this->categorized->set($feed->getKey(), $feed);
	}
	
	public function has($id)
	{
		return $this->all->containsKey($id);
	}
	
	/**
	 * 
	 * @param string $id
	 * @return Stream
	 */
	public function get($id)
	{
		return $this->all->get($id);
	}
	
	/**
	 * 
	 * @return ArrayCollection
	 */
	public function getAll()
	{
		return $this->all;
	}
	
	/**
	 * 
	 * @return ArrayCollection
	 */
	public function getCategorized()
	{
		return $this->categorized;
	}
	
	public function getUpdated()
	{
		return $this->updated;
	}

	public function setUpdated($updated)
	{
		$this->updated = $updated;
	}

}