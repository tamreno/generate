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

    public $_Scripts;
    
    /** @var string $_dtScript */
    private $_dtScript;

    /**
     * Instantiates the DataTables object.
     */
    public function __construct(){
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
    public function setRowsPerPage($options){
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
     * @param string $id
     * @return string
     */
    public function getDataTableScript($id = 'myTable'){
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
        $("#'.$id.'").DataTable( {
            '.$scriptOptions.'
        } );
      } );
    </script>
    ';        
        return $_Scripts;
    }
    
    /**
     * Instead of setting the options for a DataTable by its id (#myTable by 
     * default) this will set the options for all DataTables on a page with the 
     * same class (.dataTable by default). This way you won't have to iterate the
     * same options over multiple ID values if they are going to be the same.
     * 
     * Returns the script for the header of the page.
     *
     * @param string $class
     * @return string
     */
    public function getDataTableScriptByClass($class = 'dataTable'){
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
        $(".'.$class.'").DataTable( {
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
    public function getTableForAjax($id = 'myTable'){
        $num = count($this->_dtScript);
        $scriptOptions = '';
        $x = 1;
        foreach($this->_dtScript as $key => $val){
            $scriptOptions .= ($x > 1 && $x <= $num) ? ', ' : '';
            $scriptOptions .= $val;
            ++$x;
        }
        $_Scripts = '
            $("#'. $id .'").DataTable( {
                '.$scriptOptions.'
            } );
    ';        
        return $_Scripts;
    }
    
    /**
     * This is a stripped down script only giving the DataTable jQuery for use
     * INSIDE another jQuery script. Like for AJAX table refreshes. 
     * 
     * @param string $class
     * @return string
     */
    public function getTableForAjaxByClass($class = 'dataTable'){
        $num = count($this->_dtScript);
        $scriptOptions = '';
        $x = 1;
        foreach($this->_dtScript as $key => $val){
            $scriptOptions .= ($x > 1 && $x <= $num) ? ', ' : '';
            $scriptOptions .= $val;
            ++$x;
        }
        $_Scripts = '
            $(".'. $class .'").DataTable( {
                '.$scriptOptions.'
            } );
    ';        
        return $_Scripts;
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