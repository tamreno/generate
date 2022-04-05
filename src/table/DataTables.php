<?php
/**
 * @package tamreno\generate\table
 * @author: Tam Bieszczad
 * @license: Apache License 2.0
 */
namespace tamreno\generate\table;

/**
 * Constructs the DataTables javascript for use with the Tables.
 */
class DataTables
{
    /** @var string $_Scripts The javascript code to place in the head of the 
     * web page. */
    public $_Scripts;
    
    /** @var string $_dtScript */
    private $_dtScript;
    
    /** @var string $_attribute The table's attribute type to key off of ('id' 
     * or 'class') */
    private $_attribute;
    
    /** @var string $_name The value of the table's id or class attribute */
    private $_name;

    /**
     * Instantiates the DataTables object.
     * @param string $attribute The attribute type ('id' or 'class')
     * @param string $name The value of the id or class attribute
     */
    public function __construct($attribute, $name){
        $this->_attribute = $attribute;
        $this->_name = $name;
        $this->_dtScript['order'] = '
                "order":[]';
    }
    
    /**
     * Sets which columns will not be sortable. Zero indexed.
     * @param mixed $options
     */
    public function setNoSort($options){
        $this->_dtScript['noSort'] = '
                //Disable sorting on these columns (begin with 0)
                "columnDefs": [ ';
        if(is_array($options)){
            $x = 0;
            foreach($options as $def){
                $this->_dtScript['noSort'] .= ($x > 0) ? ', 
                    { "targets": '.$def.', "orderable": false }' : '
                    { "targets": '.$def.', "orderable": false }';
                            ++$x;
            }
        }else{
            $this->_dtScript['noSort'] .= '
                    { "targets": '.$options.', "orderable": false }';
        }
        $this->_dtScript['noSort'] .= '
                ]';
    }
    
    /**
     * Sets the option list of how many rows to show per page.
     * @param array $options
     */
    public function setRowsPerPage(array $options){
        $this->_dtScript['rowsPerPage'] = '
                //Determine the options on the pagination selector for number of rows to display';
        $_nums = '';
        foreach($options as $v){
            $_nums .= $v.', ';
        }
        $this->_dtScript['rowsPerPage'] .= '
                "lengthMenu": [['. $_nums .'-1], ['. $_nums .'"All"]]';
    }
    
    /**
     * Removes all of the "flip" controls from the datatable.
     */
    public function setNoControls(){
        $this->_dtScript['controls'] = '
                //Exclude the search box, length, information and pagination ("flip") from the sDOM
                "sDom": \'<"top">rt<"bottom"><"clear">\'';
    }
    
    /**
     * Code to refresh the table on an ajax call.
     */
    public function refreshAjax(){
        $this->_dtScript['refreshAjax'] = '
                //refreshes table on ajax calls
                "ajax": {
                    "url": "data.json",
                    "dataSrc": "Return"
                }';
    }
    
    /**
     * Sets which controls to include with the datatable. This is only necessary
     * if you don't want all (default), or none (see setNoControls() method).
     * 
     * @param string $flip
     */
    public function setControls($flip){
        //Modify to include the choice of search box, length, information and 
        //pagination ("flip") on the sDOM
        $f = preg_match('/f/', $flip) ? 'f' : '';
        $l = preg_match('/l/', $flip) ? 'l' : '';
        $i = preg_match('/i/', $flip) ? 'i' : '';
        $p = preg_match('/p/', $flip) ? 'p' : '';
        $this->_dtScript['controls'] = '
                //modified table controls on sDOM
                "sDom": \'<"top">'.$f.$l.'rt<"bottom">'.$i.$p.'<"clear">\'';
    }
    
    /**
     * This sets the Jquery script and options for a DataTable by its id value
     * (#myTable by default), or you can iterate the script for multiple, separate
     * id values. These must then also be set within the view (or helper script)
     * to differentiate the separate tables and their possibly different options.
     * 
     * Returns the script for the header of the page.
     * 
     * @return string
     */
    public function getDataTableScript(){
        $_Scripts = self::requiredDataTablesFiles();
        $num = count($this->_dtScript);
        $scriptOptions = '';
        $x = 1;
        foreach($this->_dtScript as $key => $val){
            $scriptOptions .= ($x > 1 && $x <= $num) ? ', ' : '';
            $scriptOptions .= $val;
            ++$x;
        }
        $_Scripts .= '
    <script>
      $(document).ready(function() {
        $("'.$this->_getTableKey().'").DataTable( {
            '.$scriptOptions.'
        } );
      } );
    </script>
    ';        
        return $_Scripts;
    }
    
    /**
     * This is a stripped down script only giving the DataTable jQuery for use
     * INSIDE another jQuery script. Like for AJAX table refreshes.
     * 
     * @return string $_Scripts
     */
    public function getTableForAjax(){
        $num = count($this->_dtScript);
        $scriptOptions = '';
        $x = 1;
        foreach($this->_dtScript as $key => $val){
            $scriptOptions .= ($x > 1 && $x <= $num) ? ', ' : '';
            $scriptOptions .= $val;
            ++$x;
        }
        $_Scripts = '
            $("'.$this->_getTableKey().'").DataTable( {
                '.$scriptOptions.'
            } );
    ';        
        return $_Scripts;
    }
    
    /**
     * Get the table identifier that DataTables will use to key off of.
     * 
     * @return string
     */
    private function _getTableKey(){
        $_key = '';
        switch (strtolower($this->_attribute)) {
            case 'id':
                $_key = '#' . $this->_name;
                break;

            case 'class':
                $_key = '.' . $this->_name;
                break;
        }
        return $_key;
    }
    
    /**
     * These are the required files necessary to make DataTables work. This is 
     * automatically included when you call getDataTableScript() method or 
     * getDataTableScriptByClass() method.
     * 
     * @return string $_Scripts
     */
    public function requiredDataTablesFiles(){
        $_Scripts = '
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
    ';
        return $_Scripts;
    }
}