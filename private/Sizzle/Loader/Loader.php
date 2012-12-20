<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Loader
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Loader;

/**
 * Loader
 *
 * Loads our configuration file into an array.
 *
 * @category   Sizzle
 * @package    Loader
 * @author     David Squires <dave@bluetopmedia.com>
 */



class Loader{

    /**
     * Config array
     * @var array
     */
	private static $config;

	/**
	 * Load the config into a static variable and return the config array
	 * @param  string $key
	 * @return array config
	 */
	public static function loadConfig($key = null)
	{
		$config = array();
		
        if(!empty(self::$config)) {
            return self::$config;
        }
		$dir = realpath('../private/application/configs/');
		require_once($dir.DIRECTORY_SEPARATOR.'config.php');
		
		if(isset($config)) {
			self::$config = $config;
		}

        return (isset($key) && isset(self::$config[$key])) ? self::$config[$key] : self::$config;
	}
	
	
	
	public static function get_include_contents($filename) {
		if (is_file($filename)) {
			ob_start();
			include $filename;
			return ob_get_clean();
		}
		return false;
	}
	
	
}
