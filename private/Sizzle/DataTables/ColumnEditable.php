<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataTables;

/**
 * ColumnEditable
 * 
 * Enables a field to become inline-editable.  
 *
 * @category   Sizzle
 * @author     David Squires <dave@bluetopmedia.com>
 * @package    DataTables
 * @link       http://www.appelsiini.net/projects/jeditable
 */
class ColumnEditable {
    
    /**
     * URL for ajax post
     * @var string
     */
    protected $url;
    
    /**
     * Field name 
     * @var string
     */
    protected $name;
    
    /**
     * Type of field (input or select)
     * @var string
     */
    protected $type;

    /**
     * POST data added to ajax request
     * @var array
     */
    protected $data;
    
    /**
     * CSS class assigned to the field
     * @var string
     */
    protected $class;    
    
    /**
     * Construct our field variables
     * @param string $url
     * @param string $name
     * @param string $type
     * @param array  $data
     */
    public function __construct($url, $name, $type = 'text', $data = false)
    {
       $this->url     = $url;
       $this->name    = $name;
       $this->type    = $type;
       $this->data    = $data;
    }
    
    /**
     * Set the primary key for updating the field
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Get the JavaScript for the editable field
     * @return string
     */
    public function getEditable()
    {
         
          $data = ($this->data) ? 'data      : '.json_encode($this->data).',' : '';

          $editable = "$('.editable_".$this->class."').editable('".$this->url."', {
                                 ".$data."
                                 indicator : \"<img src='/assets/img/loader_small.gif'>\",
                                 cancel    : 'Cancel',
                                 submit    : 'Save',
                                 tooltip   : 'Click to edit...',
                                 name      : '".$this->name."',
                                 type      : '".$this->type."',
                                 submitdata : function(value, settings) {
                                      $(this).closest('tr').find('.alert-container').remove();
                                 },
                                 callback : function(response, settings) {

                                     var obj = jQuery.parseJSON(response);
                                     
                                     if (typeof obj == 'object') {
                                     
                                         $(this).html(obj.value)
                                         $(this).closest('td').append('<div class=\"alert-container\"><div class=\"alert-message error\"><a class=\"close\" href=\"#\">&times;</a><p>'+obj.error+'</p></a></div>');
                                         $('.alert-message').alert();
                                     }

                                 }
                            });";
          return $editable;
    }    
    
    /**
     * Set a custom class name
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }
}