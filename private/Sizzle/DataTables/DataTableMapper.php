<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataTables;
use Sizzle\DataMapper\Mapper;

/**
 * DataTableMapper
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 * @uses       Sizzle\DataMapper\Mapper
 * @abstract
 */
abstract class DataTableMapper extends Mapper{ 
    
    /**
     * Database name
     * @var string
     */
    protected static $db;
    
    /**
     * Call the DataMapper Mapper class to execute our DB queries
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * getResults
     * @param  string $sql
     * @return array
     * @throws DataTablesException
     */
    protected function getResults($sql)
    {
        try {

            $db = self::$db;
            $this->$db->prepare($sql);
            $this->execute();
            return $this->$db->fetchAll();
        
        } catch(DataTablesException $e) {
            
            throw new DataTablesException($e);
        
        }
    }
    
    /**
     * getResultCount
     * @param  string $sql
     * @return array
     * @throws DataTablesException
     */
    protected function getResultCount($sql)
    {
        try {
    
            $db = self::$db;
            $this->$db->prepare($sql);
            $this->execute();
            return $this->$db->fetch();
    
        } catch(DataTablesException $e) {
    
            throw new DataTablesException($e);
    
        }
    }    
    
}
    