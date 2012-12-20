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
 * Image
 * Process and upload images
 *
 * @category   Sizzle
 * @package    Form
 * @author     David Squires <dave@bluetopmedia.com>
 */
class Image
{
	
	/**
	 * Image array
	 * @access private
	 * @var array
	 */		
	private $_tmp_image;
	
	/**
	 * Image array
	 * @access private
	 * @var array
	 */
	private $_image;	
	
	/**
	 * Image Guid
	 * @access public
	 * @var string
	 */
	public $_guid;
	
	/**
	 * Image Extension (Type)
	 * @access public
	 * @var string
	 */
	public $_type;
	
	/**
	 * Image width
	 * @access public
	 * @var string
	 */
	public $_width;
	
	/**
	 * Image height
	 * @access public
	 * @var string
	 */
	public $_height;
	
	/** Image instance of gd2 library
	 *  @access public
	 *  @var public
	 */
	public $im;
	
	/**
	 * Config 
	 * @access private
	 * @var array
	 */
	private $_config;
	
	
	/**
	 * Path to upload files
	 * @access private
	 */
	private $_upload_path;
	
	
	
	public function setUploadPath($path)
	{
		$this->_upload_path = $path;
		return $this;
	}

    /**
     * Set the image file from the post
     * @param array $file
     */
	public function set($file)
	{
	
	    if(empty($file)){
	        return false;
	    }
	    
	    $this->_tmp_image = $file;
	    return $this;
	}
	
	/**
	 * Add image to filesystem if it does not already exist
	 * @param string $dir
	 * @param int    $width
	 * @param int    $height
	 * @return \Sizzle\Form\Image
	 */
	public function uploadImage($dir = '/', $width = false, $height = false)
	{
	    if(!empty($this->_image)) {

	 		foreach($this->_image as $img) {
	 		     
	 			foreach($img as $key => $value) {
	 				
	 				if (!file_exists($this->_upload_path . $value['name'])) {
	 				    
	 					$this->getImageInfo($value['tmp_name']);
	 					$this->save($value['name'], $value['type'], $dir);
	 					
	 					if($width && $height) {
	 					    $this->createImage($value['name'],$value['type'], $width, $height, $dir);
	 					}
	 				} 
	 			}
	 		}
	    } 
	 	
	 	return $this;
	 }
		
	 /**
	  * Create the thumbnail images
	  * @param string $filename
	  * @param string $type
	  * @param int    $new_w
	  * @param int    $new_h
	  * @param string $dir
	  */
	 private function createImage($filename, $type, $new_w, $new_h, $dir) {
	 
	     
	 	if ($this->width > $this->height) {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
		if ($this->width < $this->height) {
			$thumb_w = $this->width * ($new_w/$this->height);
			$thumb_h = $new_h;
		}
		if ($this->width == $this->height) {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
		
		if ($this->width < $new_w) {
			$thumb_w = $this->width;
			$thumb_h = $this->height;
		}
		if ($this->height < $new_h) {
			$thumb_w = $this->width;
			$thumb_h = $this->height;
		}
	     
	     $new_img = imagecreatetruecolor($thumb_w, $thumb_h);
	     imagealphablending($new_img, false);
	     imagesavealpha($new_img, true);
	     imagecopyresampled($new_img,$this->im,0,0,0,0,$thumb_w,$thumb_h,$this->width,$this->height);
	     $this->save($filename, $type, $dir, $new_img);
	 }
	
	/**
	 * Generate the guids names for each of our images and rebuild our image array
	 */
	public function generateName()
	{

	    foreach($this->_tmp_image as $key => $img) {
	    	
     	       $name = explode('.',$img['name']);    	 
     	       $this->_type = $name[1];
           	   $this->_image[$key]['name'] = $this->buildGuid($name[0]).'.'.$name[1];
               $this->_image[$key]['type'] = $img['type'];
               $this->_image[$key]['tmp_name'] = $img['tmp_name'];
               $this->_image[$key]['error'] = $img['error'];
               $this->_image[$key]['size'] = $img['size'];

	    }

	    return $this;
	}
	
	
	/**
	 * Remove image from the filesystem
	 * @param string $file
	 * @param string $dir
	 * @return \Sizzle\Form\Image
	 */
	public function deleteImage($file, $dir){
	  
	    $directory = $this->_upload_path . $dir;
	    $file = $directory . '/' . $file;

	    if(file_exists($file)){
	        unlink($file);
	        return $this;
	    }
	  
	    return $this;
	}
	
	/**
	 * Get the size and type information using GD
	 * @param string $filename
	 */
	private function getImageInfo($filename)
	{
		$info = getimagesize($filename);
		
		if($info['mime'] == 'image/jpeg') {
			$this->im = imagecreatefromjpeg($filename);
		} else if($info['mime'] == 'image/png') {
			$this->im = imagecreatefrompng($filename);
		} else if($info['mime'] == 'image/gif') {
			$this->im = imagecreatefromgif($filename);
		}
		
		$this->width = imagesx($this->im);
		$this->height = imagesy($this->im);
		
	}
	
	/**
	 * Save the image on the filesystem
	 * @param string $filename
	 * @param string $type
	 * @param string $dir
	 * @param string $im
	 */
	private function save($filename, $type, $dir='original', $im = null){
	    
		if($im == null) {
			$im = $this->im;
		}
		
		$directory = $this->_upload_path . $dir;
		$file = $directory . '/' . $filename;
		
		if(!file_exists($directory)) {
		    mkdir($directory, 0777, true);
		}
		
		imagealphablending($im, false);
		imagesavealpha($im, true);
		
		if($type == 'image/jpeg') {
			imagejpeg($im, $file);
		} else if($type == 'image/png') {
			imagepng($im, $file);
		} else if($type == 'image/gif') {
			imagegif($im, $file);
		}
	}
	
	/**
	 * Builds our guid for the image name to avoid conflicts
	 * @param string $var
	 */
	private function buildGuid($var = null)
	{
	    $this->_guid = md5(base64_encode($var.uniqid()));
	    return $this->_guid;
	}	

}

