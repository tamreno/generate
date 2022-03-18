<?php
/**
 * @package tamreno/generate/table
 * @subpackage Column
 * @author: Tam Bieszczad
 * @license 
 */
namespace tamreno\generate\table;

/**
 * A column object within the table.
 */
class Column
{
    /** @var string $_colClass The class attribute for all cells within this 
     * column. */
    private $_colClass;

    /** @var string $_colStyle The style attribute for all cells within this 
     * column. */
    private $_colStyle;

    /** @var string $_dataAttribute Any data attribute for all cells within 
     * this column. */
    private $_dataAttribute;
    
    /**
     * 
     */
    public function setStyle(){
        $_styles = func_get_args();
        $x = 0;
        foreach($_styles as $s){
            $this->_colStyle .= $x > 0 ? ';' : '';
            $this->_colStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * 
     */
    public function setClass(){
        $_classes = func_get_args();
        $x = 0;
        foreach($_classes as $c){
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
    public function setDataAttr($dataAttribute){
        foreach($dataAttribute as $key => $val){
            $this->$_dataAttribute[$key] = $val;
        }
    }
    
    /**
     * Return requested column object value.
     * 
     * @param string $value
     * @return string
     */
    public function get($value){
        if(isset($this->{$value})){
            return $this->{$value};
        }
    }
}