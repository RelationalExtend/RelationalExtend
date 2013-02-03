<?php
/**
 * Basic HTML Helper
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class AdminHelpers {
    /**
     * Returns bootstrap buttons with links
     *
     * @static
     * @param array $buttons
     * @return string
     */
    public static function bootstrap_buttons($buttons = array())
    {
        $button_html = "";

        foreach($buttons as $button)
        {
            $button_html.="<a href='".$button->button_link."' class='btn'>".$button->button_text."</a>";
        }

        return $button_html;
    }

    public static function record_label($record)
    {
        if($record->object_meta_control != DBFieldMeta::CONTROL_HIDDEN)
            return "<p>".$record->object_meta_name."</p>";
    }

    /**
     * Creates a record control
     *
     * @static
     * @param $record
     * @param null $record_value
     * @return string
     */

    public static function record_control($record, $record_value = null)
    {

        // TODO: Implement functionality for all the control types

        $control_name = $record->object_meta_slug;
        $values = $record->object_meta_values;
        $control_values = $values;
        $return_string = null;

        if($record_value != null)
            $values = $record_value->{$record->object_meta_slug};
        
        switch($record->object_meta_control)
        {
            case DBFieldMeta::CONTROL_SIMPLE_TEXT:
                $return_string = "<input type='text' value='$values' name='$control_name'/>";
                break;
            case DBFieldMeta::CONTROL_NUMERIC:
                $return_string = "<input type='text' value='$values' name='$control_name' style='text-align: right;'/>";
                break;
            case DBFieldMeta::CONTROL_CHECKBOX:
                $checked = "";

                if(intval($values) == 1)
                    $checked = "checked";
                    
                $return_string = "<input type='checkbox' value='$values' name='$control_name' $checked/>";
                break;
            case DBFieldMeta::CONTROL_HIDDEN:
                $return_string = "<input type='hidden' value='$values' name='$control_name' />";
                break;
            case DBFieldMeta::CONTROL_LIST:
                $values_array = explode("|", $control_values);

                $control_string = "<select name='$control_name'>";

                foreach($values_array as $value_array_item) {
                    if($value_array_item == $values)
                        $control_string.="<option value='$value_array_item' selected>$value_array_item</option>";
                    else
                        $control_string.="<option value='$value_array_item'>$value_array_item</option>";
                }
                
                $control_string.="</select>";

                $return_string = $control_string;

                break;
            case DBFieldMeta::CONTROL_MULTI_TEXT:
                $return_string = "<textarea name='$control_name' rows='10' cols='160'>$values</textarea>";
                break;
            case DBFieldMeta::CONTROL_RICH_EDIT:
                $return_string = "<textarea class='ckeditor' cols='80' name='$control_name' rows='10'>$values</textarea>";
                break;
        }

        return $return_string == null? "" : "<p>$return_string</p>";
    }

    /**
     * Value for the save button
     *
     * @return string
     */

    public static function save_button_value()
    {
        return "Save";
    }

    /**
     * Value for the save and exit button
     *
     * @static
     * @return string
     */

    public static function save_and_exit_button_value()
    {
        return "Save and Exit";
    }
}
