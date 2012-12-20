<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataMapper
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataMapper;
use Sizzle\DataMapper\Db,
    Sizzle\DataMapper\Types\DateTime;

/**
 * Model abstract class which is inherited by application models.  This class
 * contains general information about the model extending this and associated
 * data.
 *
 * @category   Sizzle
 * @package    DataMapper
 * @author     David Squires <dave@bluetopmedia.com>
 * @uses       Sizzle\DataMapper\Db
 * @uses       Sizzle\DataMapper\Types\DateTime
 * @abstract
 */
abstract class Model extends Db
{
	/**
	 * Name of the model
	 * @var string
	 */
	private $model;
	
    /**
     * Name of table
     * @var string
     */
    private $table;
    
    /**
     * Name of the primary key
     * @var array
     */    
    private $id;
    
    /**
     * ManyToMany relationships
     * @var array
     */    
    private $ManyToMany = array();

    /**
     * Custom column types (DateTime)
     * @var array
     */
    private $ColumnTypes = array();    
    
    /**
     * We call connect() to create database connection(s)
     */
    public function __construct()
    {
        parent::__construct();
        parent::connect();
        $this->model = str_replace("\\",'',substr(get_called_class(),strrpos(get_called_class(), "\\")));
    }
    
    /**
     * We parse all the comments from within the model.
     * This obtains necassary information for use in our Db->save() and Db->delete() methods.
     */
    protected function parseComments()
    {
		
        $reflection = new \ReflectionClass(get_called_class());
        $propertyList = $reflection->getProperties();

        //Get the dockblock comments for the model 
        $docComment = trim($reflection->getDocComment());
        if(!empty($docComment)) {
            $this->table = $this->parseString($docComment, '@Table(name="', '")');
        }
        
        //Now we parse the individual properties to get the primary key and manytomany tables
        foreach ($propertyList as $property) {
            
            $docComment = trim($property->getDocComment());
            $attr['Id'][]         = $this->parseString($docComment, '@Id(name="', '")');
            $attr['ManyToMany'][$property->getName()] = $this->parseString($docComment, '@ManyToMany(target="', '"');
            $attr['JoinOn'][$property->getName()]     = $this->parseString($docComment, 'JoinOn="', '")');
            $attr['ColumnType'][$property->getName()] = $this->parseString($docComment, '@Column(type="', '")');
        }

        foreach($attr['ManyToMany'] as $key => $item) {
            if(isset($attr['JoinOn'][$key])) {
                if(!empty($item) && !empty($attr['JoinOn'][$key])){
                    $this->ManyToMany[$key] = array('table' => $item, 'id' => $attr['JoinOn'][$key]);
                }
            }
        }
        

        foreach($attr['ColumnType'] as $key => $item) {
                if(!empty($item)){
                    $this->ColumnTypes[$key] = $item;
                }
        }
        
        $this->id = array_filter($attr['Id']);
    }
  
    
    /**
     * Return the ManyToMany data, used in Db
     * @param string $table
     * @return array|boolean
     */
    protected function getManyToMany($table)
    {
        if(!empty($this->ManyToMany)) {
            foreach($this->ManyToMany as $key => $item) {
                if($key == $table) {
                    return $this->ManyToMany[$key];
                }
            }
        }
        
        return false;
    }
    
    /**
     * Return the Column Type data, used for defining SQL 
     * @param string $column
     * @return array|boolean
     */
    protected function getColumnTypes($column)
    {

        if(!empty($this->ColumnTypes)) {
            foreach($this->ColumnTypes as $key => $item) {
                if($key == $column) {
                    return $this->ColumnTypes[$key];
                }
            }
        }
    
        return false;
    }
    
    /**
     * Returns the table name
     * @return string   
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * Returns the primary key field
     * @return int
     */
    protected function getId()
    {
        return isset($this->id[0]) ? $this->id[0] : false;
    }
    
    /**
     * Returns the mapper
     * @return string
     */
    public function getMapper()
    {
        $mapper = 'Models\Mappers\\' . $this->model.'Mapper';
        $this->mapper = new $mapper;
        return $this->mapper;
    }
    
    /**
     * Private function to parse comments.  Grabs content between start and end position.
     * @todo   Create regex helper library and use here.
     * @param  string $string
     * @param  string $start
     * @param  string $end
     * @return string
     */
    private function parseString($string, $start, $end){
        
        $string = " ".$string;
        $ini = strpos($string, $start);
        if ($ini == 0) return false;
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return trim(substr($string,$ini,$len));
    }
}