<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Mail
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Mail;
use Sizzle\Mail\MailException,
    Sizzle\Loader\Loader,
    Sizzle\Helper\Mustache;

    /*
    require_once COMMON.'Zend/Loader.php';
    set_include_path('Sizzle/ThirdParty');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_Gapps');
    Zend_Loader::loadClass('Zend_Mail');
    Zend_Loader::loadClass('Zend_Mail_Transport_Smtp');
    */

/**
 * Mail
 * 
 * SMTP mail wrapper
 *
 * @todo       Remove dependency of Zend Mail
 * @category   Sizzle
 * @package    Mail
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Mail{
	
    /**
     * Config
     * @var array
     */
	private $_config;
	
	/**
	 * Email template
	 * @var mixed
	 */
	private $_template     = false;
	
	/**
	 * Recipient email address
	 * @var string
	 */
	private $_recipient;
	
	/**
	 * From name
	 * @var string
	 */
	private $_from_name;
	
	/**
	 * Subject
	 * @var string
	 */
	private $_subject;
	
	/**
	 * Key/Value pair of tags to replace within email content
	 * @var array
	 */
	private $_tags;
	
	/**
	 * HTML or Text body
	 * @var string
	 */
	private $_body         = false;
	
	/**
	 * Load our mail settings
	 * @throws MailException
	 */
	public function __construct()
	{
		$config = Loader::LoadConfig();
		
		if (isset($config['mail'])) {
			$this->_config = $config['mail'];
		} else {
			throw new MailException('Mail settings are not defined within the config.');
		}
		
	}
	
	/**
	 * setTemplate
	 * @param string $template
	 * @return \Sizzle\Mail\Mail
	 */
	public function setTemplate($template)
	{
		$this->_template = $template;
		return $this;
	}
	
	/**
	 * setBody
	 * @param string $body
	 * @return \Sizzle\Mail\Mail
	 */
	public function setBody($body)
	{
	    $this->_body = $body;
	    return $this;
	}
	
	/**
	 * setTo
	 * @param string $email
	 * @return \Sizzle\Mail\Mail
	 */
	public function setTo($email)
	{
		$this->_recipient = $email;
		return $this;
	}
	
	/**
	 * setSubject
	 * @param string $subject
	 * @return \Sizzle\Mail\Mail
	 */
	public function setSubject($subject)
	{
	  
		$this->_subject = $subject;
		return $this;
	}	
	
	/**
	 * setFromName
	 * @param unknown_type $name
	 * @return \Sizzle\Mail\Mail
	 */
	public function setFromName($name)
	{

		$this->_from_name = $name;
		return $this;
	}	
	
	/**
	 * replaceTags
	 * @param string $tags
	 * @return \Sizzle\Mail\Mail
	 */
	public function replaceTags($tags)
	{
		$this->_tags = $tags;
		return $this;
	}
	
	/**
	 * getSender
	 * @return string sender
	 */
	public function getSender()
	{
		return (isset($this->_sender)) ? $this->_sender : false;
	}
	
	/**
	 * getBody
	 * @return string body
	 */
	public function getBody()
	{
		return (isset($this->_body)) ? $this->_body : false;
	}	
	
	/**
	 * Get the HTML template if defined
	 * @return string template
	 */
	private function getTemplate()
	{
		
		$template_file = TEMPLATEPATH.'email/'.$this->_template.'.html';
		
		if(!file_exists($template_file)){
			$template_file = TEMPLATEPATH.'email/default_welcome.html';
		}
		
		$template = file_get_contents($template_file);
		
		if(!empty($this->_tags)) {
	        $m = new Mustache();
		    $template = $m->render($template, $this->_tags);
		}
		
		return $template;
	}
	
	/**
	 * Send the email.
	 * @todo   Requires update to remove dependency on Zend Mail
	 * @throws MailException
	 * @throws Mail_Exception
	 */
	public function send()
	{
	 
		try {
			
			$required_attr = array('smtp','ssl','port','auth','username','password','sender');
					
			foreach($required_attr as $attr) {
				
				if (!isset($this->_config[$attr])) {
					throw new MailException('Required Mail attribute '.$attr.' is not defined.');
				}
				
			}
			
			$config = array('ssl'		=> $this->_config['ssl'],
							'port' 		=> $this->_config['port'],
							'auth' 		=> $this->_config['auth'],
							'username' 	=> $this->_config['username'],
							'password' 	=> $this->_config['password']);
	
			//$transport = new Zend_Mail_Transport_Smtp($this->_config['smtp'], $config);
			
			$this->_sender = $this->_from_name . ' <'.$this->_config['sender'].'>';

			if(!$this->_body){
			    $this->_body = $this->getTemplate();
			}
            
			/*
    			$mail = new Zend_Mail();
    			$mail->setBodyHtml($this->_body);
    			$mail->setFrom($this->_config['sender'], $this->_sender);
    			$mail->addTo($this->_recipient, '');
    			$mail->setSubject($this->_subject);
    			$mail->send($transport);
			*/

			return true;
			
		} catch (MailException $e) {
			throw new MailException($e);	
		}
	}
}