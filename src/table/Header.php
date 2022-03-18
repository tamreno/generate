<?php
/**
 * @package tamreno/generate/table
 * @subpackage Header
 * @author: Tam Bieszczad
 * @license 
 */
namespace tamreno\generate\table;

/**
 * A header object within the table
 */
class Header
{
    /** @var string $_headerName */
    private $_headerName;

    /** @var string $_headerStyle */
    private $_headerStyle;

    /** @var string $_headerClass */
    private $_headerClass;

    /**
     * 
     * @param string $headerText
     */
    public function __construct($headerText){
        $this->_headerName = $headerText;
    }
    
    /**
     * Set the style attribute of this header cell.
     */
    public function setStyle(){
        $_styles = func_get_args();
        $x = 0;
        foreach($_styles as $s){
            $this->_headerStyle .= $x > 0 ? ';' : '';
            $this->_headerStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * Set the class attribute of this header cell.
     */
    public function setClass(){
        $_classes = func_get_args();
        $x = 0;
        foreach($_classes as $c){
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
    public function get($value){
        if(isset($this->{$value})){
            return $this->{$value};
        }
    }
}