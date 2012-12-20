<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Profiler
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Session;

/**
 * Session
 * Session handler loaded by default with Sizzle/Controller/Controller
 * 
 * @category   Sizzle
 * @package    Session
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Session 
{

    /**
     * Start out session or reference current
     */
	public function __construct() 
	{  
        if(!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION = &$_SESSION;
    }

    /**
     * Magic method to override isset
     * @param string $name
     */
    public function __isset($name)
    {
    	return isset($_SESSION[$name]);
    }
	
    /**
     * Magic method to override unset
     * @param string $name
     */
    public function __unset($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Magic method to get a session value
     * @param string $name
     */
	public function &__get($name)
	{
	    return $_SESSION[$name];
	}
	
	/**
	 * Magic method to add item to session
	 * @param string $name
	 * @param string $val
	 */
	public function __set($name, $val)
	{     
	     $_SESSION[$name] = $val;
	}
	
	/**
	 * Get the full session
	 * @return array
	 */
	public function getSession()
	{
		return (isset($_SESSION)) ? $_SESSION : false;
	}
	
	/**
	 * Get the current session id
	 * @return string
	 */
	public function getSessionId()
	{
		return (isset($_SESSION)) ? session_id() : false;
	}

	/**
	 * Destroy the active session and start a new one
	 */
	public function destroy()
	{
        $_SESSION = array();
        session_destroy();
        session_write_close();
        unset($_SESSION);
        $this->__construct();
	}

}