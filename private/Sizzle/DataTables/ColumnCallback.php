<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataTables;

/**
 *  ColumnCallback
 *  Performs a callback from a user-defined function - jQuery Style.  
 *  $value is passed with the field's value.
 *  
 *  <code>
 * 	$column->setCallback(new DataTables\ColumnCallback(function($value) {
 *	    $edit = '<a href="#" data-id="'.$value.'" class="btn small">Edit</a>';
 *	    return $edit;
 *	 }));
 *  </code>
 *  
 *  @category Sizzle
 *  @package  DataTables
 *  @author   David Squires <dave@bluetopmedia.com>
 */
class ColumnCallback {
    
    /**
     * Closure
     * @var function
     */
    protected $closure;
    
    /**
     * Reflection
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * We pass our function to our construct to be invoked.
     * @param function $function
     */
    public function __construct($function)
    {
       $this->closure    = $function;
       $this->reflection = new \ReflectionFunction($function);

    }
    
    /**
     * Magic method to invoke our callback
     * @return return from function
     */
    public function __invoke()
    {
        $args = func_get_args();
        return $this->reflection->invokeArgs($args);
    }    
    
    /**
     * Get the function created 
     * @return \Sizzle\DataTables\function
     */
    public function getClosure()
    {
        return $this->closure;
    }
    

}