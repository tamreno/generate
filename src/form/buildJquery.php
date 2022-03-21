<?php
/**
 * @package tamreno\generate\form
 * @author: Tam Bieszczad
 * @license: Apache License 2.0 
 */
namespace tamreno\generate\form;

class buildJquery
{
    /**
     * Builds and returns the script for the "showOn" function for select
     * box triggers.
     * 
     * @param string $triggerId
     * 
     * @return string $_script
     */
    public static function showOnSelect(string $triggerId){
//var res = str.match(/([\S-]*)(?=Checked)/g);
        $_script = <<<END
            
<script>
  $(document).ready(function(){
    var selectedValue = $("#{$triggerId}").val();
    console.log('#{$triggerId} selectedValue = ' + selectedValue);
    if(selectedValue){
      $(".{$triggerId}Show").not("." + selectedValue).hide();
      $(".{$triggerId}Show").not("." + selectedValue).find('input[type=checkbox]').prop('checked',false);
      $(".{$triggerId}Show").not("." + selectedValue).find("input, select, textarea").prop("disabled", true);
      $(".{$triggerId}Show." + selectedValue).show();
      $(".{$triggerId}Show." + selectedValue).find("input, select, textarea").prop("disabled", null);
    } else {
      $(".{$triggerId}Show").hide();
      $(".{$triggerId}Show").find('input[type=checkbox]').prop('checked',false);
      $(".{$triggerId}Show").find("input, select, textarea").prop("disabled", true);
    }
    $("#$triggerId").on( "change", function(){
      $(this).find("option:selected").each(function(){
        var selectedValue = $(this).attr("value");
        console.log('#{$triggerId} selectedValue = ' + selectedValue);
        $(".{$triggerId}Show").not("." + selectedValue).hide();
        $(".{$triggerId}Show").not("." + selectedValue).find('input[type=checkbox]').prop('checked',false);
        $(".{$triggerId}Show").not("." + selectedValue).find("input, select, textarea").prop("disabled", true);
        $(".{$triggerId}Show." + selectedValue).show();
        $(".{$triggerId}Show." + selectedValue).find("input, select, textarea").prop("disabled", null);
      });
    });
  });
</script>
END;
        return $_script;
    }
    
    /**
     * Builds and returns the script for the "showOn" function for radio button
     * or checkbox triggers.
     * 
     * @param string $triggerId
     * @param string $triggerName
     * 
     * @return string $_script
     */
    public static function showOnRadio(string $formID, string $triggerName){
        $_script = <<<END
            
<script>
  $(document).ready(function(){
    var selectedValue = $('input[name=$triggerName]:checked', '#$formID').val();
    console.log('Trigger Name = $triggerName, selectedValue = ' + selectedValue);
    if(selectedValue){
      $(".{$triggerName}Show").not("." + selectedValue).hide();
      $(".{$triggerName}Show").not("." + selectedValue).find('input[type=checkbox]').val('checked',false);
      $(".{$triggerName}Show").not("." + selectedValue).find("input, select, textarea").prop("disabled", true);
      $(".{$triggerName}Show." + selectedValue).show();
      $(".{$triggerName}Show." + selectedValue).find("input, select, textarea").prop("disabled", null);
    }else{
      $(".{$triggerName}Show").hide();
      $(".{$triggerName}Show").find('input[type=checkbox]').val('checked',false);
      $(".{$triggerName}Show").find("input, select, textarea").prop("disabled", true);
    }
    $("[id^={$triggerName}-]").on( "click", function(){
      var selectedValue = $(this).attr("value");
      console.log('Selected Value is ' + selectedValue);
      $(".{$triggerName}Show").not("." + selectedValue).hide();
      $(".{$triggerName}Show").not("." + selectedValue).find('input[type=checkbox]').val('checked',false);
      $(".{$triggerName}Show").not("." + selectedValue).find("input, select, textarea").prop("disabled", true);
      $(".{$triggerName}Show." + selectedValue).show();
      $(".{$triggerName}Show." + selectedValue).find("input, select, textarea").prop("disabled", null);
    });
  });
</script>
END;
        return $_script;
    }
    
    /**
     * Builds the jQuery script for show on checkbox functionality.
     * 
     * @param string $triggerId
     * @return string
     */
    public static function showOnCheckbox(string $triggerId){
        $_script = <<<END
            
<script>
  $(document).ready(function(){
    if(!$("#{$triggerId}").prop('checked')){
        console.log("{$triggerId} not checked");
        $(".{$triggerId}Show").hide();
        $(".{$triggerId}Show").find('input[type=checkbox]').prop('checked',false);
        $(".{$triggerId}Show").find("input, select, textarea").prop("disabled", true);
    };
    $("#$triggerId").on("change", function(){
        if($(this).is(":checked")) {
          console.log("#$triggerId" + ' checked');
          $(".{$triggerId}Show").not(".{$triggerId}Checked").hide();
          $(".{$triggerId}Show").not(".{$triggerId}Checked").find("input, select, textarea").prop("disabled", true);
          $(".{$triggerId}Checked").show();
          $(".{$triggerId}Checked").find("input, select, textarea").prop("disabled", null);
        } else {
          console.log("#$triggerId" + ' unchecked');
          $(".{$triggerId}Show").hide();
          $(".{$triggerId}Show").find('input[type=checkbox]').prop('checked',false);
          $(".{$triggerId}Show").find("input, select, textarea").prop("disabled", true);
        }
    });
  });
</script>
END;
        return $_script;
    }
    
    /**
     * Supplies the jQuery for limiting characters in a field.
     * 
     * @return string 
     */
    public static function limitCharacters(){
        $_script = <<<END
                
  <script>
      $(document).ready(function() {
        $(".limitChars").on("keyup", function() {
            var maxLength = $(this).attr("maxlength");
            var id = $(this).attr("id");
            var text_length = $(this).val().length;
            console.log("maxLength = " + maxLength + "; ID = " + id + "; text-length = " + text_length);
            var text_remaining = maxLength - text_length;
            $("#" + id + "-chars").html(text_remaining);
        });
      });
  </script>               
END;
        return $_script;
    }

    /**
     * Builds the DatePicker jQuery script.
     * 
     * @param array $fieldInfo
     * @return string
     */
    public static function datePicker(array $fieldInfo){
        $parameters = $fieldInfo['datepicker'] ?? array();
        $_script = '
                
  <script>
    $(document).ready(function() {
      $( "#'.$fieldInfo['id'].'" ).datepicker({';
        $_script .= !empty($parameters['dateFormat']) ? "
        dateFormat: '".$parameters['dateFormat']."'," : "
        dateFormat: 'yy-mm-dd',";
        unset($parameters['dateFormat']);
        $_script .= !empty($parameters['changeYear']) ? "
        changeYear: '".$parameters['changeYear']."'," : "
        changeYear: true,";
        unset($parameters['changeYear']);
        if(!empty($parameters)){
            foreach($parameters as $key => $val){
                $_script .= "
        $key: ";
                if(is_bool($val)){
                    $_script .= $val == 1 ? "true," : "false";
                }elseif(is_int($val)){
                    $_script .= $val.',';
                }else{
                    $_script .= "'$val',";
                }
            }
        }
        $_script = rtrim($_script, ",");
        $_script .= '
      });
    });
  </script>';
        return $_script;
    }
}