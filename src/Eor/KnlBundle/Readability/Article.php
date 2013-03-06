<?php
/*
 * This file is part of the KindleNewsletter.com package.
 * 
 * (c) Damián Nohales <damiannohales@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code. 
 */

namespace Eor\KnlBundle\Readability;

/**
 * Description of Article
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class Article
{
	private $data;
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
    public function getContent()
	{
		return $this->data['content'];
	}

    public function getDomain()
	{
		return $this->data['domain'];
	}

    public function getAuthor()
	{
		return $this->data['author'];
	}

    public function getUrl()
	{
		return $this->data['url'];
	}

    public function getShortUrl()
	{
		return $this->data['short_url'];
	}

    public function getTitle()
	{
		return $this->data['title'];
	}

    public function getExcerpt()
	{
		return $this->data['excerpt'];
	}

    public function getDirection()
	{
		return $this->data['direction'];
	}

    public function getWordCount()
	{
		return $this->data['word_count'];
	}

    public function getTotalPages()
	{
		return $this->data['total_pages'];
	}

    public function getDatePublished()
	{
		return $this->data['date_published'];
	}

    public function getDek()
	{
		return $this->data['dek'];
	}

    public function getLeadImageUrl()
	{
		return $this->data['lead_image_url'];
	}

    public function getNextPageId()
	{
		return $this->data['next_page_id'];
	}

    public function getRenderedPages()
	{
		return $this->data['rendered_pages'];
	}

}