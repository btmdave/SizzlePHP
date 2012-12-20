<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Controller
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Controller;
use Sizzle\Uri\Uri,
    Sizzle\Loader\Loader,
    Sizzle\Pattern\Singleton,
    Sizzle\Helper\Arrays,
    Sizzle\Error\Error;

/**
 * Controller Routing
 *
 * Controller_Router handles all URI routing for the application.  
 * This class is called within the application bootstrap.
 *
 * @category   Sizzle
 * @package    Controller
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Router extends Singleton 
{
	
	/**
	 * Config array
	 * @var array
	 */
	private static $config;
	
	/**
	 * URI Object
	 * @var object
	 */
    private static $uri;
    
    /**
     * Controller
     * @var object 
     */
    private static $controller;
    
    /**
     * Method
     * @var object
     */
    private static $method;

    /**
     * Name of controller being called
     * @var string
     */
    private static $controller_name;
    
    /**
     * Name of method being called
     * @var string
     */
    private static $method_name;    
    
    /**
     * Name of the static_uri segment
     * @var string
     */
    private static $static_uri;    
    
    /**
     * Error Handler
     * @var object
     */
    private static $error;
    
    /**
     * Provides initilization called within __construct of Singleton
     * @see Sizzle\Pattern.Singleton::init()
     */
	public function init(){

		self::$config 			= Loader::loadConfig();
		self::$uri              = new Uri;
		self::$error            = new Error;
	}
	
    /**
     * Run through sequence for handling controller/method routing.
     */	
	public static function run()
	{
		
	    $controller_segment = self::$uri->segment(0);
	    self::$method       = self::$uri->segment(1);
		
	    if(!empty($controller_segment)){
	    
	    	 $path	 		= self::$uri->getPath();
	    	 $segments 		= explode('/', trim($path, '/'));
	    	 $root_dir		= '../private/application/controllers/';
	    	 $dir			= '';
	    	 $dir_prev		= '';
	    	 
	    	 foreach($segments as $key => $segment) {

	    	 	$dir 	  .= ucwords(trim($segment)).'/';
	    	
	    	 	if (is_dir($root_dir.$dir) && Arrays::last($key, $segments)) {
	    	 		
	    	 		$controller_segment 	= self::$uri->segment($key+1);
	    	 		self::$method 	    	= self::$uri->segment($key+2);
	    	 		$controller = (empty($controller_segment)) ? 'IndexController' : ucwords(trim($controller_segment)).'Controller';
	    	 		$file = $root_dir.$dir.$controller.'.php';
	    	 		if (file_exists($file)) {
	    	 			include $file;
	    	 			break;
	    	 		}
	    	 		
	    	 	} elseif (!is_dir($root_dir.$dir)) {
			
	    	 		$controller_segment 	= self::$uri->segment($key);
	    	 		self::$method 	    	= self::$uri->segment($key+1);
	    	 		$controller = (empty($controller_segment)) ? 'IndexController' : ucwords(trim($controller_segment)).'Controller';
	    	 		$file = $root_dir.$dir_prev.$controller.'.php';
	    	 		if (file_exists($file)) {
	    	 			include $file;
	    	 			break;
	    	 		}
	    	 	}
	    	 	
	    	 	$dir_prev 	  .= ucwords(trim($segment)).'/';
	    	 }

	    } else {
	    	
	    	$dir = '../private/application/controllers/'.ucwords(trim($controller_segment));
	    	$controller = (empty($controller_segment)) ? 'IndexController' : ucwords(trim($controller_segment)).'Controller';
	    	$file = '../private/application/controllers/'.$controller.'.php';

	    	if (file_exists($file)) {
	    		include $file;
	    	}
	    }

		/*
		 * Include the controller from within the Application
		 */
		if(class_exists($controller)){
			self::$controller = new $controller();
		} else {
		    //self::$error->setError(404);
		}
		
		return self::getInstance();
	}

	/**
	 * Load is called after self:run().  This will actually load the controller/method and view.
	 * @throws ControllerException
	 */
	public static function load()
	{
		
		if (isset(self::$method) && !empty(self::$method)) {
		    
			if (method_exists(self::$controller, self::$method)) {
				
			    $c = self::$controller;
				$m = self::$method;
				$c->$m();
				
			} else {
			    
			    self::$error->setError(404);
			}
			
		} else {
		    
		    if (method_exists(self::$controller, 'index')) {
		        
			    self::$controller->index();
			    
		    } else {

		        self::$error->setError(404);

			}
			
		}
	}
	
	
	/**
	 * Returns the name of method
	 * @return string name of current method
	 */
	public static function getMethodName() {
	
	    return self::$method_name;
	
	}
	
	/**
	 * Returns the name of controller
	 * @return string name of current controller
	 */
	public static function getControllerName()
	{
	    return self::$controller_name;
	}
	
	/**
	 * Returns the static_uri segment if being used
	 * @return string name of static URI segment if defined
	 */
	public static function getStaticUri()
	{
	    return self::$static_uri;
	}
	
}

