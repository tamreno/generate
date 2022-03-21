<?php
/**
 * @package tamreno/generate/table
 * @author: Tam Bieszczad
 * @license: Apache License 2.0
 */
namespace tamreno\generate\table;

/**
 * A row object within the table.
 */
class Row
{
    /** @var array $cell An array of cell objects. */        
    public $cell;

    /**
     * Constructor for the Row object.
     * 
     * @param array $rowData
     */
    public function __construct($rowData){
        foreach($rowData as $cell){
            $this->cell[] = new \tamreno\generate\table\Cell($cell);
        }
    }
    
    /**
     * Return requested row object value.
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