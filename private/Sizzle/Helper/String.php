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
 * String Helper
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */
class String {

    /**
     * 
     * Shortens a string to a specific length and adds optional tail
     * 
     * @param string
     * @param length
     * @param tail 
     * @param word_sensitive - don't allow broken words
     * @return string
     */
    public function shorten($string, $length, $tail = '', $word_sensitive = true)
    {

        if(strlen($string) > $length){
            
            if($word_sensitive) {
                $string = substr($string, 0, strpos(wordwrap($string, $length), "\n"));
            }

            $string = substr($string, 0, $length).$tail;
        } 
        
        return $string;
    }
    
    /**
     * 
     */
    public static function blank($value)
    {
    	return empty($value) && !is_numeric($value);
    }
}