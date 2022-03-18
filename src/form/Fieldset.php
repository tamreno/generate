<?php
/**
 * @package tamreno/generate/form
 * @subpackage Fieldset
 * @author: Tam Bieszczad
 * @license 
 */
namespace tamreno\generate\form;

/**
 * Creates a Fieldset object.
 * 
 * This is the class that creates an object for all of the "Fieldset" properties.
 */
class Fieldset
{

    /** @var string $_fieldsetID The id attribute of the fieldset element. */
    private $_fieldsetID;
    
    /** @var string $_legend The legend attribute of the fieldset element. */
    private $_legend;
    
    /** @var string $_fieldsetStyle The style attribute of the fieldset element. */
    private $_fieldsetStyle;
    
    /** @var string $_fieldsetClass The class attribute of the fieldset element. */
    private $_fieldsetClass;
    
    /** @var array $field An array of Field objects within the fieldset. */
    public $field;
    
    /**
     * The constructor for the Fieldset object.
     * 
     * @param string $fieldsetID The id to be attributed to the fieldset.
     * @param string $legend The text to be assigned to the "legend" element.
     * 
     * @return void
     */
    public function __construct(string $fieldsetID, string $legend = null){
        $this->_fieldsetID = $fieldsetID;
        $this->_legend = $legend ?? null;
    }
    
    /**
     * Set the legend for the fieldset. If it already has a value this will 
     * overwrite it.
     * 
     * @param string $legend
     * 
     * @return void
     */
    public function setLegend(string $legend = null){
        $this->_legend = $legend;
    }

    /**
     * Set any style(s) for the fieldset element. This will overwrite any styles
     * if already set.
     * 
     * @param mixed $styles,... List of one or more styles.
     * 
     * @return void
     */
    public function setStyle(...$styles){
        $this->_fieldsetStyle = '';
        $x = 0;
        foreach($styles as $s){
            $this->_fieldsetStyle .= $x > 0 ? ';' : '';
            $this->_fieldsetStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * Add any style(s) for the fieldset.
     * 
     * @param mixed $styles,... List of one or more styles.
     * 
     * @return void
     */
    public function addStyle(...$styles){
        $x = 0;
        foreach($styles as $s){
            $this->_fieldsetStyle .= $x > 0 || !empty($this->_fieldsetStyle) ? ';' : '';
            $this->_fieldsetStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * Set any class(es) for the fieldset. This will overwrite any classes if
     * already set.
     * 
     * @param mixed $classes,... List of one or more classes.
     * 
     * @return void
     */
    public function setClass(...$classes){
        $this->_fieldsetClass = '';
        $x = 0;
        foreach($classes as $c){
            $this->_fieldsetClass .= $x > 0 ? ' ' : '';
            $this->_fieldsetClass .= $c;
            ++$x;
        }
    }
    
    /**
     * Add any class(es) for the fieldset.
     * 
     * @param mixed $classes,... List of one or more classes.
     * 
     * @return void
     */
    public function addClass(...$classes){
        $x = 0;
        foreach($classes as $c){
            $this->_fieldsetClass .= $x > 0 || !empty($this->_fieldsetClass) ? ' ' : '';
            $this->_fieldsetClass .= $c;
            ++$x;
        }
    }
    
    /**
     * Add a new field to the fieldset.
     * 
     * @param array $params
     * 
     * @return void
     */
    public function addField(array $params){
        $_num = !empty($this->field) ? count($this->field) + 1 : 1;
        $this->field[$_num] = new \tamreno\generate\form\Field($params);
    }
    
    /**
     * Return requested field object value.
     * 
     * @param string $value
     * @return string
     * 
     * @return void
     */
    public function get(string $value){
        if(isset($this->{$value})){
            return $this->{$value};
        }
    }
}