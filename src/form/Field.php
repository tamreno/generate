<?php
/**
 * @package tamreno\generate\form
 * @author: Tam Bieszczad
 * @license: Apache License 2.0 
 */
namespace tamreno\generate\form;

/**
 * Creates the "Field" object.
 */
class Field
{
    /** @var string $_fieldID The id of the field element. */
    private $_fieldID;
    
    /** @var string $_name The name of the field element. */
    private $_name;
    
    /** @var string $_fieldStyle The style attribute of this input element. */
    private $_fieldStyle;
    
    /** @var string $_fieldClass The class attribute of this input element. */
    private $_fieldClass;
    
    /** @var string $_label The text of the label element. */
    private $_label;
    
    /** @var string $_labelClass The class attribute of the label element. */
    private $_labelClass;
    
    /** @var string $_lableStyle The style attribute of the label element. */
    private $_labelStyle;
    
    /** @var string $_inputType The input type of this form field. */
    private $_inputType;
    
    /** @var string $_title The title attribute of this field element. */
    private $_title;
    
    /** @var mixed $_value The value of this input field. */
    private $_value;
    
    /** @var string $_error Error message for this field. */
    private $_error;
    
    /** @var string $_wrapType The type of element used to wrap the field element. */
    private $_wrapType = 'div';
    
    /** @var string $_wrapClass The class attribute of the wrapper element for 
     * this field. */
    private $_wrapClass;
    
    /** @var string $_wrapStyle The style attribute of the wrapper element for
     * this field. */
    private $_wrapStyle;
    
    /** @var array $_attributes An array of attributes to assign to this field. */        
    private $_attributes;
    
    /** @var boolean $_floadLabel Determines whether this field will use a 
     * floating label. This determines the placement of the label relative to 
     * the field itself. */        
    private $_floatLabel = false;

    /**
     * The constructor of a Field object.
     * 
     * @param array $details The Field object's attributes to be assigned.
     * 
     * @return void
     */
    public function __construct(array $details){
        $this->setAttributes($details);
        //If the type is "paragraph" then is doesn't need a name.
        $this->_name = $details['name'] ?? null;
        //If it doesn't have a _fieldID, then give it the same as its _name.
        $this->_fieldID = $this->_fieldID ?? preg_replace('/[\W!-]/', '', $this->_name);
    }
    
    /**
     * Set a list of attributes from array.
     * 
     * @param array $attributeList
     * 
     * @return void
     */
    public function setAttributes(array $attributeList){
        foreach($attributeList as $attribute => $val){
            $this->setAttribute($attribute, $val);
        }
    }
    
    /**
     * Set an attribute for the input field, label or wrapper.
     * 
     * @param string $attribute The attribute of the field, label or wrapper.
     * @param mixed $val The value of the attribute.
     * 
     * @return void
     */
    public function setAttribute(string $attribute, $val){
        switch(strtolower($attribute)){
            case 'type':
                $this->_inputType = $val;
                break;
            case 'id':
                $this->setId($val);
                break;
            case 'floatlabel':
                $this->floatLabel($val);
                break;
            case 'value':
                $this->setValue($val);
                break;
            case 'error':
                $this->setError($val);
                break;
            case 'title':
                $this->setTitle($val);
                break;
            case 'style':
                call_user_func_array(array($this,"setStyle"), (array)$val);
                break;
            case 'class':
                call_user_func_array(array($this,"setClass"), (array)$val);
                break;
            case 'wrapclass':
                call_user_func_array(array($this,"setWrapClass"), (array)$val);
                break;
            case 'wrapstyle':
                call_user_func_array(array($this,"setWrapStyle"), (array)$val);
                break;
            case 'labelclass':
                call_user_func_array(array($this,"setLabelClass"), (array)$val);
                break;
            case 'labelstyle':
                call_user_func_array(array($this,"setLabelStyle"), (array)$val);
                break;
            default:
                $_name = '_'.$attribute;
                $this->{$_name} = $val;
                break;
        }
    }
    
    /**
     * Set the label for this form field.
     * 
     * @param String $label
     * 
     * @return void
     */
    public function setLabel(string $label = null){
        $this->_label = $label;
    }

    /**
     * Set any classes for the field label. This will overwrite any classes if
     * already set.
     * 
     * @param mixed $classes,... List of one or more classes.
     * 
     * @return void
     */
    public function setLabelClass(...$classes){
        $this->_labelClass = '';
        $x = 0;
        foreach($classes as $c){
            $this->_labelClass .= $x > 0 ? ' ' : '';
            $this->_labelClass .= $c;
            ++$x;
        }
    }

    /**
     * Set any styles for the field label. This will overwrite any styles if
     * already set.
     * 
     * @param mixed $styles,... List of one or more styles.
     * 
     * @return void
     */
    public function setLabelStyle(...$styles){
        $this->_labelStyle = '';
        $x = 0;
        foreach($styles as $s){
            $this->_labelStyle .= $x > 0 ? ' ' : '';
            $this->_labelStyle .= $s;
            ++$x;
        }
    }

    /**
     * Set the input type for this field.
     * 
     * @param string $type
     * 
     * @return void
     */
    public function setInputType(string $type){
        $this->_inputType = $type;
    }

    /**
     * Set any styles for the field element. This will overwrite any styles if
     * already set.
     * 
     * @param mixed $styles,... List of one or more styles.
     * 
     * @return void
     */
    public function setStyle(...$styles){
        $this->_fieldStyle = '';
        $x = 0;
        foreach($styles as $s){
            $this->_fieldStyle .= $x > 0 ? ';' : '';
            $this->_fieldStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * Add any styles for the field.
     * 
     * @param mixed $styles,... List of one or more styles.
     * 
     * @return void
     */
    public function addStyle(...$styles){
        $x = 0;
        foreach($styles as $s){
            $this->_fieldStyle .= $x > 0 || !empty($this->_fieldStyle) ? ';' : '';
            $this->_fieldStyle .= $s;
            ++$x;
        }
    }
    
    /**
     * Set any classes for the field. This will overwrite any classes if
     * already set.
     * 
     * @param mixed $classes,... List of one or more classes.
     * 
     * @return void
     */
    public function setClass(...$classes){
        $this->_fieldClass = '';
        $x = 0;
        foreach($classes as $c){
            $this->_fieldClass .= $x > 0 ? ' ' : '';
            $this->_fieldClass .= $c;
            ++$x;
        }
    }
    
    /**
     * Add any classes for the field.
     * 
     * @param mixed $classes,... List of one or more classes.
     * 
     * @return void
     */
    public function addClass(...$classes){
        $x = 0;
        foreach($classes as $c){
            $this->_fieldClass .= $x > 0 || !empty($this->_fieldClass) ? ' ' : '';
            $this->_fieldClass .= $c;
            ++$x;
        }
    }
    
    /**
     * Turn on/off the "Float Label" option for this field.
     * 
     * @param boolean $float
     * 
     * @return void
     */
    public function floatLabel(bool $float = true){
        $this->_floatLabel = in_array(strtolower($float), array('no','false','none','off')) ? (false) : (true);
    }
    
    /**
     * Retrieve the requested field property.
     * 
     * @param string $property
     * @return string
     * 
     * @return void
     */
    public function get(string $property){
        if(isset($this->{$property})){
            return $this->{$property};
        }
    }
    
    /**
     * Set the "Value" property.
     * 
     * If this form field has a value already associated and it needs to be put
     * back in the form (prefilled value or carried over from an error, etc.)
     * this will set the form field's value.
     * 
     * @param string $val
     * 
     * @return void
     */
    public function setValue(string $val = null){
        $this->_value = $val;
    }
    
    /**
     * Set an error message to be displayed for this field.
     * 
     * @param string $error Error message.
     * 
     * @return void
     */
    public function setError(string $error = null){
        $this->_error = $error;
    }
    
    /**
     * Set a "title" attribute for this field.
     * 
     * @param string $title
     * 
     * @return void
     */
    public function setTitle(string $title = null){
        $this->_title = $title;
    }
    
    /**
     * Set the type of element for the wrapping container holding the input
     * field and label. (i.e. 'div', 'span', etc.)
     * 
     * @param string $type
     * 
     * @return void
     */
    public function setWrapType(string $type = 'div'){
        $this->_wrapType = $type;
    }

    /**
     * Set any class(es) for the wrapper around the input field and label.
     * 
     * @param mixed $wrapClass,... List of one or more classes for the wrapper.
     * 
     * @return void
     */
    public function setWrapClass(...$wrapClass){
        $this->_wrapClass = '';
        $x = 0;
        foreach($wrapClass as $c){
            $this->_wrapClass .= $x > 0 ? ' ' : '';
            $this->_wrapClass .= $c;
            ++$x;
        }
    }

    /**
     * Add any class(es) for the wrapper around the input field and label.
     * 
     * @param mixed $wrapClass,... List of one or more classes for the wrapper.
     * 
     * @return void
     */
    public function addWrapClass(...$wrapClass){
        $x = 0;
        foreach($wrapClass as $c){
            $this->_wrapClass .= $x > 0 || !empty($this->_wrapClass) ? ' ' : '';
            $this->_wrapClass .= $c;
            ++$x;
        }
    }
    
    /**
     * Set any style(s) for the wrapper around the input field and label.
     * 
     * @param mixed $wrapStyle,... List of one or more styles for the wrapper.
     * 
     * @return void
     */
    public function setWrapStyle(...$wrapStyle){
        $this->_wrapStyle = '';
        $x = 0;
        foreach($wrapStyle as $s){
            $this->_wrapStyle .= $x > 0 ? ' ' : '';
            $this->_wrapStyle .= $s;
            ++$x;
        }
    }

    /**
     * Add one or more styles to the wrapper around the input field and label.
     * 
     * @param mixed $wrapStyle
     */
    public function addWrapStyle(...$wrapStyle){
        if(!empty($wrapStyle)){
            foreach($wrapStyle as $s){
                $this->_wrapStyle .= $s . ';';
            }
        }
    }

    /**
     * Set the id for the input field element.
     * 
     * @param string $id
     * 
     * @return void
     */
    public function setId(string $id){
        $this->_fieldID = $id;
    }
}