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
 * Description of Stream
 *
 * @author Damián Nohales <damiannohales@gmail.com>
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
	
	private $isMultipleSource;
	
	private $feeds;
	
	private $categories;
	
	public function __construct()
	{
		$this->feeds = new ArrayCollection();
		$this->categories = new ArrayCollection();
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
		$id = urlencode($id);
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
	
	public function isMultipleSource()
	{
		return $this->isMultipleSource;
	}

	public function setIsMultipleSource($isMultipleSource)
	{
		$this->isMultipleSource = $isMultipleSource;
	}
	
	public function getFeeds()
	{
		return $this->feeds;
	}

	public function addFeed(Stream $feed)
	{
		$this->feeds->add($feed);
		$feed->addCategory($this);
	}
	
	public function getCategories()
	{
		return $this->categories;
	}
	
	public function addCategory(Stream $category)
	{
		$this->categories->add($category);
	}

	public function isLeaf()
	{
		return $this->feeds->count() === 0;
	}
	
}
