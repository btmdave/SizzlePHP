<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataTables;
use Sizzle\DataTables\Column;

/**
 * ColumnCollection
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 * @uses       Sizzle\DataTables\Column
 * @uses       Iterator
 */
class ColumnCollection implements \Iterator
{
    /**
     * Holds our column objects
     * @var array of objects
     */
    protected $items = array();
    
    /**
     * Index of a column
     * @var int
     */
    protected $index = 0;

    /**
     * Construct, we define our items array.
     * @param array $items
     */
    public function __construct($items = array())
    {
        $this->items = $items;
    }

    /**
     * Add column to collection
     * @param Column $item
     */
    public function add(Column $item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Get a Column by index value
     * @param  int $index
     * @return Column
     */
    public function get($index)
    {
        if(isset($this->items[$index])) {
            return $this->items[$index];
        }
    }

    /**
     * Count
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->items[$this->index];
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * (non-PHPdoc)
     * @see Iterator::valid()
     */
    public function valid()
    {
        return isset($this->items[$this->index]);
    }
}