<?php
/**
 * Basic HTML Helper
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class AdminHelpers {

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
     * Returns multiple select records from a table
     *
     * @static
     * @param $control_name
     * @param $table_name
     * @param $id_field
     * @param $description_field
	 * @param $relationship_table
     * @param $selected_values
     * @param string $sort_direction
     * @return records
     */
	
	private static function checkbox_list($control_name, $table_name, $id_field, $description_field, $relationship_table,
		$selected_values = array(), $sort_direction = "asc")
	{
		$records = DB::select(array($id_field, 'id'), array($description_field, 'description'))
            ->from($table_name)->order_by($description_field, $sort_direction)
            ->as_object()->execute();

        $control = "<p style='padding-left:20px;'><input type='checkbox' name='".$control_name."[]' value='{{ value }}' {{ selected }} /> &nbsp;{{ display_value }}</p>";
        $options_string = "";

        foreach($records as $record)
        {
            $selected = "";

            if(count($selected_values) > 0)
            {
            	if(in_array($record->id, $selected_values))
				{
					$selected = "checked";
				}
            }
			
			$control_string = str_replace("{{ value }}", $record->id, $control);
			$control_string = str_replace("{{ selected }}", $selected, $control_string);
			$control_string = str_replace("{{ display_value }}", $record->description, $control_string);

            $options_string.= $control_string;
        }

        $output_string = "<div style='padding-top:10px;padding-bottom:10px;'><input type='hidden' name='checkbox_".$control_name."' value='$relationship_table' />";
		$output_string.=$options_string;
		$output_string.="</div>";

        return $output_string;
	}

	/**
	 * Returns selected values for a relationship
	 *
	 * @param relationship_table
	 * @param master_id
	 * @return array of selected values
	 */

	private static function get_checkbox_list_selected_value($relationship_table, $master_id = 0)
	{
		$fields = DB::select(array(DBFieldMeta::FIELD_RELATIONSHIP_MASTER_FIELD, "id_master"),
			array(DBFieldMeta::FIELD_RELATIONSHIP_DETAIL_FIELD, "id_detail"))->from($relationship_table);
		
		$fields = $fields->where(DBFieldMeta::FIELD_RELATIONSHIP_MASTER_FIELD, "=", $master_id);
		$fields = $fields->as_object()->execute();
		
		$field_value_array = array();
			
		if(count($fields) > 0)
		{
			foreach($fields as $field)
			{
				$field_value_array[] = $field->{DBFieldMeta::FIELD_RELATIONSHIP_DETAIL_FIELD};
			}
		}
		
		return $field_value_array;
	}


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
            $button_html.="<a href='".$button->button_link."' class='btn' style='margin-right:5px;'>".$button->button_text."</a>";
        }

        return $button_html;
    }

	/**
     * Returns bootstrap submit buttons
     *
     * @static
     * @param array $buttons
     * @return string
     */

    public static function bootstrap_submit_buttons($buttons = array())
    {
        $button_html = "";

        foreach($buttons as $button)
        {
            $button_html.="<input type='submit' name='submit' value='".$button."' class='btn' style='margin-right:5px;' />";
        }

        return $button_html;
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
			case DBFieldMeta::CONTROL_MULTISELECT:
				$meta_values = explode("|", $control_meta_values);
                    
                if(!is_array($meta_values))
                {
                    throw new Exception_Extension("The values supplied for multi select are not in an array");
                }
				
				$meta_record = new stdClass();

                foreach($meta_values as $meta_value)
                {
                    list($meta_value_key, $meta_value_value) = explode("=", $meta_value);
                    $meta_record->{$meta_value_key} = $meta_value_value;
                }
				
				if(isset($meta_record->table) && isset($meta_record->id_field) && isset($meta_record->description_field)
					&& isset($meta_record->relationship_table))
                {
                    $sort_direction = isset($meta_record->sort_direction) ? $meta_record->sort_direction :  "asc";
                    
                    $selected_values = null;
                    
                    if(isset($record_value->id))
                    	$selected_values = self::get_checkbox_list_selected_value($meta_record->relationship_table, $record_value->id);
					else
						$selected_values = self::get_checkbox_list_selected_value($meta_record->relationship_table, 0);
					
                    $return_string = self::checkbox_list($control_name, $meta_record->table, $meta_record->id_field, $meta_record->description_field, 
                    	$meta_record->relationship_table, $selected_values, $sort_direction);
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
            case DBFieldMeta::CONTROL_CUSTOM:
                $custom_control = new \customcontrols\CustomControls($control_name, $control_values);
                $return_string = $custom_control->render_control($values);
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

    /**
     * Places pagination links
     *
     * @static
     * @param $pagination_records
     * @return mixed|string
     */

    public static function pagination_links($pagination_records)
    {
        $pagination_string = "<div class='pagination'>{{ pagination_links }}</div>";
        $number_of_pages = intval($pagination_records->num_pages);
        $current_page = intval($pagination_records->current_page);
        $pagination_link = $pagination_records->pagination_link;
        $pagination_size = intval($pagination_records->pagination_size);
		$mid_range = ceil($pagination_size / 2);

        $page_difference = $number_of_pages - $current_page;
		
		$start_page = $current_page - $mid_range;
		$end_page = $start_page + $pagination_size;
		
		$previous_page = $current_page - 1;
		$next_page = $current_page + 1;
		
		if($end_page > $number_of_pages)
		{
			$difference = $end_page - $number_of_pages;
			$end_page = $number_of_pages;
			$start_page = $start_page - $difference;
		}
		
		if($start_page < 1)
		{
			$difference = $start_page * (-1);
			$start_page = 1;
			$end_page = $end_page + $difference;
		}
		
		if($end_page > $number_of_pages)
		{
			if($number_of_pages == 0)
				$end_page = 1;
			else
				$end_page = $number_of_pages;
		}
		
		if($previous_page < 1)
		{
			$previous_page = 1;
		}
		
		if($next_page > $number_of_pages)
		{
			if($number_of_pages == 0)
				$next_page = 1;
			else
				$next_page = $number_of_pages;
		}
		
        $page_links_string = "";

        $page_links_string.="<li><a href='".$pagination_link.$previous_page."'>&laquo;</a></li>";

        for($page_loop = $start_page; $page_loop <= $end_page; $page_loop ++)
        {
            $link_to_page = $pagination_link.$page_loop;
            if($page_loop != $current_page)
                $page_links_string.="<li><a href='$link_to_page'>$page_loop</a></li>";
            else
                $page_links_string.="<li class='active'><a href='$link_to_page'>$page_loop</a></li>";
        }

        $page_links_string.="<li><a href='".$pagination_link.$next_page."'>&raquo;</a></li>";

        $pagination_string = str_replace("{{ pagination_links }}", "<ul>".$page_links_string."</ul>",
            $pagination_string);

        return $pagination_string;
    }

    /**
     * Build a twitter bootstrap button
     *
     * @param $url
     * @param $text
     * @param $controller_path
     * @return stdClass
     */

    public static function build_bootstrap_button($controller_path, $url, $text)
    {
        $my_button = new stdClass();

        $my_button->button_link = Uri::base().$controller_path.$url;
        $my_button->button_text = $text;

        return $my_button;
    }
	
	/**
     * Build a twitter bootstrap submit button
     *
     * @param $name
	 * @param $text
     * @return stdClass
     */
	
	public static function build_bootstrap_submit_button($text)
    {
        return self::bootstrap_submit_buttons(array($text));
    }
}
