<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Form
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Form;

use Sizzle\Language\Language,
    Sizzle\Controller\Request,
    Sizzle\Form\FormException;

/**
 * Validation
 * Form validation
 *
 * @category    Sizzle
 * @package     Form
 * @author      David Squires <dave@bluetopmedia.com>
 * @uses        Sizzle\Language
 * @uses        Sizzle\Controller\Request
 */
class Validation
{

    /**
     * Errors
     * @var array
     */
	private $_errors = array();
	
	/**
	 * Fields
	 * @var fields
	 */
	private $_field = array();
	
	/**
	 * POST array
	 * @var array
	 */
	private $_post;
	
	/**
	 * Language object
	 * @var object
	 */
	private $_lang;
	
	/**
	 * Request object
	 * @var object
	 */
	private $_request;

	/**
	 * Define our language settings and load dependencies
	 * @param string $language
	 * @param string $lang_file
	 */
	function __construct($language = 'english', $lang_file = 'validation')
	{
	
		$this->_lang     = new Language;
		$this->_request  = new Request;
   
	}
	
	/**
	 * Set the source of the data we're validating (POST)
	 * @param array $source
	 */
	public function source($source)
	{
		$this->_post = $source;
	}
	
	/**
	 * Set validation for a field
	 * @param string $field
	 * @param string $label
	 * @param string $error_msg
	 */
	public function set($field, $label = '', $error_msg = '')
	{	
	    
			$this->_field = array(
				'field'				=> $field,
				'label'				=> $label,
				'error'				=> $error_msg
			);

			return $this;	
	}

	/**
	 * We define a custom validation type associated to a language key.
	 * @param string $type    Name of our validation.  Such as 'required' or 'invalid_login'
	 * @param string $return
	 * @param string $label
	 * @throws FormException
	 * @return \Sizzle\Form\Validation
	 */
	public function customError($type, $return = false, $label = null)
	{
	    if(isset($this->_field['field'])){
    	    if(array_key_exists($this->_field['field'], $this->_errors)) {
    	        return $this;
    	    }
	    }
	    
        if($label) {
	        $this->_field['label'] = $label;
        }
        
        if (false !== ($line = $this->_lang->line($type))) {
            if($return) {
                
                $line = sprintf($line, $label);
                return $line;
                
            } else {
    			$this->setErrors($type);
    			return $this;
            }
         }
		
		throw new FormException('No custom error message to display.');
	}
	
	/**
	 * isRequired
	 * @param string $lang_line
	 * @return \Sizzle\Form\Validation
	 */
	public function isRequired($lang_line = null)
	{	

	    if(array_key_exists($this->_field['field'], $this->_errors)) {
	        return $this;
	    }
    
		$field = $this->_field['field'];
		if (!isset($this->_post[$field]) || $this->_post[$field] == ''){
			if($lang_line){
				$this->setErrors($lang_line);
			} else {
				$this->setErrors('required');
			}
			
		}
		return $this;
	}

	/**
	 * notContain
	 * @param string $string
	 * @param string $lang_line
	 * @return \Sizzle\Form\Validation
	 */
	public function notContain($string, $lang_line = null){

	    if(array_key_exists($this->_field['field'], $this->_errors)) {
	        return $this;
	    }
	
	    $field = $this->_field['field'];
	    if (stristr($this->_post[$field], $string)) {
	        if ($lang_line) {
	            $this->setErrors($lang_line);
	        } else {
	            $this->setErrors('invalid_characters');
	        }
	    }
	
	    return $this;
	
	}	
	
	/**
	 * maxLength
	 * @param int $n
	 * @return \Sizzle\Form\Validation
	 */
	public function maxLength($n){
	
		if(array_key_exists($this->_field['field'], $this->_errors)) {
			return $this;	
		}
		
		$field = $this->_field['field'];
		if(strlen($this->_post[$field]) > $n){
			$this->_field['extra'] = $n;
			$this->setErrors('max_length');
		}
		
		return $this;
	}
	
	/**
	 * minLength
	 * @param int $n
	 * @return \Sizzle\Form\Validation
	 */
	public function minLength($n){

    	if(array_key_exists($this->_field['field'], $this->_errors)) {
    		return $this;
    	}
    		
    	$field = $this->_field['field'];
    	if(strlen($this->_post[$field]) < $n){
    		$this->_field['extra'] = $n;
    		$this->setErrors('min_length');
    		
    	}
    		
    	return $this;
	}	
	
	/**
	 * isEqualTo
	 * @param string $match_field
	 * @param string $lang_line
	 * @return \Sizzle\Form\Validation
	 */
	public function isEqualTo($match_field, $lang_line)
	{
			
		if(array_key_exists($this->_field['field'], $this->_errors)) {
			return $this;
		}
		
		$field = $this->_field['field'];
	
		if ($this->_post[$field] !== $this->_post[$match_field]) {
			$this->setErrors($lang_line);
		}
			
			
		return $this;
	
	}	
	
    /**
     * isEmail
     * @return \Sizzle\Form\Validation
     */
	public function isEmail()
	{
			
		if(array_key_exists($this->_field['field'], $this->_errors)) {
			return $this;	
		}
		
		$field = $this->_field['field'];
		if(!filter_var($this->_post[$field], FILTER_VALIDATE_EMAIL))
		{
			$this->setErrors('valid_email');
		}
		
		return $this;
	}

    /**
     * setErrors
     * @param string $type
     * @throws FormException
     */
	private function setErrors($type)
	{
		if(!empty($this->_field)) {
			
			if (false !== ($line = $this->_lang->line($type))) {
		
				if($this->_field['error'] == '') {
					
					$this->_errors[$this->_field['field']]['field'] = $this->_field['field'];
					$this->_errors[$this->_field['field']]['label'] = $this->_field['label'];
					
					if(isset($this->_field['extra'])) {
					   
						$this->_errors[$this->_field['field']]['msg'] = sprintf($line, $this->_field['label'], $this->_field['extra']);
					
					} else {
					    
						$this->_errors[$this->_field['field']]['msg'] = sprintf($line, $this->_field['label']);
					}
				}
				
			} else {
				
				if($this->_field['error'] !== '') {
					
					$this->_errors[$this->_field['field']]['field'] = $this->_field['field'];
					$this->_errors[$this->_field['field']]['label'] = $this->_field['label'];
					$this->_errors[$this->_field['field']]['msg'] = sprintf($line, $this->_field['label']);
					
				} else {
					
					throw new FormException('No error message to display.');
				}
			}
		}
	}

	/**
	 * getErrors
	 * return mixed
	 */
	public function getErrors()	
	{
		$errors = $this->_errors;
		empty($this->_errors);
		
		return $errors;
	}	

}