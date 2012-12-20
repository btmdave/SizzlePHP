<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Session
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Session;

/**
 * Cookie
 * Cookie handler 
 * Cookies are set by default to expire in 30 days.
 *  
 * <code> 
 *   $cookie = new Cookie;
 *   $cookie->set('name', 'value');
 *   $name = $cookie->get('name'); 
 * </code> 
 * 
 * @category   Sizzle
 * @package    Session
 * @subpackage Cookie
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Cookie 
{
    
    /**
     * Sets a cookie
     * @param string $name
     * @param string $value
     * @param string $expires
     */
    public function set($name, $value, $expires = false)
    {
        if(!$expires) {
            $expires = time()+60*60*24*30;
        }
    	setcookie($name, $value, $expires);
    }
	
    /**
     * Gets a cookie
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
    }
    
    /**
     * Deletes a cookie
     * @param string $name
     */
    public function delete($name)
    {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', time() - 3600);
        }
    }

}