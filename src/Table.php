<?php
/**
 * @package tamreno\generate
 * @author: Tam Bieszczad
 * @license: Apache License 2.0
 */
namespace tamreno\generate;

/**
 * Generate the HTML markup for a table
 */
class Table
{
    /** @var string $_tableID The id attribute of the table element. */
    private $_tableID;
    
    /** @var class $_tableClass The class attribute of the table element. */
    private $_tableClass;
    
    /** @var string $_tableStyle The style attribute of the table element. */
    private $_tableStyle;
    
    /** @var array $_rows Array of row objects. */        
    private $_rows;

    /** @var array $header Array of values for the header row. */        
    public $header;
    
    /** @var array $column Array of column objects. */
    public $column;

    /**
     * Instantiates the Table object.
     * 
     * @param string $tableID
     */
    public function __construct($tableID = null){
        $this->_tableID = $tableID ?? null;
    }
    
    /**
     * Set the text for the header row cells.
     */
    public function setHeader(){
        //Get all parameters passed to setHeader
        $_headers = func_get_args();
        if(is_array($_headers[0])){
            $_headers = $_headers[0];
        }
        foreach($_headers as $h){
            $this->header[] = new \tamreno\generate\table\Header($h);
            $this->column[] = new \tamreno\generate\table\Column;
        }
    }

    /**
     * Set the style for the table.
     */
    public function setStyle(){
        $_styles = func_get_args();
        $x = 0;
        foreach($_styles as $s){
            $this->_tableStyle .= $x > 0 ? ';' : '';
            $this->_tableStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * Set the class for the table.
     */
    public function setClass(){
        $_classes = func_get_args();
        $x = 0;
        foreach($_classes as $c){
            $this->_tableClass .= $x > 0 ? ' ' : '';
            $this->_tableClass .= $c;
            ++$x;
        }
    }
    
    /**
     * Set the columns that should be ignored when importing data into the Table.
     */
    public function ignoreDataColumns(){
        $_ignoreCols = func_get_args();
        foreach($_ignoreCols as $i){
            $this->_ignoreDataColumns[$i] = true;
        }
    }
    
    /**
     * Incorporates the DataTables javascript for use with the Table.
     */
    public function datatables(){
        $this->datatables = new \tamreno\generate\table\DataTables();
    }
    
    /**
     * To manually set a row of data in the event the data needs to be manipulated
     * between what is retrieved from the db and what is presented in the table.
     */
    public function setRow(){
        $_rowData = func_get_args();
        $this->_rows[] = new \tamreno\generate\table\Row($_rowData);
    }
    
    /**
     * Generate the HTML for the Table.
     * 
     * @param array|null $data
     * @return string
     */
    public function generate($data = null){
        if(!empty($data)){
            $this->_processData($data);
        }
        
        $_tableID = !empty($this->_tableID) ? ' id="'. $this->_tableID .'"' : '';
        $_tableClass = !empty($this->_tableClass) ? ' class="'. $this->_tableClass .'"' : '';
        $_tableStyle = !empty($this->_tableStyle) ? ' style="'. $this->_tableStyle .'"' : '';
        $_HTML = '
    <table '. $_tableID . $_tableClass . $_tableStyle .'>';
        if(!empty($this->header)){
            $_HTML .= '
      <thead>
        <tr>';
            foreach($this->header as $h){
                $_headerClass = !empty($this->_headerClass) ? ' class="'. $this->_headerClass .'"' : '';
                $_headerStyle = !empty($this->_headerStyle) ? ' style="'. $this->_headerStyle .'"' : '';
                $_HTML .= '
          <th'. $_headerClass . $_headerStyle .'>'. $h->get('_headerName') .'</th>';
            }
            $_HTML .= '
        </tr>
      </thead>';
        }
        $_HTML .= '
      <tbody>';
        if(!empty($this->_rows)){
            foreach($this->_rows as $row){
                $_HTML .= '
        <tr>';
                $x = 0;
                foreach($row as $cells){
                    foreach($cells as $cell){
                        $_cellStyle = !empty($this->column[$x]->get('_colStyle')) ? ' style="'. $this->column[$x]->get('_colStyle') .'"' : '';
                        $_cellClass = !empty($this->column[$x]->get('_colClass')) ? ' class="'. $this->column[$x]->get('_colClass') .'"' : '';
                        /** Need to figure out how to handle data-order **/
                        $_dataAttribute = $cell->get('_dataAttribute') ? ' '.$cell->get('_dataAttribute') : '';
                        $_HTML .= '
          <td'. $_cellClass . $_cellStyle . $_dataAttribute .'>'. $cell->get('_text') .'</td>';
                        ++$x;
                    }
                }
                $_HTML .= '
        </tr>';
            }
        }
        $_HTML .= '
      </tbody>
    </table>';
        return $_HTML;
    }
    
    /**
     * Imports the data into the table.
     * 
     * @param array|null $data
     */
    private function _processData($data = null){
        if(!empty($data)){
            foreach($data as $row){
                $this->_processDataRow($row);
            }
        }
    }
    
    /**
     * Imports a row of data from the processData() method.
     * 
     * @param array $row
     */
    private function _processDataRow($row){
        $_newRow = array();
        //Remove any ignored data
        if(!empty($this->_ignoreDataColumns)){
            foreach($this->_ignoreDataColumns as $i => $mark){
                unset($row[$i]);
            }
        }
        $x = 0;
        foreach($row as $key => $val){
            //check if associative array or not
            if(!is_int($key)){
                //If header is empty, grab the keys as headers
                if(empty($this->header)){
                    $this->setHeader(array_keys($row));
                }
            }
            $_newRow[$x] = $val;
            ++$x;
        }

        $this->_rows[] = new \tamreno\generate\table\Row($_newRow);
        if(empty($this->column)){
            $x = 0;
            while($x < count($row)){
                $this->column[$x] = new \tamreno\generate\table\Column;
                ++$x;
            }
        }
    }
    
    /**
     * Return the table object properties to show in browser. This is for 
     * development and testing purposes.
     * 
     * @return string Display of the entire table object
     * 
     * @return void
     */
    public function showObject(){
        return '<pre>'.print_r($this,true).'</pre>';
    }
}