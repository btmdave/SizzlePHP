<?php
/**
 * Sizzle Framework
 *
 * @category   Sizzle
 * @package    DataTables
 * @author     David Squires <dave@bluetopmedia.com>
 */

namespace Sizzle\DataTables;
use Sizzle\DataTables\ColumnCollection,
    Sizzle\DataTables\Column,
    Sizzle\DataTables\ColumnCallback,
    Sizzle\DataTables\DataTableMapper,
    Sizzle\Controller\Request,
    Sizzle\Helper\Arrays;

/**
 *
 * DataTable
 *
 * @category   Sizzle
 * @package    DataTables
 * @author   David Squires <dave@bluetopmedia.com>
 * @uses Sizzle\DataTables
 * @uses Sizzle\Controller\Request
 * @uses Sizzle\Helper\Arrays
 */
class DataTable extends DataTableMapper{
    
    /**
     * ColumnCollection
     * @var ColumnCollection
     */
    protected $columns;
    
    /**
     * Ajax Source URL
     * @var string
     */
    protected $source;
    
    /**
     * Database Table
     * @var string
     */
    protected $from;
    
    /**
     * JOINS for SQL query
     * @var array
     */
    protected $joins  = array();
    
    /**
     * WHERE clauses
     * @var array
     */
    protected $wheres = array();

    /**
     * GROUP BY
     * @var string
     */
    protected $group_by;
    
    /**
     * ORDER BY
     * @var string
     */
    protected $order_by;
    
    /**
     * ASC|DESC
     * @var string
     */
    protected $order_by_direction;
    
    /**
     * LIMIT start
     * @var int
     */
    protected $limit_offset   = 0;
    
    /**
     * LIMIT end
     * @var int
     */
    protected $limit_rowcount = 10;
    
    /**
     * Search term being requested
     * @var string
     */
    protected $search_term    = '';
    
    /**
     * Table's default CSS class 
     * @var string
     */
    private  $default_class   = 'data-table';
    
    /**
     * Additional custom CSS class assigned to table
     * @var string
     */
    protected $table_class;
    
    /**
     * The display length for the table (10,25,50,100)
     * @var string
     */
    protected $display_length = 10;  

    /**
     * Dom ID assigned to table
     * @var string
     */
    protected $table_id;
    
    /**
     * FetchAll()
     * @var array
     */
    protected $result;

    /**
     * Sql used for datatable
     * @var string
     */
    private $sql;
    
    
    /**
     * Initilize the ColumnCollection and call parent construct of DataTableMapper
     */
    public function __construct()
    {
        parent::__construct();
        $this->columns = new ColumnCollection();
    }

    /**
     * Key associated to database instance within the config
     * @param string $db
     * @return \Sizzle\DataTables\DataTable
     */
    public function setDatabase($db)
    {
        parent::$db = $db;
        return $this;
    }
    
    /**
     * setTableId
     * @param string $id
     * @return \Sizzle\DataTables\DataTable
     */
    public function setTableId($id)
    {
        $this->table_id = $id;
        return $this;
    }

    /**
     * setDisplayLength
     * @param int $id
     * @return \Sizzle\DataTables\DataTable
     */
    public function setDisplayLength($length)
    {
    	$this->display_length = $length;
    	return $this;
    }    
    
    /**
     * setTableClass
     * @param string $class
     * @return \Sizzle\DataTables\DataTable
     */
    public function setTableClass($class)
    {
        $this->table_class = $class;
        return $this;
    }
    
    /**
     * addColumn
     * @param string $columns
     * @return \Sizzle\DataTables\DataTable
     */
    public function addColumn($columns)
    {
        $this->columns->add($columns);
        return $this;
    }
    
    /**
     * setAjaxSource
     * @param string $source
     * @return \Sizzle\DataTables\DataTable
     */
    public function setAjaxSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * setFrom
     * @param string $from
     * @return \Sizzle\DataTables\DataTable
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }
    
    /**
     * setJoin
     * @param string $join
     * @param string $type
     * @return \Sizzle\DataTables\DataTable
     */
    public function setJoin($join, $type = 'INNER')
    {
        $this->joins[] = array('join' => $join, 'type' => $type);
        return $this;
    }
    
    /**
     * setWhere
     * @param string $where
     * @return \Sizzle\DataTables\DataTable
     */
    public function setWhere($where)
    {
        $this->wheres[] = $where;
        return $this;
    }

    /**
     * setGroupBy
     * @param string $group_by
     * @return \Sizzle\DataTables\DataTable
     */
    public function setGroupBy($group_by)
    {

    	$this->group_by = ' '.$group_by.' ';
        return $this;
    }   
    
    /**
     * setOrderBy
     * @param string $order_by
     * @return \Sizzle\DataTables\DataTable
     */
    public function setOrderBy($order_by)
    {
        $this->order_by = ' '.$order_by.' ';
        return $this;
    }
    
    /**
     * setOrderByDirection
     * @param string $direction
     * @return \Sizzle\DataTables\DataTable
     */
    public function setOrderByDirection($direction)
    {
        $this->order_by_direction = $direction;
        return $this;
    }
    
    /**
     * setSearchTerm
     * @param string $term
     * @return \Sizzle\DataTables\DataTable
     */
    public function setSearchTerm($term = '')
    {
        $this->search_term = $term;
        return $this;
    }    
    
    /**
     * setLimit
     * @param int $offset
     * @param int $rowcount
     * @return \Sizzle\DataTables\DataTable
     */
    public function setLimit($offset, $rowcount)
    {
        $this->limit_offset   = $offset;
        $this->limit_rowcount = $rowcount;
        return $this;
    }
    
    /**
     * Generate table's DOM and JavaScript for displaying within the View
     * 
     * Example:
     * <code>
     *  //In the controller
     *  $this->View->table = $datatable->generateTable();
     *  
     *  //Now in the view
     *  <?=(isset($this->table)) ? $this->table : ''?> 
     * </code>
     * 
     * @return string
     */
    public function generateTable()
    {
         
       $table        = '<table class="'.$this->default_class.' '.$this->table_class.'" id="'.$this->table_id.'">';
       $table        .= '<thead><tr>';

        foreach($this->columns as $key => $column)
        {
        	if($column->getIsHidden()) {
        		continue;
        	}
        	
            $class = ($column->getEditable()) ? 'th_editable' : '';
            $table    .= '<th class="'.$class.'" width="'.$column->getWidth().'">'.$column->getTitle().'</th>';
        }  
       $rows = $table;
       $table .= "</tr></thead>";
       $table .= "<tbody></tbody";

       $table .= "</table>";
       
       $js = $this->getJavaScript();
       
       return $table.$js;

    }
    
    /**
     * Build our JavaScript
     * @return string
     */
    private function getJavaScript()
    {
        
        $sortable = '';
        $sorting  = '';
        $editable = '';
        $index    = 0;
   
        foreach($this->columns as $key => $column)
        {
        	
        	if($column->getIsHidden()) {
        		continue;
        	}
        	
            if($key++){
                $sortable .= ', ';
                $sorting .= ', ';
            }
            
            if($column->getIsSortable()) {
                $sortable .= '{"bSortable": true, "asSorting": ["desc","asc"], "aTargets": ['.$index.']}';
            } else {
                $sortable .= '{"bSortable": false, "asSorting": ["desc","asc"], "aTargets": ['.$index.']}';
            }

            $sorting .= '[ '.$index.', "'.$column->getSortOrder().'" ]';
          
            
            if($column->getEditable()) {
                $editable .= $column->getEditable();
            }
            
            $index++;
           
        }
        
        $js = '
            <script type="text/javascript">
                var oDataTable = $(".'.$this->default_class.'").dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "bDeferRender": true,
                    "iDisplayLength": '.$this->getDisplayLength().',
                    "sAjaxSource": "'.$this->source.'",
                    "aoColumnDefs": [
				      '.$sortable.'
				    ],
				    "aaSorting": ['.$sorting.'],
                	"fnDrawCallback": function( oSettings ) {
                	    '.$editable.'
                	    $(".editable").closest("td").addClass("td_editable");
                    }
                });
            </script>';
        
        return $js;
        
    }
    
    /**
     * Echo the JSON object to be used as the ajaxSource for the table.
     * We exit() out after we echo the JSON.  If we're calling this method, we don't 
     * want to go any further in the application.
     * @uses $this->executeQuery();
     */
    public function getJson()
    {
        
        $request = new Request;
        if($request->isGet()) {
   
            $this->setSearchTerm($request->sSearch)
                 ->setOrderBy($request->iSortCol_0)
                 ->setOrderByDirection($request->sSortDir_0)
                 ->setLimit($request->iDisplayStart, $request->iDisplayLength);
        }
        
        $this->result = $this->executeQuery();
        $total        = $this->executeCountQuery();
        
        $array = array();
        if(!empty($this->result)) {
           
            foreach($this->result as $key => $result_set)
            {
                
                $array[$key] = array();
                
                $i = 0;
                foreach($result_set as $item)
                {
                    $callback = false;
                    $editable = false;

                    //We perform checks on our data before we render
                    if($this->columns->get($i)) {
                        
                        $column = $this->columns->get($i);
                        if($column->getIsPrimaryKey()) {
                            $id = $item;
                        }
                        
                        if($column->getIsHidden()) {
                        	continue;
                        }
                        
                        //Process any defined callback functions
                        if($column->getCallback()) {
                        
                            $callback = $column->getCallback();
                            $array[$key][$i] = $callback($result_set);
                            $callback = true;
                        }
  
                        //Wrap around an editable field if isEditable()
                        if($column->getEditable()) {
                        
                            //We'll need the primary key as ID in order to know what to update.
                            //The primary key, should be set as 'PRIMARY_KEY as id'
                            $item = (isset($array[$key][$i])) ? $array[$key][$i] : $item;
                            $array[$key][$i] = '<div class="editable editable_'.$column->getName().'" id="'.$id.'">'.$item.'</div>';
                            $editable = true;
                            
                        }
                       
                    }
                    
                    if(!$callback && !$editable){
                        $array[$key][$i] = $item;
                    }
                    
                    $i++;
                }
            }
        }

        $json = '{
        "iTotalRecords"        : '.$total.',
        "iTotalDisplayRecords" : '.$total.',
        "aaData": '.json_encode($array).'}';
        echo $json;
        exit();
    }
   
    /**
     * We build our SQL and execute it using getResults()
     * @see DataTableMapper\getResults()
     * @return array
     */
    private function executeQuery()
    {
        
        $this->sql = "SELECT ";
        $this->sql .= $this->getColumns();
        $this->sql .= " FROM ".$this->from;   
        $this->sql .= $this->getJoins();
        $this->sql .= $this->getWhere();
        $this->sql .= $this->getGroupBy();
        $this->sql .= $this->getOrderBy();
        $this->sql .= $this->getLimit();
        $result = $this->getResults($this->sql);
        return $result;
        
    }
    
    /**
     * Count number or records for use in pagination
     * @return int
     */
    private function executeCountQuery()
    {
        $sql = "SELECT count(*) as count";
        $sql .= " FROM ".$this->from;
        $sql .= $this->getJoins();
        $sql .= $this->getWhere();
        $sql .= $this->getGroupBy();
        $sql .= $this->getOrderBy();
        $result = $this->getResultCount($sql);
        return empty($result['count']) ? 0 : $result['count'];
    }
    
    /**
     * Returns the LIMIT syntax used in our SQL
     * @return string
     */
    private function getLimit()
    {
        return ' LIMIT '.$this->limit_offset.','.$this->limit_rowcount;
    }
    
    /**
     * Returns the LIMIT syntax used in our SQL
     * @return string
     */
    private function getJoins()
    {
    	if(!empty($this->joins)) {
    		$joins = ' ';
    		foreach($this->joins as $key => $join) {
   
    			$joins .= $join['type'].' JOIN '.$join['join'].' ';
    		}
    		
    		return $joins;
    	}

    }   
    
    /**
     * Returns the GROUP BY syntax used in our SQL
     * @return boolean|string
     */
    private function getGroupBy()
    {
        if(empty($this->group_by)) {
           return false; 
        }
        
        return ' GROUP BY '.$this->group_by;
    }
 
    /**
     * Builds our WHERE clauses
     * @return string
     */
    private function getWhere()
    {

        if (empty($this->wheres)) {
            
            $search = $this->getSearch();
            if (!empty($search)) {
                return ' WHERE '.$search;
            }
             
            return ' ';
        }
        
        $wheres  = ' WHERE ';
        $operators = array('AND','OR');
        foreach($this->wheres as $key => $where)
        {
            if($key++){ 
                if(Arrays::strstr_array($operators, $where)) {
                    $wheres .= ' AND ';
                }
            }
            $wheres .= $where.' ';
        }
        
        $search = $this->getSearch();
        if (!empty($search)) {
            $wheres  .= ' AND '.$search;
        }
        
        return $wheres;
    }
    

    /**
     * Get the SQL string for search functionality
     * @return string
     */
    private function getSearch()
    {
            $search = '';
            if($this->search_term) {
                foreach($this->columns as $key => $column)
                {
                    if($column->getIsSearchable()) {
                    	
						$name = ($column->getCustomName()) ? $column->getCustomName() : $column->getName();
	
                        $search		.= $name." LIKE '%".$this->search_term."%'";
                    }

                    $num = $key + 1;
                    $col = $this->columns->get($num);
                    if($col && $col->getIsSearchable()){
                        if($search !== '') {
                            $search .= ' OR ';
                        }
                    }
                }
            
            }

            return $search; 
    }
    
    /**
     * Get the ORDER BY sql
     * @return string
     */
    private function getOrderBy()
    {

        foreach($this->columns as $key => $column) {
            if($key == $this->order_by) {
            	$name = ($column->getCustomName()) ? $column->getCustomName() : $column->getName();
                $order_by = 'ORDER BY '.$name.' '.$this->order_by_direction;
                return $order_by;
            }
        }

    }
    
    /**
     * getColumns
     * @return string
     */
    private function getColumns()
    {
        $columns = '';
        foreach($this->columns as $key => $column)
        {
            if($key++){
                $columns .= ', ';
            }
            $columns.= $column->getName();
        }  
        
        return $columns;
    }
    
    /**
     * getDisplayLength
     * @return int
     */
    private function getDisplayLength()
    {
    	return $this->display_length;
    }
    
    /**
     * getSql
     * @return string
     */
    public function getSql()
    {
    	return $this->sql;
    }
    
    

}