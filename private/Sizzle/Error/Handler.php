<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Error
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Error;
use Sizzle\Loader\Loader;

/**
 * Custom Error Handler
 *
 * @category   Sizzle
 * @package    Error
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Handler {

    /**
     * Config attributes
     * @var array
     */
    public static $_config;    
    
    /**
     * Static method to run the default PHP error handler.
     */
    public static function run()
    {
       
       self::$_config = Loader::LoadConfig();
       
       //check the config for error_handler attributes
       if(isset(self::$_config['error_handler'])) {
           if(self::$_config['error_handler']['enabled'] == 'true') {
               set_error_handler("errorHandler");
           }
       }
       
    }
}

/**
 * Custom error handler
 * @param int    $errno
 * @param string $errstr
 * @param string $errfile
 * @param string $errline
 */
function errorHandler($errno, $errstr, $errfile, $errline)
{

    $error_template = '
    <table style="padding: 2px; border: 1px solid #cccccc;">
        <thead>
            <tr>
                <td style="padding: 15px; font-family: Arial; font-size: 14px; background: #ae0707; font-weight: bold;">
                    <span style="color: #ffffff;">%s
                </td>
            </tr>
        </thead>
        <tr>
            <td style="padding: 15px; font-family: Arial; font-size: 14px;"><div style="color: #000000;">%s</div></td>
        </tr>
    </table>
    ';
    
    switch ($errno) {
        
        case E_USER_ERROR:
            $error_name = 'ERROR';
            $error = "<b>ERROR: </b> [$errno] $errstr<br /><br />
             Line $errline in file $errfile <br /><br />
             URL: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; 
            break;
    
            case E_USER_WARNING:
            $error_name = 'WARNING';
            $error = "<b>WARNING: </b> [$errno] $errstr<br /><br />
             Line $errline in file $errfile <br /><br />
             URL: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; 
            break;
    
            case E_USER_NOTICE:
            $error_name = 'NOTICE';
            $error = "
            <b>NOTICE: </b> [$errno] $errstr<br /><br />
             Line $errline in file $errfile <br /><br />
             URL: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; 
            break;
    
            default:
            $error_name = 'UNKNOWN';
            $error = "
            <b>UNKNOWN: </b> [$errno] $errstr<br /><br />
             Line $errline in file $errfile <br /><br />
             URL: ".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; 
  
    }

    $error = sprintf($error_template, $error_name, $error);
    
    if(isset($error)) {
        
          /**
           * @todo Replace dependency on Zend's mail object.
           */
            /*
                $mail = new Mail();
                $mail->setBody($error)
                ->setTo(Handler::$_config['error_handler']['to'])
                ->setFromName('Sizzle Error Handler')
                ->setSubject('CORE '.$errno)
                ->send();
            */
    }
    
    /* Don't execute PHP internal error handler */
    return true;
}