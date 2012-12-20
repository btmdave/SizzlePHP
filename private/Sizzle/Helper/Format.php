<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Helper;

/**
 * Format
 * Format utility class
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Format
{

	/**
	 * toSerialized
	 * @param  array $data to serialize
	 * @return string serialized 
	 */
	public static function toSerialized($data)
	{
		return serialize($data);
	}
	
	/**
	 * Accepts an int and return as decimal format
	 * @param int 
	 * @param int $x = 1000 (division by)
	 * @param int $decimals = 2
	 * @return decimal
	 */
	public static function int_to_decimal($num, $x = 1000, $decimals = 2)
	{
	       return number_format(($num / $x), $decimals);
	}
	
	/**
	 * Accepts a decimal and return as int with trailing zero as default (x 1000)
	 * @param  int $num
	 * @param  int $x (multiplier)
	 * @return int
	 */
	public static function decimal_to_int($num, $x = 1000)
	{
        return ($num * $x);
	}
	
}

