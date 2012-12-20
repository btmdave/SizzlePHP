<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Cache
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Cache;

use Sizzle\Loader\Loader;

/**
 * 
 * Memcache wrapper for PHP's memcache object.
 * 
 * @category Sizzle
 * @package  Cache
 * @author   David Squires <dave@bluetopmedia.com>
 * @uses     Sizzle\Loader
 * @uses     \Memcache
 */


class Memcache {
	
	private $_config;
	
	private $_memcache;
	
	/**
	 * Instance of current memcache
	 * @var object
	 */
	private static $instance = null;
	
	public function __construct() {
	
		self::$instance =& $this;
	
		if (!empty(self::$instance)) {
			foreach (self::$instance as $key => $class) {
				$this->$key = $class;
			}
		}
		
		$this->connect();
	}
	
	/**
	 * Initilize our memcache connection(s)
	 */
	protected function connect()
	{
		$this->_config 			= Loader::loadConfig();
		
		if (isset($this->_config['memcache'])) {
			$this->_memcache = new \Memcache;
			foreach($this->_config['memcache'] as $key => $server) {
				if($this->_memcache->addServer((string)$server['host'], (int)$server['port'])) {
					$connect_status[$key] = true;
				}
			}
			
			foreach($connect_status as $status) {
				if(!$status) {
					return false;
				}
			}
			
			return true;
		}
	}
	
	/**
	 * Set item in memcache
	 * 
	 * @param string  $key
	 * @param string  $value
	 * @param int     $expires
	 * @param int 	  $compress
	 */
	public function set($key, $value, $expires = 0, $compress = 0) {
		$this->_memcache->set($key, $value, (int)$compress, (int)$expires);
	}
	
	/**
	 * Return item from memcache if it exists
	 * @param string $key
	 */
	public function get($key) {
		$res = $this->_memcache->get($key);
		if($res) {
			return $res;
		}
		return false;
	}
	
	/**
	 * Delete item from cache
	 * @param string $key
	 */
	public function delete($key) {
		$this->_memcache->delete($key);
	}
	
}