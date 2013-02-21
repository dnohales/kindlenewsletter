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
				if (!$this->has($cat['id'])) {
					$category = Factory::createCategory($cat);
					$this->all->set($category->getId(), $category);
					$this->categorized->set($category->getId(), $category);
				} else {
					$category = $this->get($cat['id']);
				}
				$category->addFeed($feed);
			}
		} else {
			$this->categorized->set($feed->getId(), $feed);
		}
		$this->all->set($feed->getId(), $feed);
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

}