<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Form
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Form;
use Sizzle\Form\FormException;

/**
 * Sanitize input data using filters.  Validation can be used after data sanitizing.
 *
 * <code>
 * $filter = new Form_Input_Filter();
 * $sanitized_email = $filter->value('email@email.com')->filterEmail()->get();
 * </code>
 *
 * @category Sizzle
 * @package Form
 * @author   David Squires <dave@bluetopmedia.com>
 */
class Filter
{

    /**
     * Input value being santitized.  
     * @access private
     * @var string
     */
    private $_input;
    
    /**
     * Method overloading
     * Used to validate correct order of chaining and maintain protected methods
     * 
     * @access public
     * @param  method protected method
     * @param  string input being sanitized
     */		
    public function __call($method, $args)
    {
        if($method !== 'value' && empty($this->_input)) {
           return false;
        }
        
        if(method_exists($this, $method)) {
            $args = implode('',$args);
            return $this->$method($args);
        } 
     
        return false;
    }
    
    /**
     * Sets the input being sanitized.  This is the first method to be called passing the
     * value to be sanitized.
     *
     * @param string input being sanitized
     * @return $this
     */		
    protected function value($var)
    {
        $this->_input = $var;
        return $this;
    }
    
    /**
     * EMAIL filter
     *
     * @return $this
     */		
    protected function filterEmail()
    {
          $this->_input = filter_var($this->_input, FILTER_SANITIZE_EMAIL);
          return $this;
    }
    
    /**
     * ENCODED filter
     *
     * @return $this
     */		
    protected function filterEncoded()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_ENCODED);
        return $this;
    }
    
    /**
     * MAGIC_QUOTES filter
     *
     * @return $this
     */		
    protected function filterMagicQuotes()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_MAGIC_QUOTES);
        return $this;
    }		
    
    /**
     * NUMBER_FLOAT filter
     *
     * @return $this
     */
    protected function filterFloat()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_NUMBER_FLOAT);
        return $this;
    }
    
    /**
     * NUMBER_INT filter
     *
     * @return $this
     */
    protected function filterInt()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_NUMBER_INT);
        return $this;
    }
    		
    /**
     * SANITIZE_SPECIAL_CHARS filter
     *
     * @return $this
     */
    protected function filterSpecialChars()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_SPECIAL_CHARS);
        return $this;
    }		
    
    /**
     * STRING filter
     *
     * @return $this
     */
    protected function filterString()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_STRING);
        return $this;
    }		
    
    /**
     * URL filter
     *
     * @return $this
     */
    protected function filterUrl()
    {
        $this->_input = filter_var($this->_input, FILTER_SANITIZE_URL);
        return $this;
    }		
    
    /**
     * Returns the final sanitized string after all applied filters.
     * Clears $this->_input to avoid conflicts
     *
     * @return string - sanitized string
     */
    protected function get()
    {
       $filtered_input = $this->_input;
       $this->_input = null;
        
       return $filtered_input;
    }
}
	
