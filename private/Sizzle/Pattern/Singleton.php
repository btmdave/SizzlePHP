<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Pattern
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Pattern;

/**
 * Abstract class for Singlton pattern.
 * Singlton pattern only allows the instantiation of an object one time.
 * Instead of calling a new Object, we inherit this class and use the static getInstance()
 * 
 * <code>
 * $object = InheritedSingltonClass::getInstance();
 * </code>
 * 
 * @category   Sizzle
 * @package    Pattern
 * @author     David Squires <dave@bluetopmedia.com>
 * @abstract
 */
abstract class Singleton {

    /**
     * Contains all singleton objects
     * @var array<Singlton>
     */
	protected static $_singletonInstance = array();

    /**
     * Calls init function.  Inherited classes can define their own init.
     */
	public final function __construct() {
		$this->init();
	}

	/**
	 * Called within __construct(), override if neccessary.
	 */	
	protected function init() { }


    /**
     * Cloning not allowed
     * @throws PatternException
     */
	protected final function __clone() { 
	    throw new PatternException('Cloning singletons is not allowed');
	}

    /**
     * Serilizing not allowed
     * @throws PatternException
     */
	public final function __sleep() {
		throw new PatternException('Serializing singletons is not allowed');
	}

	/**
	 * Unserializing not allowed
	 * @throws PatternException
	 */
	public final function __wakeup() {
	    throw new PatternException('Unserializing singletons is not allowed');
	}
	
	/**
	 * Returns an unique instance of current child class.
	 * @return	Singlton
	 */
	public static final function getInstance() {
		$className = get_called_class();
		if (!isset(self::$_singletonInstance[$className])) {
			self::$_singletonInstance[$className] = new $className();
		}

		return self::$_singletonInstance[$className];
	}
}