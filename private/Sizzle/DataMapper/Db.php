<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataMapper
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataMapper;

use Sizzle\Loader\Loader,
    Sizzle\Profiler\Profiler,
    \PDO;

/**
 * 
 * Database class wraps MySQL PDO functionality and provides internal connection and model methods.
 * This class is inherted by the Mapper and Model objects.
 * Shorthand methods (save(), delete()) for use by indvidual model's are found within this class.
 * 
 * @category Sizzle
 * @package  DataMapper
 * @author   David Squires <dave@bluetopmedia.com>
 * @uses     Sizzle\Loader
 * @uses     Sizzle\Profiler\Profiler,
 * @uses     \PDO
 */
class Db {

    /**
     * PDO connecton object.  Maintains multiple DB connects by key.
     * @var array
     */   
    private $_connection;
    
    /**
     * PDOStatement
     * @var object 
     */
    private $_pdoStmt;
    
    /**
     * SQL code being prepared to execute
     * @var string
     */
    private $_sql;
    
    /**
     * Database name
     * @var string
     */
    private $_database_name;
    
    /**
     * Config
     * @var array
     */
    private $_config;
    
    /**
     * Profiler instance
     * @var object
     */
    private $Profiler;
    
    /**
     * Instance of current DB object
     * @var object
     */
    private static $instance = null;
    
    /**
     * Set order for sql query
     * @var string;
     */
    private $_order = false;
    /**
     * DB Constructor
     */
    public function __construct() {
        
         self::$instance =& $this;

         if (!empty(self::$instance)) {
            foreach (self::$instance as $key => $class) {
                     $this->$key = $class;
            }
         }
         
         //Get an instance of the profiler to log page-level SQL queries
         $this->Profiler = Profiler::getInstance();
    }

    /**
     * Initilize our database connection(s)
     * This method is called in the constructors of the class in which this is inherited
     * 
     * @throws DataMapperException
     */
    protected function connect()
    {
        $this->_config 			= Loader::loadConfig();

        if( isset($this->_config['databases'])){
        
            foreach($this->_config['databases'] as $db => $setting) {
                try {
                    
                    if(isset($this->_connection[$db])) {
                        continue;
                    }
                    
                    $this->_connection[$db] = new PDO('mysql:dbname='.$setting['name'].';host='.$setting['host'].';port='.$setting['port'], $setting['user'], $setting['pass']);
                    $this->_connection[$db]->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    
                } catch (DataMapperException $e) {
        
                    throw new DataMapperException($e);
        
                }
            }
        }
    }
    
    /**
     * Obtain the the PDO object for the desired connection
     *
     * @param string $key
     */
    public function __get($key)
    {
        if( isset($this->_connection[$key]) ) {
            $this->_database_name = $key;
            return $this;
        }
        
        return false;
    }
    
    /**
     * Set PDO attributes
     *
     * @param string $key
     * @param string $value
     */
    protected function setAttribute($key, $value)
    {
        return $this->_connection[$this->_database_name]->setAttribute($key, $value);
    }
    
    /**
     * Will perform an insert on the passed model object.  Using
     * This uses 'ON DUPLICATE KEY UPDATE' to perform an update if we pass
     * an existing primary key value.
     * 
     * Example from within controller:
     * <code>
     *  //Using the Account model
     *  $account = new Account;
     *  $account->setFirstName('name');
     *  $account->db->save();
     * </code>
     * 
     * @throws DataMapperException
     * @return boolean|string
     */
    public function save() 
    {
        $model = $this;
        $model->parseComments();
        
        //Get the data we need related to our model
        $table      = $model->getTable();
        $id         = $model->getId();
        $values     = $this->getModelPropertyValues($model);
        
        //If the model is empty, we can stop now.
        if(empty($values)) {
            return false;
        }
        
        //We get the values and remove any empty ones.
        $values = array_filter($values);
        $fields = array_keys($values);

        //We iterate through our fields and make adjustments based on the annotations
        foreach ($fields as $key => $field) {
            
            //Handle our ManyToMany relationships
            if (($ManyTarget = $model->getManyToMany($field)) !== false) {
               if(!empty($values[$field])) {
                  $ManyToMany[] = array_merge($ManyTarget, array('value' => $values[$field]));
                  unset($fields[$key]);
                  continue;
               } else {
                  unset($fields[$key]);
                  continue;
               }
            }
            
            //Get any custom fields, remove them so we're not binding the values, and prepare them for formatting
            if (($ColumnTypeTarget = $model->getColumnTypes($field)) !== false) {

                if(!empty($values[$field])) {
                    $columnTypes[] = array('name' => $field, 'type' => $ColumnTypeTarget, 'value' => $values[$field]);
                    unset($fields[$key]);
                    continue;
                } else {
                    unset($fields[$key]);
                    continue;
                }
            }   
    
            
            $nfields[$field]  = $field." = :".$field;
        }
     
   		
        if (isset($ManyToMany)) {
      
            //We're going to loop through any ManyToMany tables to build the SQL and perform inserts
            $params = array();
            foreach ($ManyToMany as $key => $target) {
            
                $this->_sql = "INSERT INTO `".$target['table']."`
                                   (".$table."_".$id.", ".$target['id'].") 
                               VALUES 
                                   (:".$id.", :".$target['id'].")
                               ON DUPLICATE KEY UPDATE 
                                                ".$table."_".$id." = ".$model->$id.", ".$target['id']." = ".$target['value'];
          
                $this->prepare($this->_sql);
                $this->_pdoStmt->bindParam(":".$id, $values[$id]);
                $this->_pdoStmt->bindParam(":".$target['id'], $target['value']);

                //Execute the statement and catch any exceptions
                $this->execute();

              return $this->lastInsertId();
           }
        }
        
        //Prepare our additional fields based on the custom column types.
        $newFieldSet       = '';
        $newFieldValues    = '';
        $newFieldSetValues = '';
        if(!empty($columnTypes)) {
            foreach($columnTypes as $item) {
                $newFieldSet        .= ', '.$item['name'];
                $newFieldValues     .= ', '.$item['value'];
                $newFieldSetValues  .= ', '.$item['name'].' = '.$item['value'];
            }
        }
        
        //We format our data for SQL
        $bindFields     =     implode(', ',  $nfields);
        $fieldSet       =     implode(', ',  $fields);
        $bindFieldSet   = ':'.implode(', :', $fields);
        
        $this->_sql = "INSERT INTO `{$table}` (".$fieldSet.$newFieldSet.") VALUES (".$bindFieldSet.$newFieldValues.")
                       ON DUPLICATE KEY UPDATE ".$bindFields.$newFieldSetValues;

        $this->prepare($this->_sql);

        //We loop through our values and bind our params
        if (!empty($values)) {
            $params = array();
            foreach ($values as $key => $value) {
                if (isset($nfields[$key])) {
                    $this->_pdoStmt->bindParam($key, $values[$key]);
                }
            }
        }
        
        //Execute the statement and catch any exceptions
        $this->execute();

        return $this->lastInsertId();
    }
    
    /**
     * Will issue a delete on the passed model object.
     * Requires commit() to perform execute statement.
     * You may use getSql() to ensure the intended statement
     * is accurate prior to issuing a commit()
     * 
     * @param Model $model
     * @return \Sizzle\DataMapper\Db
     */
    public function delete()
    {
    	$model = $this;
        //We need the annotations to associate our data
        $model->parseComments();
        
        //Get the data we need related to our model
        $table      = $model->getTable();
        $id         = $model->getId();
        $values     = $this->getModelPropertyValues($model);
   
        $fields     = array_keys($values);

        $this->_sql  = "DELETE FROM `{$table}` ";
        
        foreach($fields as $key => $field){

            if(($ManyTarget = $model->getManyToMany($field)) !== false) {
   
                $ManyToMany = array_merge($ManyTarget, array('value' => $values[$field]));
                $table  = $ManyToMany['table'];
                $id     = $ManyToMany['id'];
                $value  = $ManyToMany['value'];
                $this->_sql  = "DELETE FROM `{$table}` WHERE `{$id}` = {$value}";
                return $this;
            }
        }
        
        
        $id    = $model->getId();
  
        if(!empty($values[$id])) {
        
            $this->_sql  = "DELETE FROM `{$table}` WHERE `{$id}` = ".$values[$id];
        }
        
        return $this;
        
    }
    
    /**
     * We perform general select statements and fetch the results based on
     * the model calling our find() method.  By default, this will limit all
     * results to 1.  
     * 
     * Example:
     * <code>
     * $user = new User;
     * $user->find();
     * </code>
     * 
     * We optionally define attributes to be used in our where condition:
     * 
     * Example:
     * <code>
     * $user = new User;
     * $user->setId(5);
     * $user->find();
     * </code>
     * 
     * We can also pass in an array of key/values to be used in the where condition.
     * These will be used in conjunction to any pre-defined attributes.  The key should
     * match the column name, while the value is the condition we're searching for.
     * 
     * Example:
     * <code>
     * $user = new User;
     * $user->find(array('id' => 5, 'username' => 'name');
     * </code>
     * 
     * @see    Db::findAll()
     * @param  array $args
     * @param  string $limit
     * @return array|false
     */
    public function find($args = array(), $order_by = null, $limit = 'LIMIT 1', $select = array())
    {   

        //We need the annotations to associate our data
        $this->parseComments();
        
        //Get the data we need related to our model
        $table      = $this->getTable();
        $id         = $this->getId();
        $values     = $this->getModelPropertyValues($this);
        $fields     = array_keys($values);
        
        if(!empty($select)) {
            $field_select = implode(',', $select);
        } else {
            $field_select = '*';
        }
        
        $this->_sql  = "SELECT {$field_select} FROM {$table}";
        $where = '';

        foreach($values as $key => $value) {
            if(!$this->is_blank($value)) {
                if(!empty($where)) {
                    $where .= " AND {$key} = '{$value}'";
                } else {
                    $where .= "{$key} = '{$value}'";
                }
            }
        }
        
        if(!empty($where)) {
            $this->_sql .= ' WHERE '.$where;
        }

        foreach($fields as $key => $field){
          
            if(($ManyTarget = $this->getManyToMany($field)) !== false) {
              if(!empty($ManyTarget['value'])) {
                $ManyToMany = array_merge($ManyTarget, array('value' => $values[$field]));
                $table  = $ManyToMany['table'];
                $id     = $ManyToMany['id'];
                $value  = $ManyToMany['value'];
                $this->_sql  = "SELECT {$field_select} FROM {$table} WHERE {$id} = {$value}";
              }
            }
        }


        if(count($args) > 0) { 
            
            $i   = 0;
            $len = count($args);
            $where = '';
            foreach($args as $key => $value) {
                
                $where .= "{$key} '{$value}'";
            
                if($i < $len - 1) {
                    $where .= " AND ";
                }
                $i++;
            }
            
            if(strstr($this->_sql, 'WHERE')) {
                $this->_sql = $this->_sql.' AND '.$where;
            } else {
                $this->_sql = $this->_sql.' WHERE '.$where;
            }
        }
        
        if (!$this->_order) {
        	$this->_sql = $this->_sql. ' '. $this->_order;
        }
        
        
        $this->_sql = $this->_sql.' '.$order_by.' '.$limit;
        $this->prepare($this->_sql);
      
        $this->execute();
    
        if($limit == 'LIMIT 1'){
        	return $this->fetch();
        } 
        return $this->fetchAll();
    }  

    /**
     * Removes LIMIT and executes Db::fetchAll()
     * @uses   Db::find()
     * @param  array     $args     used in where clause
     * @return array|false
     */
    public function findAll($where = array(), $order_by = null, $fields = array(), $limit = "")
    {
    	return $this->find($where, $order_by, $limit, $fields);
    }
        
    /**
     * Get the values associated to the model we're working with.
     * @param  Model $model
     * @return array
     */
    private function getModelPropertyValues(Model $model)
    {
        $id         = $model->getId();
        $reflect = new \ReflectionClass($model);
        $values  = array();
        foreach ($reflect->getProperties() as $property) {
            $name         = $property->getName();
            $field_name   = ($name == 'id') ? $id : $name;
            $values   = array_merge($values, array($field_name => $model->$name));
        }
        
        return $values;
    }
    
    /**
     * Set ORDER BY value for sql query
     */
    public function setOrder($order = array(),$sort = 'ASC')
    {
    	if(!empty($order))
    	{
    		$this->_order = ' ORDER BY '. implode($order,',') . ' ' .$sort;
    	}
    	return $this->_order;
    }
    /**
     * Returns current SQL string.
     * @return string
     */
    public function getSql()
    {
        return $this->_sql;
    }
    
    /**
     * Execute the sql string for the given connection.
     * @return string
     */
    public function commit()
    {

        return $this->_connection[$this->_database_name]->exec($this->_sql);
    }

    /**
     * Get the last inserted id
     * @return int
     */
    public function lastInsertId()
    {
        return $this->_connection[$this->_database_name]->lastInsertId();
    }
    
    /**
     * Prepares a statement for execution.
     * @param  string $sql
     * @return PDOStatement 
     */
    public function prepare($sql)
    {
        return $this->_pdoStmt = $this->_connection[$this->_database_name]->prepare($sql);
    }
    
    
    /**
     * Execute the current prepared statement
     * @throws DataMapperException
     */
    public function execute()
    {
        
        $this->Profiler->setSql($this->_pdoStmt->queryString);
        $this->Profiler->start();
        $execute = $this->_pdoStmt->execute();
        $this->Profiler->stop();
        if(!$execute){
            $errorInfo = $this->_pdoStmt->errorInfo();
            throw new DataMapperException($errorInfo[2].$this->getSql());
        }
        
    }
    
    /**
     * Execute the sql string for the given connection.
     * @param  string $sql
     * @return mixed    number of rows effected
     */
    public function exec($sql)
    {
        
        $this->Profiler->setSql($sql);
        $this->Profiler->start();
        $exec = $this->_connection[$this->_database_name]->exec($sql);
        $this->Profiler->stop();
        return $exec;
    }
    
    /**
     * Fetches all results using FETCH_ASSOC by default.
     * @param  PDO::ATTR constant $fetch_style
     * @return array|boolean
     */
    public function fetchAll($fetch_style = null)
    {

        $result = $this->_pdoStmt->fetchAll($fetch_style);
        
        if (!empty($result)) {
            return $result;
        }
        
        return false;
    }
    
    /**
     * Fetches a single result using FETCH_ASSOC by default.
     * @param  PDO::ATTR constant $fetch_style
     * @return array|boolean
     */
    public function fetch($fetch_style = null)
    {

        $result = $this->_pdoStmt->fetch($fetch_style);
        
        if (!empty($result)) {
            return $result;
        }
        
        return false;
    }

    /**
     * Bind parameter for execution
     * @param string $param
     * @param string $value
     * @param string $type
     */
    public function bindParam($param, $value, $type = null)
    {
        $this->_pdoStmt->bindParam($param, $value, $type);
    } 
    
    /**
     * Bind value for execution
     * @param string $param
     * @param string $value
     * @param string $type
     */
    public function bindValue($param, $value, $type = null)
    {
        $this->_pdoStmt->bindValue($param, $value, $type);
    }
    
    /**
     * Helper function.  Should use Sizzle/Helper.
     * @param mixed $value
     */
    private function is_blank($value) {
    	return empty($value) && !is_numeric($value);
    }
    
    
}
