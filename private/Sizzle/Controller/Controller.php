<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Controller
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Controller;

use Sizzle\Controller\Request,
    Sizzle\Controller\ControllerException,
    Sizzle\Uri\Uri,
    Sizzle\View\View,
    Sizzle\Session\Session,
    Sizzle\DataHandler\MongoDb,
    Sizzle\Profiler\Profiler;

/**
 * The Controller is the backbone of the application.  All controllers within an application
 * must extend this class.  All default classes that are defined in the bootstrap or within this class
 * will be accessible.  Any classes defined within the application's controller, will be available
 * for that instance only.
 *
 * @category Sizzle
 * @package  Controller
 * @author   David Squires <dave@bluetopmedia.com>
 */
abstract class Controller 
{
    
    /**
     * Holds the current instance of the controller
     * @var static object of the active controller instance
     */
	private static $instance;
	
	/**
	 * Contains an array of all loaded objects
	 * @var array 
	 */
	private static $loaded;
	
	/**
	 * Contains an array of all methods to be called
	 * @var array
	 */
	private static $method_call;
	
	/**
	 * Construct the controller and load all default classes to be inherited.
	 */
	public function __construct()
	{
	    self::$instance =& $this;

	    //By default, all applications will have access to these objects.
	    //Additional defaults, can be defined in the boostrap using setDefaultObject()
	    $this->Session  = new Session;
        $this->View     = new View;
        $this->Uri      = new Uri;
        $this->Request  = new Request;
        $this->Profiler = Profiler::getInstance();
        
        $this->MongoDb  = new MongoDb;
        $this->Mongo    = $this->MongoDb->connection();

        //Initilize statically defined objects from setDefaultObject
        if(!empty(self::$loaded)) {
            foreach(self::$loaded as $key => $class) {
                $this->$key = $class;
            }
            
            //Call any methods related to statically defined objects from callObjectMethod
            if(!empty(self::$method_call)){
                foreach(self::$method_call as $obj => $method) {
                    $this->$obj->$method();
                }
            }
        }
	}

	/**
	 * Add a default object to the controller instance.
	 * Typically this is used in the application Bootstrap.
	 * @param object $obj  
	 */
	public static function setDefaultObject($obj)
	{
	    $reflect      = new \ReflectionClass($obj);
	    $name         = $reflect->getShortName();
        self::$loaded[$name] = new $obj;
	}

    /**
     * Call a method of an object we insantiate in the bootstrap using setDefaultObject.
     * The boostrap is not a controller.  We do have not have access to objects inherited in the controller.
     * We can however, use this to call init() or other various methods within pre-loaded classes.
     * 
     * @param string $obj_name
     * @param string $method_name
     */
	public static function callObjectMethod($obj_name, $method_name)
	{
	    self::$method_call[$obj_name] = $method_name;
	}
	
	/**
	 * Returns the instance of the current object
	 * @return \Sizzle\Controller
	 */
	public static function getInstance()
	{
         return self::$instance;
	}
	
	/**
	 * Overloading getter to retreive inherited objects
	 * @param string    $name
	 */
	public function __get($name)
	{
	     return $this->$name;
	}
	
	/**
	 * Overloading setter to set objects to inherit
	 * @param  string    $name
	 * @param  string    $value
	 */
	public  function __set($name, $value)
	{
	    $this->$name = $value;
	}	

}