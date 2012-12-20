<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataHandler
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataHandler;

use Sizzle\Loader\Loader;

/**
 * 
 * Wrapper for MongoDB native adapter
 * 
 * @category Sizzle
 * @package  DataHandler
 * @author   David Squires <dave@bluetopmedia.com>
 * @uses     Sizzle\Loader
 * @uses     Sizzle\Profiler\Profiler,
 * @uses     \Mongo
 */
class MongoDb {

	/**
	 * Instance of current MongoDb
	 * @var object
	 */
	private static $instance = null;
	
	/**
	 * Mongo object
	 */
	private $mongo;
	
	public function __construct() {
	
		self::$instance =& $this;
	
		if (!empty(self::$instance)) {
			foreach (self::$instance as $key => $class) {
				$this->$key = $class;
			}
		}
	
		$this->_config 			= Loader::loadConfig();
		if (isset($this->_config['mongo'])) {
			
			$this->mongo = new \Mongo($this->_config['mongo'], array("replicaSet" => "dealcent", "timeout" => 100));
		}
	}
	 
	public function connection()
	{
		return $this->mongo;
	}

}