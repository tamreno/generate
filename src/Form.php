<?php
/**
 * @package tamreno\generate
 * @author: Tam Bieszczad
 * @license: Apache License 2.0 
 */
namespace tamreno\generate;
use \tamreno\generate\form\buildJquery;

/**
 * Generate a form.
 * 
 * This class will generate the HTML markup for a form from the data supplied to
 * it.
 */
class Form
{
    /** @var string $_formName The name of the form element. */
    private $_formName = 'MyForm';

    /** @var string $_formID The id of the form element. */
    private $_formID = 'MyForm';
    
    /** @var string $_formStyle The style attribute of the form. */
    private $_formStyle;

    /** @var string $_formClass The class attribute of the form. */
    private $_formClass;

    /** @var string $_action The action attribute of the form. */
    private $_action;

    /** @var string $_method The method attribute of the form. */
    private $_method = 'post';
    
    /**
     * @var string $_enctype The enctype of the form.
     * @example 'application/x-www-form-urlencoded' or 'multipart/form-data'.
     */
    private $_enctype;
    
    /** @var boolean $_honeypot Default is to set a honeypot for robots. */
    private $_honeypot = true;
    
    /** @var array $fieldset The array of fieldsets within the form. */        
    public $fieldset;
    
    /** @var array $_fieldlist An array of field names kept for reference. */
    private $_fieldlist;

    /** @var array $_scripts An array of scripts that are associated with this 
     * form's functions. */        
    private $_scripts;

    /**
     * Constructor for the Form object
     * 
     * @param array $params The parameters of the form.
     * 
     * @return self
     */
    public function __construct(array $params = null){
        if(!empty($params)){
            $this->_setInitialAttributes($params);
        }
        $this->newFieldset($params['legend'] ?? null);
        return $this; // for chaining
    }
    
    /**
     * Sets the initial attributes for the Form element if supplied.
     * 
     * @param array $params
     */
    private function _setInitialAttributes($params){
        foreach($params as $key => $val){
            switch(strtolower($key)){
                case 'formname':
                    $this->setFormName($val);
                    break;
                
                case 'formid':
                    $this->setFormId($val);
                    break;
                
                case 'action':
                    $this->setAction($val);
                    break;
                
                case 'method':
                    $this->setMethod($val);
                    break;
                
                case 'enctype':
                    $this->setEnctype($val);
                    break;
                
                case 'class':
                    call_user_func_array(array($this, 'setFormClass'), (array)$val);
                    break;
                
                case 'style':
                    call_user_func_array(array($this, 'setFormStyle'), (array)$val);
                    break;
            }
        }
    }
    
    /**
     * Set the name of the form.
     * 
     * @param string $formName
     */
    public function setFormName(string $formName){
    	$this->_formName = $formName;
    }

    /**
     * Set the id of the form.
     * 
     * @param string $formID
     */
    public function setFormId(string $formID){
    	$this->_formID = $formID;
    }

    /**
     * Set the enctype of the form.
     * 
     * @param string $enctype
     */
    public function setEnctype(string $enctype){
    	$this->_enctype = $enctype;
    }

    /**
     * Set any style(s) for the form element. This will overwrite any styles if
     * already set.
     * 
     * @param mixed $styles,... A list of one or more styles.
     * 
     * @return self
     */
    public function setFormStyle(...$styles){
        //Clear out the styles first, if any
        $this->_formStyle = '';
        if(!empty($styles)){
            foreach($styles as $s){
                $this->_formStyle .= $s.';';
            }
        }
        return $this; // for chaining
    }
    
    /**
     * Add any style(s) for the form element.
     * 
     * @param mixed $styles,... A list of one or more styles.
     * 
     * @return self
    */
    public function addFormStyle(...$styles){
        $x = 0;
        if(!empty($styles)){
            foreach($styles as $s){
                $this->_formStyle .= $s.';';
                ++$x;
            }
        }
        return $this;
    }
    
    /**
     * Set any class(es) to the form element. This will overwrite any classes if
     * already set.
     * 
     * @param mixed $classes,... A list of one or more classes.
     * 
     * @return self
     */
    public function setFormClass(...$classes){
        //Clear out the class info first, if any
        $this->_formClass = '';
        $x = 0;
        foreach($classes as $c){
            $this->_formClass .= $x > 0 ? ' ' : '';
            $this->_formClass .= $c;
            ++$x;
        }
        return $this;
    }
    
    /**
     * Add any class(es) to the form element.
     * 
     * @param mixed $classes,... A list of one or more classes.
     * 
     * @return self
     */
    public function addFormClass(...$classes){
        $x = 0;
        foreach($classes as $c){
            $this->_formClass .= $x > 0 || !empty($this->_formClass) ? ' ' : '';
            $this->_formClass .= $c;
            ++$x;
        }
        return $this;
    }
    
    /**
     * Set the "action" for the form.
     * 
     * @param string $action
     * 
     * @return self
     */
    public function setAction(string $action){
        $this->_action = $action;
        return $this;
    }
    
    /**
     * Set the method ('get'/'post') for the form. The default is 'post'.
     * 
     * @param string $method
     * 
     * @return self
     */
    public function setMethod(string $method){
        $_method = strtolower($method);
        $this->_method = $_method == 'get' || $_method == 'post' ? $_method : $this->_method;
        return $this;
    }
    
    /**
     * Set the 'name' and 'value' attributes for the 'Submit' button element.
     * 
     * @param string $value
     * @param string $name
     * 
     * @return self
     */
    public function setSubmit(string $value, string $name = null){
        $_params = array('type' => 'submit','value' => $value);
        if(!empty($name)){
            $_params['name'] = $name;
            $_params['id'] = $name;
        }
        $this->Submit = new \tamreno\generate\form\Field($_params);
        return $this;
    }
    
    /**
     * Set any class(es) for the Submit Button element. This will overwrite any 
     * classes if already set.
     * 
     * @param mixed $classes,... A list of one or more classes.
     * 
     * @return self
     */
    public function setSubmitClass(...$classes){
        call_user_func_array(array($this->Submit,"setClass"), $classes);
        return $this;
    }
    
    /**
     * Add a new fieldset element.
     * 
     * @param string $legend The legend of the new fieldset
     * 
     * @return self
     */
    public function newFieldset(string $legend = null){
        $_fieldsetNum = !empty($this->fieldset) ? count($this->fieldset) + 1 : 1;
        $this->fieldset[$_fieldsetNum] = new \tamreno\generate\form\Fieldset($_fieldsetNum, $legend);
        return $this;
    }
    
    /**
     * Set any style(s) for the fieldset element. This will overwrite any styles if
     * already set.
     * 
     * @param mixed $styles,... A list of one or more styles
     * 
     * @return self
     */
    public function setFieldsetStyle(...$styles){
        $_num = count($this->fieldset);
        call_user_func_array(array($this->fieldset[$_num],"setStyle"), $styles);
        return $this;
    }
    
    /**
     * Add any style(s) for the fieldset element.
     * 
     * @param mixed $styles,... A list of one or more styles
     * 
     * @return self
     */
    public function addFieldsetStyle(...$styles){
        $_num = count($this->fieldset);
        call_user_func_array(array($this->fieldset[$_num],"addStyle"), $styles);
        return $this;
    }
    
    /**
     * Set any class(es) for the fieldset element. This will overwrite any classes if
     * already set.
     * 
     * @param mixed $classes,... A list of one or more classes
     * 
     * @return self
     */
    public function setFieldsetClass(...$classes){
        $_num = count($this->fieldset);
        call_user_func_array(array($this->fieldset[$_num],"setClass"), $classes);
        return $this;
    }
    
    /**
     * Add any class(es) for the fieldset element.
     * 
     * @param mixed $classes,... A list of one or more classes
     * 
     * @return self
     */
    public function addFieldsetClass(...$classes){
        $_num = count($this->fieldset);
        call_user_func_array(array($this->fieldset[$_num],"addClass"), $classes);
        return $this;
    }
        
    /**
     * Set the legend for the last-added (current) fieldset.
     * 
     * @param string $legend The legend to be attributed to the fieldset
     * 
     * @return self
     */
    public function setLegend(string $legend){
        $_num = count($this->fieldset);
        $this->fieldset[$_num]->setLegend($legend);
        return $this;
    }
    
    /**
     * Add a field to the last-added (current) fieldset.
     * 
     * @param array $fieldInfo Attributes to be assigned to this field
     * 
     * @return self
     */
    public function addField(array $fieldInfo){
        $_fs = $fieldInfo['fieldset'] ?? count($this->fieldset);
        $_fieldset = $this->fieldset[$_fs];
        $_fieldNum = is_array($_fieldset->field) ? count($_fieldset->field) : 0;
        if(empty($fieldInfo['id'])){
            $fieldInfo['id'] = $fieldInfo['name'] ?? 'field-' . $_fs . '-' . ++$_fieldNum;
        }
        if(empty($fieldInfo['name'])){
            $fieldInfo['name'] = $fieldInfo['id'] ?? 'field-' . $_fs . '-' . ++$_fieldNum;
        }
        if(!empty($fieldInfo['limitChars'])){
            if(empty($fieldInfo['maxlength'])){
                $fieldInfo['maxlength'] = is_int($fieldInfo['limitChars']) ? $fieldInfo['limitChars'] : 100;
            }
        }
        $this->fieldset[$_fs]->addField($fieldInfo);

        //If this field has a name associated, add it to the _fieldlist array.
        if(!empty($fieldInfo['name'])){
            $this->_fieldlist[$fieldInfo['name']] = array('fieldset' => $_fs,
                                                             'field' => count($this->fieldset[$_fs]->field));
        }
        //If field type is 'file', change the _enctype to 'multipart/form-data'
        if($fieldInfo['type'] == 'file'){
            $this->_enctype = 'multipart/form-data';
        }
        
        if($fieldInfo['type'] == 'date'){
            $this->_buildDatepickerScript($fieldInfo);
        }
        
        if(!empty($fieldInfo['showOn'])){
            $this->_buildShowOnScripts($fieldInfo);
        }
        
        if(!empty($fieldInfo['limitChars'])){
            $this->_buildLimitCharactersScript($fieldInfo);
        }
        return $this;
    }
    
    /**
     * Build the jQuery scripts for the "showOn" trigger(s) for this field.
     * 
     * @param array $fieldInfo
     * 
     * @return void
     */
    private function _buildShowOnScripts(array $fieldInfo){
        foreach($fieldInfo['showOn'] as $triggerName => $triggerValue){
            $_triggerId = $this->field($triggerName,array('get' => '_fieldID'));
            if(is_array($triggerValue)){
                $_valList = '';
                $x=0;
                foreach($triggerValue as $val){
                    $_valList .= $x > 0 ? ' ' : '';
                    $_valList .= $val;
                    $x++;
                }
                $triggerValue = $_valList;
            }
            switch(strtolower($this->field($triggerName,array('get' => '_inputType')))){
                case 'select':
                    $this->_scripts[$_triggerId] = buildJquery::showOnSelect($_triggerId);
                    if($this->field($fieldInfo['name'],array('get' => '_inputType')) == 'paragraph'){
                        $this->field($fieldInfo['name'],array('addclass' => $_triggerId.'Show'));
                        $this->field($fieldInfo['name'],array('addclass' => $triggerValue));
                    }else{
                        $this->field($fieldInfo['name'],array('addwrapclass' => $_triggerId.'Show'));
                        $this->field($fieldInfo['name'],array('addwrapclass' => $triggerValue));
                    }
                    break;
                case 'radio':
                    $this->_scripts[$_triggerId] = buildJquery::showOnRadio($this->_formID, $triggerName);
                    if($this->field($fieldInfo['name'],array('get' => '_inputType')) == 'paragraph'){
                        $this->field($fieldInfo['name'],array('addclass' => $triggerName.'Show'));
                        $this->field($fieldInfo['name'],array('addclass' => $triggerValue));
                    }else{
                        $this->field($fieldInfo['name'],array('addwrapclass' => $triggerName.'Show'));
                        $this->field($fieldInfo['name'],array('addwrapclass' => $triggerValue));
                    }
                    break;
                case 'checkbox':
                case 'checkafter':
                case 'toggleswitch':
                    $this->_scripts[$_triggerId] = buildJquery::showOnCheckbox($_triggerId);
                    if($this->field($fieldInfo['name'],array('get' => '_inputType')) == 'paragraph'){
                        $this->field($fieldInfo['name'],array('addclass' => $_triggerId.'Show'));
                        $this->field($fieldInfo['name'],array('addclass' => $_triggerId .'Checked'));
                    }else{
                        $this->field($fieldInfo['name'],array('addwrapclass' => $_triggerId.'Show '));
                        $this->field($fieldInfo['name'],array('addwrapclass' => $_triggerId .'Checked'));
                    }
                    break;
            }
        }        
    }
    
    /**
     * Builds the Javascript to use for the character limit functionality of this
     * form field.
     * 
     * @param array $fieldInfo
     */
    private function _buildLimitCharactersScript(array $fieldInfo){
        $this->field($fieldInfo['name'],array('addclass' => 'limitChars'));
        $this->_scripts['limitChars'] = buildJquery::limitCharacters();
    }
    
    /**
     * Builds the Datepicker jQuery script for this date field.
     * 
     * @param array $fieldInfo
     */
    private function _buildDatepickerScript(array $fieldInfo){
        $this->field($fieldInfo['name'],array('addclass' => 'datepicker'));
        $this->_scripts[$fieldInfo['id'].'-datepicker'] = buildJquery::datePicker($fieldInfo);
    }
    
    /**
     * Access the field functions directly from the $Form class. The $params
     * array will have the "key" of the function name and the "val" of the details
     * needed by that method of the Field class.
     * 
     * @param string $name Field name
     * @param array $params Function name and data to be passed to that function.
     * 
     * @return self|string Returns string only for the 'get' action 
     */
    public function field(string $name, array $params){
        $_fs = $this->_fieldlist[$name]['fieldset'];
        $_field = $this->_fieldlist[$name]['field'];
        foreach($params as $action => $details){
            $_detailArray = is_array($details) ? $details : array($details);

            switch(strtolower($action)){
                case 'setlabel':
                    $this->fieldset[$_fs]->field[$_field]->setLabel($details);
                    break;
                case 'setlabelclass':
                    call_user_func_array(array($this->fieldset[$_fs]->field[$_field],"setLabelClass"), $_detailArray);
                    break;
                case 'setinputtype':
                    $this->fieldset[$_fs]->field[$_field]->setInputType($details);
                    break;
                case 'setstyle':
                    call_user_func_array(array($this->fieldset[$_fs]->field[$_field],"setStyle"), $_detailArray);
                    break;
                case 'addstyle':
                    call_user_func_array(array($this->fieldset[$_fs]->field[$_field],"addStyle"), $_detailArray);
                    break;
                case 'setclass':
                    call_user_func_array(array($this->fieldset[$_fs]->field[$_field],"setClass"), $_detailArray);
                    break;
                case 'addclass':
                    call_user_func_array(array($this->fieldset[$_fs]->field[$_field],"addClass"), $_detailArray);
                    break;
                case 'floatlabel':
                    $this->fieldset[$_fs]->field[$_field]->floatLabel($details);
                    break;
                case 'setvalue':
                    $this->fieldset[$_fs]->field[$_field]->setValue($details);
                    break;
                case 'seterror':
                    $this->fieldset[$_fs]->field[$_field]->setError($details);
                    break;
                case 'settitle':
                    $this->fieldset[$_fs]->field[$_field]->setTitle($details);
                    break;
                case 'get':
                    return $this->fieldset[$_fs]->field[$_field]->get($details);
                    break;
                case 'setwraptype':
                    $this->fieldset[$_fs]->field[$_field]->setWrapType($details);
                    break;
                case 'setwrapclass':
                    $this->fieldset[$_fs]->field[$_field]->setWrapClass($details);
                    break;
                case 'addwrapclass':
                    $this->fieldset[$_fs]->field[$_field]->addWrapClass($details);
                    break;
                case 'setwrapstyle':
                    $this->fieldset[$_fs]->field[$_field]->setWrapStyle($details);
                    break;
                case 'addwrapstyle':
                    $this->fieldset[$_fs]->field[$_field]->addWrapStyle($details);
                    break;
                case 'setid':
                    $this->fieldset[$_fs]->field[$_field]->setId($details);
                    break;
                case 'setattributes':
                    $this->fieldset[$_fs]->field[$_field]->setAttributes($details);
                    break;
            }
        }
        return $this;
    }
    
    /**
     * Automatically populate the values of all submitted fields from $data array.
     * Requires $fieldname => $value pairs as in $_POST results or data retrieved
     * from a database.
     * 
     * @param array $data Fieldname/value pairs to be attributed to the Form
     * 
     * @return void
     */
    private function _autoPopulate(array $data){
        foreach($data as $fieldname => $value){
            if(!empty($this->_fieldlist[$fieldname])){
                $this->field($fieldname, array('setvalue' => $value));
            }
        }
    }
    
    /**
     * Automatically set any validation errors from the $fieldname => $error pairs
     * from an $errors array submitted as the parameter.
     * 
     * @param array $errors List of errors to be associated with each field name
     * 
     * @return void
     */
    public function validationErrors(array $errors){
        foreach($errors as $fieldname => $error){
            if(!empty($this->_fieldlist[$fieldname])){
                $this->field($fieldname, array('seterror' => $error,
                                               'addClass' => 'fieldError'));
            }
        }
    }
    
    /**
     * Set whether or not there should be a 'honeypot' field to protect against
     * robot submissions. Default is true.
     * 
     * @param string $active Honeypot on or off, true or false.
     * 
     * @return void
     */
    public function setHoneypot(string $active = 'true'){
        $this->_honeypot = in_array(strtolower($active), array('no','false','none','off')) ? (false) : (true);
        return $this;
    }
    
    /**
     * Return the form object properties to show in browser. This is for 
     * development and testing purposes.
     * 
     * @return string Display of the entire Form object
     * 
     * @return void
     */
    public function showObject(){
        return '<pre>'.print_r($this,true).'</pre>';
    }
    
    /**
     * Generate the form and return the HTML markup for presentation.
     * 
     * @param array $data values to populate the form fields
     * 
     * @return string HTML markup generated for the entire form
     */
    public function generate(array $data = null){
        if(!empty($data)){
            $this->_autoPopulate($data);
        }
        
        $_HTML = '';
        if(!empty($this->_scripts)){
            foreach($this->_scripts as $script){
                $_HTML .= $script;
            }
        }
        $_formID = !empty($this->_formID) ? ' id="'. $this->_formID .'"' : 
            (!empty($this->_formName) ? ' id="'. $this->_formName .'"' : '');
        $_formName = !empty($this->_formName) ? ' name="'. $this->_formName .'"' : 
            (!empty($this->_formID) ? ' name="'. $this->_formID .'"' : '');
        $_formClass = !empty($this->_formClass) ? ' class="'. $this->_formClass .'"' : '';
        $_formStyle = !empty($this->_formStyle) ? ' style="'. $this->_formStyle .'"' : '';
        $_action = !empty($this->_action) ? ' action="'. $this->_action .'"' : '';
        //enctype can only be used if method is 'post'
        $_encType = !empty($this->_enctype) && $this->_method == 'post' ? ' enctype="'. $this->_enctype .'"' : '';
        $_HTML .= '
    <form'. $_formID . $_formName . $_formClass . $_formStyle . $_action . $_encType . ' method="'. $this->_method .'">';
        foreach($this->fieldset as $fs){
            $_HTML .= $this->_layoutFieldset($fs);
        }
        
        $_HTML .= $this->_honeypot ? $this->_insertHoneypot() : '';
        
        if(!empty($this->Submit)){
            $_HTML .= '
      <input type="submit" '. $this->_buildCommonAttributes($this->Submit) .' value="'. $this->Submit->get('_value') .'">';
        }
        $_HTML .= '
    </form>';
        return $_HTML;
    }
    
    /**
     * Lays out the fieldset for HTML markup.
     * 
     * @param object $fieldset
     * 
     * @return string $_fieldsetHTML
     */
    private function _layoutFieldset(form\Fieldset $fieldset){
        $_fieldsetID = !empty($this->_formID) ? $this->_formID .'-fs'. $fieldset->get('_fieldsetID') : 'fs'. $fieldset->get('_fieldsetID');
        $_fieldsetClass = !empty($fieldset->get('_fieldsetClass')) ? ' class="'. $fieldset->get('_fieldsetClass') .'"' : '';
        $_fieldsetStyle = !empty($fieldset->get('_fieldsetStyle')) ? ' style="'. $fieldset->get('_fieldsetStyle') .'"' : '';
        $_fieldsetHTML = '
      <fieldset id="'. $_fieldsetID .'"'. $_fieldsetClass . $_fieldsetStyle .'>';
        $_fieldsetHTML .= !empty($fieldset->get('_legend')) ? '
        <legend>'. $fieldset->get('_legend') .'</legend>' : '';
        if(!empty($fieldset->field)){
            foreach($fieldset->field as $field){
                $_fieldsetHTML .= $this->_layoutFormField($field);
            }
        }
        $_fieldsetHTML .= '
      </fieldset>';
        return $_fieldsetHTML;
    }
    
    /**
     * Lays out the individual field rows of the form.
     * 
     * @param object $field Object containing parameters of the field
     * 
     * @return string $_layout The HTML markup of the field, label and wrapper
     */
    private function _layoutFormField(form\Field $field){
        $_value = $field->get('_value') ?? '';
        $_options = $_label = $_field = '';
        $_labelAfter = $_noWrap = false;
        switch($field->get('_inputType')){
            case 'text':
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <input type="text"'. $this->_buildCommonAttributes($field) . ' value="'.$_value .'">';
                break;

            case 'password':
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <input type="password"'. $this->_buildCommonAttributes($field) . ' value="'.$_value .'">';
                break;

            case 'textarea':
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <textarea'. $this->_buildCommonAttributes($field) . '>' . $_value . '</textarea>';
                break;

            case 'checkbox':
                $_checked = $_value == 1 ? ' checked' : '';
                $_field = '
            <input type="checkbox"'. $this->_buildCommonAttributes($field) . $_checked .' value="1">';
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'><span></span>'. $field->get('_label') .'</label>';
                $_labelAfter = true;
                break;

            case 'checkafter':
                $_checked = $_value == 1 ? ' checked' : '';
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .' <span></span></label>';
                $_field = '
            <input type="checkbox"'. $this->_buildCommonAttributes($field) . $_checked . ' value="1">';
                $_labelAfter = true;
                break;
            
            case 'toggleswitch':
                $_checked = $_value == 1 ? ' checked' : '';
                $field->addClass('toggleswitch-input');
                $_label = '
            <span>' . $field->get('_label') . '</span>';
                $_dataOn = $field->get('_data-on') ?? 'On';
                $_dataOff = $field->get('_data-off') ?? 'Off';
                $_field = '
            <label ' . $this->_buildLabelAttributes($field) . '>
              <input type="checkbox"'. $this->_buildCommonAttributes($field) . $_checked . ' value="1">
              <span class="toggleswitch-inner" data-on="'. $_dataOn .'" data-off="'. $_dataOff .'"></span> 
              <span class="toggleswitch-handle"></span>
            </label>';
                break;

            case 'radio':
                $_label = '
            <span'. $this->_buildLabelClass($field) .'>'. $field->get('_label') .'</span>';
                $_field = $field->get('_options') ? $this->_buildRadioOptionList($field) : ' No options available!';
                break;

            case 'date':
                $_format = $field->get('_format') ?? 'Y-m-d';
                $_date = !empty($_value) ? date("$_format", strtotime($_value)) : '';
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <input type="text"'. $this->_buildCommonAttributes($field, array('_readonly', '_format')) . ' readonly value="' . $_date . '">';
                break;

            case 'none':
                $_label = '
            <label>'. $field->get('_label') .'</label>';
                break;

            case 'number':
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <input type="number"'. $this->_buildCommonAttributes($field) . ' value="'.$_value . '">';
                break;

            case 'year':
                if(!empty($field->get('_min'))){
                    //If the min value is not a 4 digit date it is either +/- integer
                    $_minVal = !preg_match('/\d{4}/', (int) $field->get('_min')) ? ' min="'.(date('Y') + (int) $field->get('_min')).'"' : ' min="'.$field->get('_min').'"';
                }

                if(!empty($field->get('_max'))){
                    //If the max value is not a 4 digit date it is either +/- integer
                    $_maxVal = !preg_match('/\d{4}/', (int) $field->get('_max')) ? ' max="'.(date('Y') + (int) $field->get('_max')).'"' : ' max="'.$field->get('_max').'"';
                }

                $_stepVal = !empty($field->get('_step')) ? ' step="'.$field->get('_step').'"' : '';
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <input type="number"'. $this->_buildCommonAttributes($field, array('_min', '_max', '_step')) . $_minVal . $_maxVal . $_stepVal . ' value="'.$_value .'">';
                break;

            case 'gender':
                $_label = '
            <span'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</span>';
                $_field = '
            <span class="formgroup">
              <input type="radio" id="'.$field->get('_name').'-M"'. $this->_buildCommonAttributes($field, array('_fieldID')) . 'value="m"'.$this->_checkSelected('m', $_value, $field->get('_selected')).'>
              <label for="'.$field->get('_name').'-M"> Male </label>
              <input type="radio" id="'.$field->get('_name').'-F"'. $this->_buildCommonAttributes($field, array('_fieldID')) . 'value="f"'.$this->_checkSelected('f', $_value, $field->get('_selected')).'>
              <label for="'.$field->get('_name').'-F"> Female </label>
            </span>';
                break;

            case 'yes-no':
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <span class="formgroup">
              <input type="radio" id="' . $field->get('_name').'-Y"' . $this->_buildCommonAttributes($field, array('_fieldID')) . 'value="y"'.$this->_checkSelected('y', $_value, $field->get('_selected')).'>
              <label for="' . $field->get('_name').'-Y"> Yes </label>
              <input type="radio" id="' . $field->get('_name').'-N"' . $this->_buildCommonAttributes($field, array('_fieldID')) . 'value="n"'.$this->_checkSelected('n', $_value, $field->get('_selected')).'>
              <label for="' . $field->get('_name').'-N"> No </label>
            </span>';
                break;

            case 'select':
                $_options = '';
                
                //Adding a placeholder for the select box if any
                if(empty($field->get('_selected')) && empty($_value) && !empty($field->get('_placeholder'))){
                    $_options .= '
              <option value="" disabled selected hidden>'. $field->get('_placeholder') .'</option>';
                }
                
                //Check if there was a value or selected attribute submitted
                $_selected = $field->get('_value') ?? ($field->get('_selected') ?? null);

                //If there are any optgroups, build them
                if(!empty($field->get('_optgroup'))){
                    $_options .= $this->_buildSelectOptionGroups($field->get('_optgroup'), $_selected);
                }

                //If there are any options, build them
                if(!empty($field->get('_options'))){
                    $_options .= $this->_buildSelectOptions($field->get('_options'), $_selected);
                }
                
                //Build the label
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                
                //Open the select field tag
                $_field = '
            <select'. $this->_buildCommonAttributes($field, array('_placeholder')) . '>';
                
                //Add the option list, if any
                $_field .= $_options ?? '
              <option value="">No options available!</option>';
                
                //Close the select field tag
                $_field .= '
            </select>';
                break;

            case 'paragraph':
                $_labelSpan = $field->get('_label') ? '<span class="label">'. $field->get('_label').'</span>' : '';
                $_field = '
            <p' . $this->_buildCommonAttributes($field) . '>'. $_labelSpan . $_value .'</p>';
                $_noWrap = true;
                break;

            case 'hidden':
                $_field = '
            <input type="hidden"'. $this->_buildCommonAttributes($field) . ' value="'.$_value .'">';
                $_noWrap = true;
                break;

            case 'submit':
                $_label = $field->get('_label') ? '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>' : '';
                $_field = '
            <input type="submit"'. $this->_buildCommonAttributes($field) . ' value="'.$_value .'">';
                $_noWrap = true;
                break;

            case 'file':
                $_label = '
            <label'. $this->_buildLabelAttributes($field) .'>'. $field->get('_label') .'</label>';
                $_field = '
            <input type="file"'. $this->_buildCommonAttributes($field) . '>';
                break;
            
            case 'reset':
                $_field = '
            <input type="reset"'. $this->_buildCommonAttributes($field) . '>';
                break;
            
            /*
             * @todo create the datalist input element
             */
            case 'datalist':
                
                break;
            
            default:
                $_field = '
            '. $_value;
                break;
        }
        $_field .= !empty($field->get('_limitChars')) ? '
        <div id="'. $field->get('_fieldID') .'-remaining">Characters Remaining: <span id="'. $field->get('_fieldID') .'-chars">'. $field->get('_maxlength') .'</span></div>' : '';
        $_layout = '';
        $_wrapId = !empty($field->get('_fieldID')) ? ' id="'.$field->get('_fieldID').'Wrap" ' : '';
        $_wrapClass = !empty($field->get('_wrapClass')) ? ' class="fieldwrap '. $field->get('_wrapClass') .'"': ' class="fieldwrap"';
        $_wrapStyle = !empty($field->get('_wrapStyle')) ? ' style="'. $field->get('_wrapStyle') .'"': '';
        
        //If '_newLine' attribute, add a <br /> tag before the label
        $_label = $field->get('_newLine') ? '
          <br />'.$_label : $_label;
        //If there is a wrapper element, create opening tag
        $_layout .= !$_noWrap ? '
          <'. $field->get('_wrapType') . $_wrapId . $_wrapClass . $_wrapStyle .'>' : '';
        
        //Determine whether label should be displayed before or after field
        $_layout .= $field->get('_floatLabel') || $_labelAfter ? $_field . $_label : $_label . $_field;
        
        //Create a label for an error message, if any
        $_layout .= !empty($field->get('_error')) ? '
          <label'. $this->_buildLabelFor($field) . ' class="fieldErrorMsg">'. $field->get('_error') .'</label>' : '';
        
        //Close the wrapper, if any
        $_layout .= !$_noWrap ? '
          </'. $field->get('_wrapType') .'>' : '';

         return $_layout;
    }
    
    /**
     * Build the HTML markup for the common field attributes.
     * 
     * Builds the HTML markup for the attributes listed in the _getAttributesList
     * except for any listed in the $ignore parameter.
     * 
     * @param object $field The object holding all of the field's attribute values.
     * @param array $ignore One or more attributes to ignore in the build 
     * process.
     * 
     * @return string $_attributes The string of HTML markup defining all of the
     * field's attributes within the element (i.e. input, textarea, select, etc.).
     */
    private function _buildCommonAttributes(form\Field $field, array $ignore = null){
        $_attributes = '';
        foreach($this->_getAttributeList() as $_attr){
            $_attributes .= (!empty($ignore) && in_array($_attr, $ignore)) || (empty($field->get($_attr))) ? '' : $this->_buildAttribute($_attr, $field);
        }
        return $_attributes;
    }
    
    /**
     * Array of valid input attributes.
     * 
     * @return array Array of input attributes.
     */
    private function _getAttributeList(){
        return array('_fieldID', '_name', '_fieldClass', '_fieldStyle',
            '_placeholder', '_min', '_max', '_step', '_autocomplete', '_pattern',  
            '_formaction', '_formenctype', '_formmethod', '_formtarget',
            '_list', '_formnovalidate', '_autofocus', '_required', '_readonly',
            '_disabled', '_multiple', '_maxlength');
    }
    
    /**
     * Builds the individual attribute for the field.
     * 
     * @param string $attribute The attribute that is being defined and built.
     * @param object $field The object containing all of the field's details.
     * 
     * @return string HTML markup defining the attribute of this field element.
     */
    private function _buildAttribute(string $attribute, form\Field $field){
        switch($attribute){
            case '_fieldID':
                return !empty($field->get('_fieldID')) ? ' id="'.$field->get('_fieldID') .'"' : ' id="'. $field->get('_name').'"';
                break;
            case '_name':
                return ' name="'. $field->get('_name') .'"';
                break;
            case '_fieldClass':
                return ' class="'. $field->get('_fieldClass') .'"';
                break;
            case '_fieldStyle':
                return ' style="'. $field->get('_fieldStyle') .'"';
                break;
            case '_placeholder':
                return ' placeholder="'. $field->get('_placeholder') .'"';
                break;
            case '_min':
                return ' min="'. $field->get('_min') .'"';
                break;
            case '_max':
                return ' max="'. $field->get('_max') .'"';
                break;
            case '_step':
                return ' step="'. $field->get('_step') .'"';
                break;
            case '_autocomplete':
                return in_array(strtolower($field->get('_autocomplete')), array('false','no','off')) ? ' autocomplete="off" ' : ' autocomplete="'. $field->get('_autocomplete') .'"';
                break;
            case '_pattern':
                return ' pattern="'. $field->get('_pattern') .'"';
                break;
            case '_formaction':
                return ' formaction="'. $field->get('_formaction') .'"';
                break;
            case '_formenctype':
                return ' formenctype="'. $field->get('_formenctype') .'"';
                break;
            case '_formmethod':
                return ' formmethod="'. $field->get('_formmethod') .'"';
                break;
            case '_formtarget':
                return ' formtarget="'. $field->get('_formtarget') .'"';
                break;
            case '_list':
                return ' list="'. $field->get('_list') .'"';
                break;
            case '_formnovalidate':
                return ' formnovalidate';
                break;
            case '_autofocus':
                return ' autofocus';
                break;
            case '_required':
                return ' required';
                break;
            case '_readonly':
                return ' readonly';
                break;
            case '_disabled':
                return ' disabled';
                break;
            case '_multiple':
                return ' multiple';
                break;
            case '_maxlength':
                return ' maxlength="'. $field->get('_maxlength') . '"';
                break;
        }
    }
    
    /**
     * Checks if this option should be marked as "selected"
     * 
     * @param string $expectedValue The value that is being checked.
     * @param string $assignedValue The value (if any) that has been attributed
     * as the assigned "value" in the field's parameters.
     * @param string $selectedValue The value (if any) that has been attributed
     * as "selected" in the field's parameters.
     * 
     * @return string "Selected" or empty.
     */
    private function _checkSelected(string $expectedValue, string $assignedValue = null, string $selectedValue = null){
        return ((!empty($assignedValue) && $assignedValue == $expectedValue) || ((!empty($selectedValue) && $selectedValue == $expectedValue) && empty($assignedValue)))?' checked': '';
    }
    
    /**
     * Builds the basic attributes for the label element.
     * 
     * @param object $field  The object containing all of the field's details.
     * 
     * @return string HTML markup defining the attributes of this label element.
     */
    private function _buildLabelAttributes(form\Field $field){
        $_attributes = $this->_buildLabelFor($field);
        $_attributes .= $this->_buildLabelClass($field);
        $_attributes .= $this->_buildLabelStyle($field);
        return $_attributes;
    }
    
    /**
     * Build the "for" attribute for the label element.
     * 
     * @param object $field  The object containing all of the field's details.
     * 
     * @return string the HTML markup defining the "for" attribute of the label.
     */
    private function _buildLabelFor(form\Field $field){
        return !empty($field->get('_fieldID')) ? ' for="'. $field->get('_fieldID').'"' : ' for="'. $field->get('_name').'"';
    }
    
    /**
     * Build the "class" attribute for the label element.
     * 
     * @param object $field  The object containing all of the field's details.
     * 
     * @return string the HTML markup defining the "class" attribute of the label.
     */
    private function _buildLabelClass(form\Field $field){
        return !empty($field->get('_labelClass')) ? ' class="'.$field->get('_inputType') . ' ' . $field->get('_labelClass').'"' : ' class="'.$field->get('_inputType').'"';
    }
    
    /**
     * Build the "class" attribute for the label element.
     * 
     * @param object $field  The object containing all of the field's details.
     * 
     * @return string the HTML markup defining the "class" attribute of the label.
     */
    private function _buildLabelStyle(form\Field $field){
        return !empty($field->get('_labelStyle')) ? ' style="'.$field->get('_labelStyle').'"' : '';
    }
    
    /**
     * Build the "optgroups" for the "select" element.
     * 
     * @param array $optionGroups The array of optgroups and their keys/values.
     * @param string $selected The value that should be marked as "selected"
     * 
     * @return string $_html The HTML markup for the "optgroup" of a "select"
     * element.
     */
    private function _buildSelectOptionGroups(array $optionGroups, string $selected = null){
        $_html = '';
        foreach($optionGroups as $og){
            //The "$optgroup" attribute would be an array containing
            //a $label string and $options array.
            $_html .= '
              <optgroup label="'. $og['label'] .'">';
            $_html .= !empty($og['options']) ? $this->_buildSelectOptions($og['options'], $selected) : ' No options available for this group!';
            $_html .= '
              </optgroup>';
        }
        return $_html;
    }
    
    /**
     * Build the list of "select" element options.
     * 
     * @param array $options List of options for the "select" element.
     * @param string $selected The option value that should be marked as "selected".
     * 
     * @return string $_optionList The HTML markup for the list of options for 
     * the "select" element.
     */
    private function _buildSelectOptions(array $options, string $selected = null){
        //Determine whether the array is indexed or associative and build options
        if($this->is_indexed_array($options) && $this->is_sequential_array($options, $base = 0)){
            return $this->_buildOptions($options, $selected);
        }else{
            return $this->_buildOptionsFromPairs($options, $selected);
        }
    }
    
    /**
     * Build the HTML markup for this "select" element "option" based on key/value
     * pairs of an associative array.
     * 
     * @param mixed $options The array or string detailing this option.
     * @param string $selected The value that should be checked against to see
     * which option should be marked as "selected".
     * 
     * @return string HTML markup for this "select" element option.
     */
    private function _buildOptionsFromPairs($options, string $selected = null){
        $_optionList = '';
        foreach($options as $value => $displayText){
            $_optionList .= $this->_checkIfSelectedOption($value, $displayText, $selected);
        }
        return $_optionList;
    }
    
    /**
     * Build the HTML markup for the options using the indexed array values as 
     * both the name and the value for the option.
     * 
     * @param array $options
     * @param string $selected
     * @return string
     */
    private function _buildOptions(array $options, string $selected = null){
        $_optionList = '';
        foreach($options as $option){
            $_optionList .= $this->_checkIfSelectedOption($option, $option, $selected);
        }
        return $_optionList;
    }
    
    /**
     * Checks whether this option should be marked as "selected" and then returns
     * the full HTML markup back for this option.
     * 
     * @param mixed $value The value attributed with the option.
     * @param string $displayText The text to display for the option.
     * @param mixed $selected The selected value to check against. If it matches
     * the $value parameter it should be attributed with " selected".
     * 
     * @return string The html markup for the option
     */
    private function _checkIfSelectedOption($value, string $displayText, $selected = null){
       $_selected = !empty($selected) && $selected == $value ? " selected" : '';
        return '
              <option value="'. $value .'"'. $_selected .'>'. $displayText .'</option>';
    }
    
    /**
     * Build a group of radio input elements.
     * 
     * @param object $field The object containing all of the field's details.
     * 
     * @return string $_html The html markup for the radio button options.
     */
    private function _buildRadioOptionList(form\Field $field){
        $_html = '
            <span class="formgroup">';
        foreach($field->get('_options') as $option){
            if(is_array($option)){
                foreach($option as $optionValue => $optionText){
                    $_html .= $this->_buildRadioOption($field, $optionText, $optionValue);
                }
            }else{
                $_html .= $this->_buildRadioOption($field, $option, $option);
            }
        }
        $_html .= '
            </span>';
        return $_html;
    }
    
    /**
     * Build an individual "radio" element.
     * 
     * @param object $field The object containing all of the field's details.
     * @param string $optionText The text that should be displayed within the 
     * label for this radio input element.
     * @param string $optionValue
     * 
     * @return string The HTML markup for this radio input element and its label.
     */
    private function _buildRadioOption(form\Field $field, string $optionText, string $optionValue){
        return '
              <input type="radio" id="'. $field->get('_name') . '-' . $optionValue .'"'. $this->_buildCommonAttributes($field, array('_fieldID')) . ' value="' . $optionValue . '"' . $this->_checkSelected($optionValue, $field->get('_value'), $field->get('_selected')) .'>
              <label for="'. $field->get('_name') . '-' . $optionValue .'"> '. $optionText . ' </label>';
    }
    
    /**
     * Generate the honeypot field and return the HTML to the form generator.
     * 
     * @return string $_layout The HTML markup for the honeypot input field.
     */
    private function _insertHoneypot(){
        $_layout = '
          <div class="robocheckhp" id="robocheckWrap">
            <label>No humanoid would use this:</label>
            <input name="robocheck" type="text" id="robocheck" class="robocheck" />
          </div>';
         return $_layout;
    }
    
    /**
     * Checks if each key is an integer.
     * 
     * @param array $arr
     * @return boolean
     */
    private function is_indexed_array(array &$arr) {
        for(reset($arr); is_int(key($arr)); next($arr));
        return is_null(key($arr));
    }

    /**
     * Checks if each key is an integer and also sequential starting from $base.
     * 
     * @param array $arr
     * @param int $base
     * @return boolean
     */
    private function is_sequential_array(array &$arr, int $base = 0) {
        for(reset($arr), $base = (int) $base; key($arr) === $base++; next($arr));
        return is_null(key($arr));
    }

}