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

/**
 * Description of Item
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class Item
{
	private $id;
	
	private $title;
	
	private $published;
	
	private $updated;
	
	private $link;
	
	private $author;
	
	private $content;
	
	private $normalizedContent;
	
	private $isStarred;
	
	private $isReaded;
	
	private $originId;
	
	private $originTitle;
	
	private $originUrl;
	
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
		$id = str_replace('tag:google.com,2005:reader/item/', '', $id);
		$id = str_replace('/', '-', $id);
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

	public function getPublished()
	{
		return $this->published;
	}

	public function setPublished($published)
	{
		$this->published = $published;
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	public function setUpdated($updated)
	{
		$this->updated = $updated;
	}

	public function getLink()
	{
		return $this->link;
	}

	public function setLink($link)
	{
		$this->link = $link;
	}

	public function getAuthor()
	{
		return $this->author;
	}

	public function setAuthor($author)
	{
		$this->author = $author;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function isSummarized()
	{
		return strlen($this->content) < 512;
	}

	public function getNormalizedContent()
	{
		return $this->normalizedContent;
	}

	public function setNormalizedContent($normalizedContent)
	{
		$this->normalizedContent = $normalizedContent;
	}
	
	public function isStarred()
	{
		return $this->isStarred;
	}

	public function setIsStarred($isStarred)
	{
		$this->isStarred = $isStarred;
	}
	
	public function isReaded()
	{
		return $this->isReaded;
	}

	public function setIsReaded($isReaded)
	{
		$this->isReaded = $isReaded;
	}
	
	public function getOriginId()
	{
		return $this->originId;
	}

	public function setOriginId($originId)
	{
		$this->originId = $originId;
	}

	public function getOriginTitle()
	{
		return $this->originTitle;
	}

	public function setOriginTitle($originTitle)
	{
		$this->originTitle = $originTitle;
	}

	public function getOriginUrl()
	{
		return $this->originUrl;
	}

	public function setOriginUrl($originUrl)
	{
		$this->originUrl = $originUrl;
	}

}