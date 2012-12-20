<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    AutoLoader
 * @author     David Squires <dave@bluetopmedia.com>
 */


/**
 * AutoLoader
 * 
 * @category   Sizzle
 * @package    AutoLoader
 * @author     David Squires <dave@bluetopmedia.com>
 */
class AutoLoader {
    
    /**
     * Register AutoLoaders 
     */
    public function __construct() {
        
        spl_autoload_register(array($this, 'autoloadApplication'));
        spl_autoload_register(array($this, 'autoloadSizzle'));
        
    }
    
    /**
     * Used for lazy loading of Sizzle namespace classes.
     * @param string $class_name
     */
    private function autoloadSizzle($class_name) {

        $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . $class_name.".php";
        if(file_exists($file)) {
            require($file);
        }
         
    }
    /**
     * Used for lazy loading of application classes.
     * @param string $class_name
     */
    private function autoloadApplication($class_name) {

    	$class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
    	$file = dirname(__FILE__).'/../application/'.$class_name.".php";
        if(file_exists($file)) {
            require($file);
        }
        
    }
}

$obj = new AutoLoader();

