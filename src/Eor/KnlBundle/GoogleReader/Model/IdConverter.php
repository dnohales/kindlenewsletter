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
 * Description of IdConverter
 *
 * @author Damián Nohales <damiannohales@gmail.com>
 */
class IdConverter
{
	public static function convert($id)
	{
		$id = str_replace('http://', '', $id);
		$id = str_replace('/', '-', $id);
		return $id;
	}
}