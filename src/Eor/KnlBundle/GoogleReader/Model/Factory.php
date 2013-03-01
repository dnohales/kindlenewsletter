<?php
namespace Eor\KnlBundle\GoogleReader\Model;

use Eor\KnlBundle\GoogleReader\Client;

/**
 * Create Streams against Google raw data
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class Factory
{
	private static function getKey(array $a, $k, $default = null)
	{
		return isset($a[$k])? $a[$k]:$default;
	}
	
	public static function createFeed(array $data)
	{
		$feed = new Stream();
		$feed->setIconType(Stream::ICON_FEED);
		$feed->setId(self::getKey($data, 'id'));
		$feed->setTitle(self::getKey($data, 'title'));
		$feed->setSortId(self::getKey($data, 'sortid'));
		$feed->setHtmlUrl(self::getKey($data, 'htmlUrl'));
		$feed->setIsMultipleSource(false);
		
		return $feed;
	}
	
	public static function createCategory(array $data)
	{
		$category = new Stream();
		$category->setIconType(Stream::ICON_CATEGORY);
		$category->setId(self::getKey($data, 'id'));
		$category->setTitle(self::getKey($data, 'label'));
		$category->setIsMultipleSource(true);
		
		return $category;
	}
	
	public static function createItem($data)
	{
		$item = new Item();
		
		$item->setId(self::getKey($data, 'id'));
		$item->setTitle(self::getKey($data, 'title'));
		$item->setPublished(new \DateTime('@'.self::getKey($data, 'published')));
		$item->setUpdated(new \DateTime('@'.self::getKey($data, 'updated')));
		
		$alternate = self::getKey($data, 'alternate');
		if($alternate !== null){
			$item->setLink(self::getKey(
				self::getKey($alternate, 0, array()),
				'href'
			));
		} else {
			$item->setLink(self::getKey(
				self::getKey($data, 'canonical', array()),
				'href'
			));
		}
		$item->setAuthor(self::getKey($data, 'author'));
		
		if(self::getKey($data, 'content') !== null){
			$item->setContent(self::getKey(
				self::getKey($data, 'content', array()),
				'content'
			));
		} else {
			$item->setContent(self::getKey(
				self::getKey($data, 'summary', array()),
				'content'
			));
		}
		
		$categories = self::getKey($data, 'categories', array());
		array_walk($categories, function(&$e, $k){
			$e = preg_replace('&/\d+/&', '/-/', $e);
		});
		$item->setIsStarred(in_array(Client::STATE_STAR, $categories, true));
		$item->setIsReaded(in_array(Client::STATE_READ, $categories, true));
		
		$origin = self::getKey($data, 'origin', array());
		$item->setOriginId(self::getKey($origin, 'streamId'));
		$item->setOriginTitle(self::getKey($origin, 'title'));
		$item->setOriginUrl(self::getKey($origin, 'htmlUrl'));
		
		return $item;
	}
	
	public static function createItemList(Stream $stream, $data, $currentContinuation)
	{
		$list = new ItemList($stream);
		
		$list->setContinuation(self::getKey($data, 'continuation'));
		$list->setCurrentContinuation($currentContinuation);
		
		foreach (self::getKey($data, 'items', array()) as $i) {
			$list->addItem(self::createItem($i));
		}
		
		return $list;
	}
	
	public static function createGlobalUnreadFeed()
	{
		$feed = new Stream();
		$feed->setIconType(Stream::ICON_NONE);
		$feed->setId(Client::STATE_READING_LIST);
		$feed->setTitle('All items');
		$feed->setIsMultipleSource(true);
		
		return $feed;
	}
	
	public static function createStarredFeed()
	{
		$feed = new Stream();
		$feed->setIconType(Stream::ICON_STAR);
		$feed->setId(Client::STATE_STAR);
		$feed->setTitle('Starred items');
		$feed->setIsMultipleSource(true);
		
		return $feed;
	}
}