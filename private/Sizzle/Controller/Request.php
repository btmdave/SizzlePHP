<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Controller
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Controller;

/**
 * Controller Request handling
 * @category   Sizzle
 * @package    Controller
 * @author   David Squires <dave@bluetopmedia.com>
 */

class Request {

    /**
     * Contains POST or GET variables from a request
     * @var array
     */
	private $_vars = array();

	/**
	 * Checks if there are query string parameters and adds variables to $_vars
	 * @return boolean
	 */
	public function isGet() {
	    
	    if(isset($_GET) && !empty($_GET)){
	    
	        foreach($_GET as $key => $item){
	            $this->_vars[$key] = $item;
	        }
	    
	        return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Checks if the page is being posted to and adds variables to $_vars
	 * @return boolean
	 */
	public function isPost()
	{
		if(isset($_POST) && !empty($_POST)){
			
			foreach($_POST as $key => $item){
				$this->_vars[$key] = $item;
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks if the page is being requested via AJAX or not.
	 * @return boolean
	 */
	public function isAjax()
	{
	    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

	        return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Returns all post or get variables
	 * @param array $var
	 */
	public function get($var = '')
	{
		return $this->_vars;
	}
	
	/**
	 * Magic method to return specific post or get variable
	 * @param string $key
	 */
    public function __get($key) 
    {
        if(array_key_exists($key, $this->_vars)) {
            return $this->_vars[$key];
        }
    }
    
    /**
     * Magic method to override isset 
     * @param string $key
     */
    public function __isset($key)
    {
        if(array_key_exists($key, $this->_vars)) {
            return true;
        }   
        
        return false;
    }
 	
}