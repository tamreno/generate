<?php
/**
 * @package tamreno\generate\table
 * @author: Tam Bieszczad
 * @license: Apache License 2.0
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
    public function __construct(string $headerText){
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
    public function get(string $value){
        if(isset($this->{$value})){
            return $this->{$value};
        }
    }
}