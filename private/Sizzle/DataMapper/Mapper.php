<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataMapper
 * @author     David Squires <dave@bluetopmedia.com>
 * @abstract
 */

namespace Sizzle\DataMapper;
use Sizzle\DataMapper\Db;

/**
 * Abstract Mapper class extends the Database object
 * Mapper's can access individual connections by calling the associated
 * key.
 * 
 * <code>
 * KEY is the index associated to the database found in the config.
 * $this->KEY->fetch();
 * </code>
 * 
 * @category Sizzle
 * @package DataMapper
 * @author   David Squires <dave@bluetopmedia.com>
 * @uses Sizzle\DataMapper\Db
 */

abstract class Mapper extends Db {
    
    /**
     * We call connect() to create database connection(s)
     */
    public function __construct()
    {
       parent::__construct();
       parent::connect();
    }
}