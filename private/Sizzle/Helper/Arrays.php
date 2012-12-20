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
 * Arrays
 * 
 * Array helper class.
 * 
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 * @extends    Sizzle\SizzleException
 */
class Arrays {
    
    /**
     * Array
     * @var array
     */
    public static $array = array();
    

    /**
     * Returns element if found in array
     *
     * @param  array  $haystack
     * @param  string $needle
     * @return string
     */
    public static function strstr_array($haystack = array(), $needle)
    {
        if (empty($haystack)) {
            return false;
        }

        foreach($haystack as $element) {
            if(strstr($element, $needle)) {
                return $element;
            }
        }
    }
    
 	/**
 	 * Returns an array mapped from an object
 	 *
 	 * @param object $obj
 	 * @return array
 	 */
 	public static function objectToArray($obj) {
 		if (is_object($obj)) {
 			$obj = get_object_vars($obj);
 		}
  
 		if (is_array($obj)) {
 			return array_map(array(__CLASS__, 'objectToArray'), $obj);
 		}
 		else {
 			return $obj;
 		}
 	}   
    
    /**
     * Returns true if the $current_key is the last item in the array
     * 
     * @param  string $current_key
     * @param  array  $array
     * @return bool
     */
    public static function last($current_key, $array = array())
    {
        if (!empty($array)) {
          
            self::$array = $array;

            $count = count(self::$array);
            $i = 0;
            
            foreach(self::$array as $key => $value) {
                if($i+1 == $count && $key == $current_key) {
                   
                    return true;
                }
                $i++;
            }
        }
        return false;
    }
    
    /**
     * Returns formatted keys from multidimensional array
     *
     * @param     array    $array
     * @param     string   $function
     * @return    array
     */
    public static function formatKeys($array, $function = 'strtolower'){ 
        
        $array_new = array();
        foreach($array as $key => $val) {
            
            $new = call_user_func($function, $key);


            if (!is_array($array[$key])){
        
                $array_new[$new] = $array[$key];
                unset($array[$key]);
                
            }else{
        
                $array_new[$new] = $this->formatKeys($array[$key]);
                unset($array[$key]);
            }
        }
      
        return $array_new;    
    } 
}