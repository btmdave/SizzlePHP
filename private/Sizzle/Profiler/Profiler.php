<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Profiler
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Profiler;
use Sizzle\Pattern\Singleton;

/**
 * Profiler
 * 
 *  Example:
 *  <code>
 *   $this->Profiler->enable();
 *  </code>
 *  
 *  This will output all executed SQL queries on a given page.
 *  @TODO This should be extended to incorporate stack traces, load times at class level, and memory usage.
 *
 * @category   Sizzle
 * @package    Profiler
 * @author     David Squires <dave@bluetopmedia.com>
 * @extends    Singleton
 */

class Profiler extends Singleton
{

    /**
     * Profile data
     * @var array
     */
    public  $_profile;

    /**
     * This is called in our Singlton construct
     */
    protected function init() {
        
        if(empty($this->_profile)) {
            $this->_profile = array();
        }
        
    }
    
    /**
     * We use this in our Db object anytime SQL is about to be executed.
     * @param string $sql
     */
    public function setSql($sql)
    {
        $this->_profile = array_merge($this->_profile, array(array('sql' => $sql)));
    }
    
    /**
     * Start the profiling time
     */
    public function start()
    {
        $last = array_pop(array_keys($this->_profile));
        $this->_profile[$last]['start'] = microtime(true);
    }
    
    /**
     * Stop the profiling time and set the total lapse time
     */
    public function stop()
    {
        $last = array_pop(array_keys($this->_profile));
        $this->_profile[$last]['stop'] = microtime(true);
        $this->_profile[$last]['time'] = number_format($this->_profile[$last]['stop'] - $this->_profile[$last]['start'], 5);
    }    
    
    /**
     * When using prepared statements, we'll keep a record of the actual values prior to execution.
     * @param string $field
     * @param string $value
     */
    public function setParams($field, $value)
    {
        $this->_profile = array_merge($this->_profile, array(array($field => $value)));
    }
    
    /**
     * Outputs the profiler to the browser
     * Includes the css and phtml for the profiler
     */
    public function enable()
    {
        $this->css = file_get_contents(dirname(__FILE__).'/profiler.css');
        include_once(dirname(__FILE__).'/profiler.phtml');
    }
    
}