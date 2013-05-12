<?php
/**
 * Basic HTML Helper
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class FrontEndHelpers {
    /**
     * Returns records from a table
     *
     * @static
     * @param $control_name
     * @param $table_name
     * @param $id_field
     * @param $description_field
     * @param null $selected_value
     * @param string $sort_direction
     * @return records
     */

    private static function tabular_dropdown_list($control_name, $table_name, $id_field, $description_field, $selected_value = null, $sort_direction = "asc")
    {
        $records = DB::select(array($id_field, 'id'), array($description_field, 'description'))
            ->from($table_name)->order_by($description_field, $sort_direction)
            ->as_object()->execute();

        $control = "<select name='$control_name'>{{ options }}</select>";
        $options_string = "";

        foreach($records as $record)
        {
            $selected = "";

            if($selected_value != null)
            {
                if($selected_value == $record->id)
                    $selected = " selected";
            }

            $options_string.="<option value='".$record->id."'$selected>".$record->description."</option>";
        }

        $control = str_replace("{{ options }}", $options_string, $control);

        return $control;
    }

    /**
     * Build a record level label
     *
     * @static
     * @param $record
     * @return string
     */

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
     * @param null $preset_record_value
     * @return string
     */

    public static function record_control($record, $record_value = null, $preset_record_value = null)
    {

        // TODO: Implement functionality for all the control types

        $control_name = $record->object_meta_slug;
        $values = $record->object_meta_values;
        $control_meta_values = $values;
        $control_values = $values;
        $return_string = null;

        // Any record values set?

        if($record_value != null)
            $values = $record_value->{$record->object_meta_slug};

        // Any preset values?

        if($preset_record_value != null)
            $values = $preset_record_value;

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
            case DBFieldMeta::CONTROL_TABULAR_LIST:
                $meta_values = explode("|", $control_meta_values);

                if(!is_array($meta_values))
                {
                    throw new Exception_Extension("The values supplied for tabular list are not in an array");
                }

                $meta_record = new stdClass();

                foreach($meta_values as $meta_value)
                {
                    list($meta_value_key, $meta_value_value) = explode("=", $meta_value);
                    $meta_record->{$meta_value_key} = $meta_value_value;
                }

                if(isset($meta_record->table) && isset($meta_record->id_field) && isset($meta_record->description_field))
                {
                    $sort_direction = isset($meta_record->sort_direction) ? $meta_record->sort_direction :  "asc";
                    $return_string = self::tabular_dropdown_list($control_name, $meta_record->table,
                        $meta_record->id_field, $meta_record->description_field, $values, $sort_direction);
                }
                else
                {
                    throw new Exception_Extension("Values table, id_field, description_field not set in the array");
                }
                break;
            case DBFieldMeta::CONTROL_MULTI_TEXT:
                $return_string = "<textarea name='$control_name' rows='10' cols='160' style='width:100%;'>$values</textarea>";
                break;
            case DBFieldMeta::CONTROL_RICH_EDIT:
                $return_string = "<textarea class='ckeditor' cols='80' name='$control_name' rows='10'>$values</textarea>";
                break;
            case DBFieldMeta::CONTROL_FILE:
                $return_string = "<input type='file' name='$control_name' />";
                break;
        }

        return $return_string == null? "" : "<p>$return_string</p>";
    }

    /**
     * Button to save
     *
     * @return string
     */

    public static function save_button_value()
    {
        return "Save";
    }

    /**
     * Button to submit
     *
     * @return string
     */

    public static function submit_button_value()
    {
        return "Submit";
    }
}
