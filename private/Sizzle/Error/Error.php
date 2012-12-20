<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Error
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Error;

/**
 * HTTP Error Handling
 *
 * @category   Sizzle
 * @package    Error
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Error {

    /**
     * Content of error message
     * @var content
     */
    private $content;
    
    /**
     * Error code thrown
     * @var int
     */
    private $code;
    
    /**
     * Sets the error to be handled.  Uses the Error/Views folder with Default template 
     * and {HTTPCode}.phtml (404.phtml) to display the message.
     * 
     * call $this->setError('')
     *
     * @param  int code
     * @return void
     */
    public function setError($code = 404)
    {
   		$this->code = $code;
        $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        
        //Log the error message
        $log = 'Error: '.$this->code.' on '.$url;
        error_log($log, 0);
        
        $this->content['title'] = $this->code;
        
        $this->applicationError();
        
        $this->sizzleError();

    }
    
    /**
     * Application-level error messaging.
     * Sizzle has framework-level handling of error handling (404).  Each app can customize this
     * by adding to their application /errors/404.phtml and /errors/Default.phtml
     * We first check if there are application-level errors defined and use Sizzle as a fallback.
     */
    private function applicationError()
    {
    	$view = dirname(__FILE__).'/../../application/errors/'.$this->code.'.phtml';
    	if (file_exists($view)){
    	
    		ob_start();
    		include $view;
    		$this->content['message'] = ob_get_contents();
    		ob_end_clean();
    	}
    	 
    	$default = dirname(__FILE__).'/../../application/errors/Default.phtml';
    	if (file_exists($default)){
    		include $default;
    		exit();
    	}
    }
    
    /**
     * Sizzle framework-level error messaging.
     */
    private function sizzleError()
    {
    	
    	$view = dirname(__FILE__).'/Views/'.$this->code.'.phtml';
    	
    	if (file_exists($view)){
    		ob_start();
    		include $view;
    		$this->content['message'] = ob_get_contents();
    		ob_end_clean();
    	}
    	
    	$default = dirname(__FILE__).'/Views/Default.phtml';
    	
    	if (file_exists($default)){
    		include $default;
    		exit();
    	}
    }

}