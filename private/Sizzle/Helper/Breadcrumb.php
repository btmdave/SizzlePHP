<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Helper;
use Sizzle\Controller\Router;

/**
 * Breadcrumb generator based off domain model
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 * @uses       Sizzle\Controller\Router
 */
class Breadcrumb {
    
    /**
     * Array of item properties in the breadcrumbs
     * @var array
     */
    private $items = array();
    
    /**
     * Breadcrumbs
     * @var array
     */
    private $breadcrumbs = array();

    /**
     * Name of controller
     * @var string
     */
    private $controller;
    
    /**
     * Name of method
     * @var string
     */
    private $method;

    /**
     * setStaticUri
     * @param string $value
     * @return \Sizzle\Helper\Breadcrumb
     */
    public function setStaticUri($value)
    {
        $this->items['static_uri'] = $value;
        return $this;
    }
    
    /**
     * setController
     * @param string $controller
     */
    public function setController($controller = '')
    {
        $this->items['controller'] = $controller;
        return $this;
    }
    
    /**
     * setMethod
     * @param string $method
     * @return \Sizzle\Helper\Breadcrumb
     */
    public function setMethod($method = '')
    {
        $this->items['method'] = $method;
        return $this;
    }
    
    /**
     * setCrumb
     * @param string $label
     * @param string $url
     * @param string $class
     */
    public function setCrumb($label, $url, $class = '')
    {
        $this->items['crumb'][] = array('label' => $label,
                                        'url'   => $url,
                                        'class' => $class);
        
        return $this;
    }

    /**
     * Build our breadcrumb
     */
    public function build()
    {
        $this->breadcrumbs[] = $this->items;
    }
    
    /**
     * getBreadcrumb and assign to view property
     * @return  array
     */
    public function getBreadcrumb()
    {

        foreach($this->breadcrumbs as $key => $item)
        {
            if (isset($item['static_uri'])) {
                if ($item['static_uri'] == Router::getStaticUri()      && 
                    $item['controller'] == Router::getControllerName() &&
                    $item['method']     == Router::getMethodName()) {
                    
                    return $this->breadcrumbs[$key];
                    
                }
            }
            
            if($item['controller'] == Router::getControllerName() && $item['method'] == Router::getMethodName()) {
                
                    return $this->breadcrumbs[$key];
                    
            }
        } 
    }
}