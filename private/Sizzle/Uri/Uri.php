<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Profiler
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Uri;
use Sizzle\SizzleException,
    Sizzle\Loader\Loader;

/**
 * URI
 *
 * This class is used to obtain URI items and enforce sanity requirements
 *
 * @category   Sizzle
 * @package    Uri
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Uri {
    
    /**
     * Config from config.php
     *
     * @var array
     */
    private $_config;
    
    /**
     * Full URL string - REQUEST_URI
     *
     * @var string
     */
    private $_uri;
    
    /**
     * Query string after cleaning
     *
     * @var string
     */
    private $_query_string;
    
    /**
     * Array of each URI segment.
     *
     * @var string
     */
    private $_array_uri = array();
    
    /**
     * Characters allowed in the URI. All others will be stripped.
     * @var string
     */
    private $allowed_uri_characters = 'a-z 0-9~%.:_\-';
    
    /**
     * Load our configs and parse our URI
     */
    public function __construct() {
        
        $this->_uri 			= strtolower($_SERVER['REQUEST_URI']);
        $this->_query_string 	= $this->_clean_uri(strtolower($_SERVER['QUERY_STRING']));
        $this->request();
    }
    
    /**
     * Build the _array_uri
     * @return _array_uri
     */
    public function request()
    {
        $path = preg_split('[\\/]', strtolower($this->_uri), -1, PREG_SPLIT_NO_EMPTY);
        $arrgs = array();
        foreach($path as $uri) {
    
            $str = str_replace($this->_query_string, '', $uri);
            $str = str_replace('?', '', $str);
            $arrgs[] = $str;
    
        }

        parse_str($this->_query_string, $parms);
        foreach($parms as $name => $item){
            $arrgs[$name] = $item;
        }
     
        return $this->_array_uri = $arrgs;
    }
    
    /**
     * Get the current URI string
     * @return string
     */
    public function get()
    {
        return $this->_uri;
    }
    
    /**
     * Alias for PATH_INFO
     * @return string
     */
    public function getPath()
    {
    	return (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : false;
    }
    
    /**
     * Get the referral URI string
     */
    public function getReferer()
    {
    	return (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '/';
    }
    
    /**
     * Get segment from URI by key.  This can be numeric based on index or by name if using query string
     * @param  int $index
     * @return string|false
     */
    public function segment($index)
    {
    	return isset($this->_array_uri[$index]) ? $this->_array_uri[$index] : false;
    }
    
    /**
     * Redirect to a new URL
     * @param string $url
     * @return void
     */
    public function redirect($url)
    {
        Header("Location: ".$url);
        exit();
    }   
    
    /**
     * Remove invalid characters and sanitize URI.  Uses config setting "allowed_uri_characters".
     * @param  string $str
     * @return string cleaned string after removing any disallowed characters
     */
    private function _clean_uri($str)
    {
        if ($str != '' && $this->allowed_uri_characters != '' && $this->allowed_uri_characters == false) {
            if (!preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($this->allowed_uri_characters, '-'))."]+$|i", $str)) {
                throw new SizzleException('The URI you submitted has disallowed characters');
            }
        }
        $bad	= array('$',		'(',		')',		'%28',		'%29');
        $good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');
    
        return str_replace($bad, $good, $str);
    }
}