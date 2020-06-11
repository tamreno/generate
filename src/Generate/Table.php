<?php
/**
 * @package Tam-Reno/Jenr8it
 * @subpackage Table
 * @author: Tam Bieszczad
 * @license 
 */

/**
 * Generate the HTML markup for a table
 */
class Generate_Table
{
    /**#@+
     * @access private
     * @var string 
     */
    private $_tableID;
    private $_tableClass;
    private $_tableStyle;
    /**#@-*/
    
    /**#@+
     * @access private
     * @var array 
     */        
    private $_rows;
    /**#@-*/

    /**#@+
     * @access public
     * @var array 
     */        
    public $header;
    public $column;
    /**#@-*/

    public function __construct($tableID = null)
    {
        $this->_tableID = $tableID ?? null;
    }
    
    public function setHeader()
    {
        //Get all parameters passed to setHeader
        $_headers = func_get_args();
        foreach($_headers as $h)
        {
            $this->header[] = new header($h);
            $this->column[] = new column;
        }
    }

    public function setStyle()
    {
        $_styles = func_get_args();
        $x = 0;
        foreach($_styles as $s)
        {
            $this->_tableStyle .= $x > 0 ? ';' : '';
            $this->_tableStyle .= $s;
            ++$x;
        }
    }
    
    public function setClass()
    {
        $_classes = func_get_args();
        $x = 0;
        foreach($_classes as $c)
        {
            $this->_tableClass .= $x > 0 ? ' ' : '';
            $this->_tableClass .= $c;
            ++$x;
        }
    }
    
    public function ignoreDataColumns()
    {
        $_ignoreCols = func_get_args();
        $x = 0;
        foreach($_ignoreCols as $i)
        {
            $this->_ignoreDataColumns[$i] = true;
        }
    }
    
    /**
     * To manually set a row of data in the event the data needs to be manipulated
     * between what is retrieved from the db and what is presented in the table.
     */
    public function setRow()
    {
        $_rowData = func_get_args();
        $this->_rows[] = new row($_rowData);
    }
    
    public function generate($data = null)
    {
        if(!empty($data))
        {
            $this->processData($data);
        }
        
        $_tableID = !empty($this->_tableID) ? ' id="'. $this->_tableID .'"' : '';
        $_tableClass = !empty($this->_tableClass) ? ' class="'. $this->_tableClass .'"' : '';
        $_tableStyle = !empty($this->_tableStyle) ? ' style="'. $this->_tableStyle .'"' : '';
        $_HTML = '
    <table '. $_tableID . $_tableClass . $_tableStyle .'>';
        if(!empty($this->header))
        {
            $_HTML .= '
      <thead>
        <tr>';
            foreach($this->header as $h)
            {
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
        if(!empty($this->_rows))
        {
            foreach($this->_rows as $row)
            {
                $_HTML .= '
        <tr>';
                $x = 0;
                foreach($row as $cells)
                {
                    foreach($cells as $cell)
                    {
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
    
    private function processData($data = null)
    {
        if(!empty($data))
        {
            $_newRow = array();
            foreach($data as $row)
            {
                if(!empty($this->_ignoreDataColumns))
                {
                    $x = 0;
                    foreach($row as $key => $val)
                    {
                        if(empty($this->_ignoreDataColumns[$key]))
                        $_newRow[$x] = $val;
                        ++$x;
                    }
                }
                else
                {
                    $_newRow = $row;
                }
                $this->_rows[] = new row($_newRow);
                if(empty($this->column))
                {
                    $x = 0;
                    while($x < count($row))
                    {
                        $this->column[$x] = new column;
                        ++$x;
                    }
                }
            }
        }
    }
}

/**
 * A header object within the table
 */
class header
{
    /**#@+
     * @access private
     * @var string 
     */
    private $_headerName;
    private $_headerStyle;
    private $_headerClass;
    /**#@-*/

    public function __construct($headerText)
    {
        $this->_headerName = $headerText;
    }
    
    public function setStyle()
    {
        $_styles = func_get_args();
        $x = 0;
        foreach($_styles as $s)
        {
            $this->_headerStyle .= $x > 0 ? ';' : '';
            $this->_headerStyle .= $s;
            ++$x;
        }
    }
    
    public function setClass()
    {
        $_classes = func_get_args();
        $x = 0;
        foreach($_classes as $c)
        {
            $this->_headerClass .= $x > 0 ? ' ' : '';
            $this->_headerClass .= $c;
            ++$x;
        }
    }

    /**
     * Return requested header object value.
     * 
     * @param string $value
     * @return string
     */
    public function get($value)
    {
        if(isset($this->{$value}))
        {
            return $this->{$value};
        }
    }
}

/**
 * A column object within the table.
 */
class column
{
    /**#@+
     * @access private
     * @var string 
     */
    private $_colClass;
    private $_colStyle;
    private $_dataAttribute;
    /**#@-*/
    
    public function setStyle()
    {
        $_styles = func_get_args();
        $x = 0;
        foreach($_styles as $s)
        {
            $this->_colStyle .= $x > 0 ? ';' : '';
            $this->_colStyle .= $s;
            ++$x;
        }
    }
    
    public function setClass()
    {
        $_classes = func_get_args();
        $x = 0;
        foreach($_classes as $c)
        {
            $this->_colClass .= $x > 0 ? ' ' : '';
            $this->_colClass .= $c;
            ++$x;
        }
    }
    
    /**
     * This is used for Tablesorter to be able to order a column by something other
     * than what is in the data cell using the 'data-order' parameter.
     * 
     * @param array $dataAttribute
     */
    public function setDataAttr($dataAttribute)
    {
        foreach($dataAttribute as $key => $val)
        {
            $this->$_dataAttribute[$key] = $val;
        }
    }
    
    /**
     * Return requested column object value.
     * 
     * @param string $value
     * @return string
     */
    public function get($value)
    {
        if(isset($this->{$value}))
        {
            return $this->{$value};
        }
    }
    
}

/**
 * A row object within the table.
 */
class row
{
    /**#@+
     * @access public
     * @var array 
     */        
    public $cell;
    /**#@-*/
    
    public function __construct($rowData)
    {
        foreach($rowData as $cell)
        {
            $this->cell[] = new Cell($cell);
        }
    }
    
    /**
     * Return requested row object value.
     * 
     * @param string $value
     * @return string
     */
    public function get($value)
    {
        if(isset($this->{$value}))
        {
            return $this->{$value};
        }
    }
}

/**
 * A cell object within a row.
 */
class cell
{
    /**#@+
     * @access private
     * @var string 
     */
    private $_text;
    private $_data;
    /**#@-*/
    
    public function __construct($cellData)
    {
        if(is_array($cellData))
        {
            $this->_text = $cellData['value'];
            $this->_data = preg_match('/^data-([a-zA-Z0-9_-])/', $cellData['data']) ? $cellData['data'] : 'data-'.$cellData['data'];
        }
        else
        {
            $this->_text = $cellData;
        }
    }
    
    /**
     * Return requested cell object value.
     * 
     * @param string $value
     * @return string
     */
    public function get($value)
    {
        if(isset($this->{$value}))
        {
            return $this->{$value};
        }
    }
}