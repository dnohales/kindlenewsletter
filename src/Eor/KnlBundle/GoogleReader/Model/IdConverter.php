<?php
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