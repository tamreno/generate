<?php
/**
 * @package tamreno\generate\table
 * @author: Tam Bieszczad
 * @license: Apache License 2.0
 */
namespace tamreno\generate\table;

/**
 * A cell object within a row.
 */
class Cell
{
    /** @var string $_text The text content of the table cell.*/
    private $_text;

    /** @var string $_data The data attribute(s) of the table cell.*/
    private $_dataAttribute;
    
    /**
     * Constructor for the table cell.
     * 
     * @param type $cellData
     */
    public function __construct($cellData){
        if(is_array($cellData)){
            $this->_text = $cellData['value'];
            $this->_dataAttribute = preg_match('/^data-([a-zA-Z0-9_-])/', $cellData['data']) ? $cellData['data'] : 'data-'.$cellData['data'];
        } else {
            $this->_text = $cellData;
        }
    }
    
    /**
     * Return requested cell object value.
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