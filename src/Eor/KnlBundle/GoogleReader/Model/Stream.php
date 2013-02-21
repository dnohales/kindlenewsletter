<?php
namespace Eor\KnlBundle\GoogleReader\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Stream
 *
 * @author damiannohales
 */
class Stream
{
	const ICON_CATEGORY = 1;
	const ICON_FEED = 2;
	const ICON_NONE = 3;
	const ICON_STAR = 4;
	
	private $id;
	
	private $title;
	
	private $sortId;
	
	private $count;
	
	private $newestItemTimestampUsec;
	
	private $htmlUrl;
	
	private $iconType;
	
	private $feeds;
	
	public function __construct()
	{
		$this->feeds = new ArrayCollection();
	}
	
	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public static function idToKey($id)
	{
		$id = str_replace('http://', '', $id);
		$id = str_replace('/', '-', $id);
		$id = trim($id, '-');
		return $id;
	}
	
	public function getKey()
	{
		return self::idToKey($this->getId());
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getSortId()
	{
		return $this->sortId;
	}

	public function setSortId($sortId)
	{
		$this->sortId = $sortId;
	}

	public function getCount()
	{
		return $this->count;
	}

	public function setCount($count)
	{
		$this->count = $count;
	}

	public function getNewestItemTimestampUsec()
	{
		return $this->newestItemTimestampUsec;
	}

	public function setNewestItemTimestampUsec($newestItemTimestampUsec)
	{
		$this->newestItemTimestampUsec = $newestItemTimestampUsec;
	}
	
	public function getHtmlUrl()
	{
		return $this->htmlUrl;
	}

	public function setHtmlUrl($htmlUrl)
	{
		$this->htmlUrl = $htmlUrl;
	}
	
	public function getIconType()
	{
		return $this->iconType;
	}

	public function setIconType($iconType)
	{
		$this->iconType = $iconType;
	}
	
	public function getFeeds()
	{
		return $this->feeds;
	}

	public function addFeed(Stream $feed)
	{
		$this->feeds->add($feed);
	}
	
	public function removeFeed(Stream $feed)
	{
		$this->feeds->removeElement($feed);
	}

	public function isLeaf()
	{
		return $this->feeds->count() === 0;
	}
	
}
