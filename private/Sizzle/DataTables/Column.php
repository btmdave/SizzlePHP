<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataTables;

use Sizzle\Helper\String;

use Sizzle\DataTables\ColumnCallback,
    Sizzle\DataTables\ColumnEditable;

/**
 * Column
 * 
 * Defines our column properties for each column we'll be adding to our DataTable
 * Each column has it's own column instance.
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 * @uses       Sizzle\DataTables\ColumnCallback
 * @uses       Sizzle\DataTables\ColumnEditable
 */
class Column {
    
    /**
     * Name of column to match database column name
     * @var string
     */
    protected $name;
    
    /**
     * Define the column name to be used when sorting and searching
     * We can't sort by aliases (i.e.,no users.username as name)
     * @var string
     */
    protected $custom_name = false;
    
    /**
     * Name to display in TH
     * @var string
     */
    protected $title;
    
    /**
     * Is column sortable?
     * @var boolean
     */
    
    protected $sortable = true;
   
    /**
     * Width of the column
     * @var string
     */
    protected $width;
    
    /**
     * Is column visible?
     * @var boolean
     */
    protected $visible    = true;
    
    /**
     * Is column searchable?
     * @var boolean
     */
    protected $search     = true;    
    
    /**
     * Is this the primary key column?
     * @var boolean
     */
    protected $primary    = false;
    
    /**
     * Is this column hidden from display?
     * @var boolean
     */
    protected $hidden     = false;
    
    /**
     * Callback 
     * @var object ColumnCallback
     */
    protected $callback   = false;
    
    /**
     * Inline editable
     * @var ColumnEditable
     */
    protected $editable   = false;
    
    /**
     * Default sort order ('asc' or 'desc')
     * @var string
     */
    protected $sort_order = 'asc';
    
    
    /**
     * setName
     * @param string $name
     * @return \Sizzle\DataTables\Column
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * setIsPrimaryKey
     * @return \Sizzle\DataTables\Column
     */
    public function setIsPrimaryKey()
    {
        $this->primary = true;
        return $this;
    }
    
    /**
     * setHidden
     * @return \Sizzle\DataTables\Column
     */
    public function setHidden()
    {
    	$this->hidden = true;
    	return $this;
    }   
    
    /**
     * setTitle
     * @param string $title
     * @return \Sizzle\DataTables\Column
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * setEditable
     * @param ColumnEditable $editable
     */
    public function setEditable(ColumnEditable $editable)
    {
        $editable->setClass($this->getName());
        $this->editable = $editable;
        return $this;
    }
    
    /**
     * setIsSortable
     * @param unknown_type $sortable
     */
    public function setIsSortable($sortable = true)
    {
        $this->sortable = $sortable;
        return $this;
    }
    
    public function setCustomName($name) 
    {
    	$this->custom_name = $name;
    	return $this;
    }
    
    /**
     * setCallback
     * @param ColumnCallback $callback
     * @return \Sizzle\DataTables\Column
     */
    public function setCallback(ColumnCallback $callback) {

        $this->callback = $callback;
        return $this;
    }
        
    /**
     * setWidth
     * @param string $width
     * @return \Sizzle\DataTables\Column
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }
    
    /**
     * setIsVisible
     * @param unknown_type $visible
     * @return \Sizzle\DataTables\Column
     */
    public function setIsVisible($visible = true)
    {
        $this->visible = $visible;
        return $this;
    }  
    
    
    public function setSortOrder($order)
    {
    	$this->sort_order = $order;
    	return $this;
    }
    
    /**
     * getIsPrimaryKey
     * @return boolean
     */
    public function getIsPrimaryKey()
    {
        return $this->primary;
    }  
   
    
    /**
     * setIsSearchable
     * @param boolean $search
     * @return \Sizzle\DataTables\Column
     */
    public function setIsSearchable($search = true)
    {
        $this->search = $search;
        return $this;
    }
    
    /**
     * getName
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getSortOrder
     * @return string
     */
    public function getSortOrder()
    {
    	return $this->sort_order;
    }
    
    
    /**
     * getCallback
     * @return ColumnCallback
     */
    public function getCallback() {
        return $this->callback;
    }    
    
    /**
     * getWidth
     * @return string
     */
    public function getWidth()
    {
    	return $this->width;
    }    
    
    /**
     * getTitle
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * getIsSortable
     * @return boolean
     */
    public function getIsSortable()
    {
        return $this->sortable;
    }
    
    /**
     * getCustomName
     * @return string
     */
    public function getCustomName()
    {
    	return $this->custom_name;
    }    
    
    
    /**
     * getEditable
     * @return ColumnEditable()->getEditable();
     */
    public function getEditable()
    {
        if($this->editable){
            return $this->editable->getEditable();
        }
    }    
    
    /**
     * getIsSearchable
     * @return boolean
     */
    public function getIsSearchable()
    {
        return $this->search;
    }  

    /**
     * getIsHidden
     * @return boolean
     */
    public function getIsHidden()
    {
    	return $this->hidden;
    }    
    
    
}