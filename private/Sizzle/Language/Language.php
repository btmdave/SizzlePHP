<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    Helper
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\Language;
use Sizzle\Language\LanguageException;

/**
 * Language
 * 
 * Language library for defining multilingual messaging.
 * Languages folders define the language to work with.  
 * Files within each folder are relative to their purpose.
 * Languages are stored in an array object using a key => value store.
 *
 * @category   Sizzle
 * @package    Language
 * @author     David Squires <dave@bluetopmedia.com>
 *
 */
class Language
{

    /**
     * Language
     * @var array
     */
	private $_lang = array();
	
	/**
	 * Set our default language
	 * @param string $language
	 * @param string $lang_file
	 * @param string $lang
	 */
	public function __construct($language = 'english', $lang_file = 'validation', $lang = '')
	{
		$this->setLangFile($language, $lang_file, $lang = '');
	}
	
	/**
	 * Set the language file and parameters
	 * @param   string $language
	 * @param   string $lang_file
	 * @param   string $lang
	 * @returns array languages
	 * @throws LanguageException
	 */
	public function setLangFile($language, $lang_file, $lang = '') {
	    
	    $file = __DIR__.'/'.$language.'/'.$lang_file.'.php';
	    
	    if(file_exists($file))
	    {
	        include $file;
	        $this->_lang = $lang;
	        return $this->_lang;
	    }
	    
	    throw new LanguageException('Language file '.$language.'/'.$lang_file.' was not found.');  
	}
	
	/**
	 * Get line by key from a language file
	 * @param string $key
	 * @param string $message define custom message
	 */
	public function line($key, $message = '')
	{
	    if(isset($this->_lang[$key])){
	        
	        if($message !== '') {
	           return sprintf($this->_lang[$key], $message);
	        }
	        
	        return $this->_lang[$key];
	    }
	}	
	
	
}
