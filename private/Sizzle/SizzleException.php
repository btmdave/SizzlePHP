<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Controller
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle;

/**
* SizzleException
*
* SizzleException extends PHP's base Exception class.  All methods are available for PHP 5.3.0+.  
* This class should be extended by a child class that represents the exception usage.
* 
* <code>
* use Sizzle\SizzleException;
* class ControllerException extends SizzleException {}
* throw new ControllerException('Message');
* </code>
*
* @category    Sizzle
* @package     SizzleException
* @author      David Squires <dave@bluetopmedia.com>
* @extends     Exception
*/
abstract class SizzleException extends \Exception
{

    /**
     * Get the exception message.
     *
     * @param  string $message
     * @param  int $code
     * @param  Exception $previous
     * @return void
   */
	public function __construct($message, $code = 0, Exception $previous = null)
	{	
	    /**
	     * @param $message
	     * @param $code
	     * @param $previous
	     */
		parent::__construct($message, $code, $previous);
		set_exception_handler(function ($exception) {

        	$exception_msg = '<b>'.get_class($exception).'</b> <br />';
        	$exception_msg .= $exception->getMessage().'<br />';
        
        	$exception_trace = '<br />';
        	$exception_trace_array = $exception->getTrace();
        	
        	if (!empty($exception_trace_array)) {
        		foreach($exception_trace_array as $key => $item) {
        			$exception_trace .= '<b>File:</b> '.$item['file'].' - Line# '.$item['line'].'<br />';
        		}
        	}
        	echo $exception_msg;
        	echo $exception_trace;
	
        }); 
	}	
}

/**
 * Overrides uncaught exceptions
 *
 * @param  $exception
 * @return void
*/
