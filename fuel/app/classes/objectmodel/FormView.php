<?php
/**
 * Viewing form views
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class ObjectModel_FormView {
    public $page_title;
    public $page_content;
    public $preset_form_fields;
    public $form_action;
    public $return_path = "";
    public $record_id = 0;

    public $div_container_class = "";
    public $div_content_class = "";
    public $div_form_class = "";

    private $table_slug;
    private $object_meta_data;

    private $records = null;

    /**
     * Constructor
     *
     * @param $table_slug
     * @param int $record_id
     * @param string $return_path
     */

    public function __construct($table_slug, $record_id = 0, $return_path = "")
    {
        $this->table_slug = $table_slug;
        $this->record_id = $record_id;
        $this->return_path = $return_path;

        $meta_data = new ObjectModel_ObjectMetaData($table_slug);
        $this->object_meta_data = $meta_data->get_meta_data();

        $record = array();

        if($record_id > 0)
            $record = DB::select("*")->from($table_slug)->where("id", "=", $record_id)->as_object()->execute();

        if(count($record) > 0)
            $this->records = $record[0];
        else
            $this->records = null;
    }

    /**
     * Gets the meta data for an object
     *
     * @return meta|object
     */

    public function get_object_meta_data()
    {
        return $this->object_meta_data;
    }

    /**
     * Gets the object's records
     *
     * @return null
     */

    public function get_records()
    {
        return $this->records;
    }
	
	/**
	 * Gets the records to save from a form view
	 * 
	 * @param $object_meta_data
	 * @param $controller
	 * 
	 * @return records_to_save
	 */
	
	public static function get_records_to_save($object_meta_data, $controller)
	{
		$database_array = array();
		
		foreach($object_meta_data as $object_meta_data_item)
        {
            $value_to_set = "";
            $skip_control = false;
            
            switch($object_meta_data_item->{Extension_ObjectMeta::SEGMENT_OBJECT_META_CONTROL}) {
                case DBFieldMeta::CONTROL_SIMPLE_TEXT:
                    $value_to_set = trim(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_NUMERIC:
                    $value_to_set = floatval(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_MULTI_TEXT:
                    $value_to_set = trim(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_RICH_EDIT:
                    $value_to_set = trim(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_LIST:
                    $value_to_set = Input::post($object_meta_data_item->object_meta_slug);
                    break;
                case DBFieldMeta::CONTROL_TABULAR_LIST:
                    $value_to_set = Input::post($object_meta_data_item->object_meta_slug);
                    break;
				case DBFieldMeta::CONTROL_MULTISELECT:
					$selected_values = Input::post($object_meta_data_item->object_meta_slug);
					$table_name = Input::post("checkbox_".$object_meta_data_item->object_meta_slug);
					$value_to_set = "($table_name)|".implode("|", $selected_values);
					break;
                case DBFieldMeta::CONTROL_CHECKBOX:
                    $value_to_set = isset($_POST[$object_meta_data_item->object_meta_slug]) ? 1 : 0;
                    break;
                case DBFieldMeta::CONTROL_HIDDEN:
                    $value_to_set = $controller->special_field_operation($object_meta_data_item->object_meta_slug, Input::post());
                        
                    if($value_to_set == false) {
                        $skip_control = true;
                    }
                    break;
                case DBFieldMeta::CONTROL_FILE:

                    $upload_error = $_FILES[$object_meta_data_item->object_meta_slug]["error"];

                    $value_to_set = ($upload_error == UPLOAD_ERR_OK) ?
                            $_FILES[$object_meta_data_item->object_meta_slug]["name"] : "";

                    if($upload_error != UPLOAD_ERR_OK)
                    {
                        $skip_control = true;
                    }

                    break;
            }

            if(!$skip_control)
                $database_array[$object_meta_data_item->object_meta_slug] = $value_to_set;
        }

		return $database_array;
	}

	public static function save_records_to_db($object, $record_id, $object_meta_data, $database_array, $media_upload_path)
	{
		$update_object = null;
		
		if($record_id > 0) {
            $update_object = DB::update($object)->where("id", "=", $record_id);
        }
        else {
            $update_object = DB::insert($object);
        }
		
		$insert_id = 0;
        $rows_affected = 0;

        if(count($database_array) > 0)
        {
            if($record_id > 0)
            {
                $rows_affected = $update_object->set($database_array)->execute();
                $insert_id = $record_id;
            }
            else {
                list($insert_id, $rows_affected) = $update_object->set($database_array)->execute();
            }
			
			// Save multiple select values in a related table
			
			foreach($object_meta_data as $object_meta_data_item)
			{
				if($object_meta_data_item->{Extension_ObjectMeta::SEGMENT_OBJECT_META_CONTROL} == DBFieldMeta::CONTROL_MULTISELECT)
				{
					if(isset($database_array[$object_meta_data_item->object_meta_slug]))
					{
						$items_to_save = explode("|", $database_array[$object_meta_data_item->object_meta_slug]);
						$num_items = count($items_to_save);
						
						if($num_items > 0)
						{
							$table_name = $items_to_save[0];
							$table_name = ltrim($table_name, "(");
							$table_name = rtrim($table_name, ")");
							
							// Clear the table
							
							DB::delete(trim($table_name))->where(DBFieldMeta::FIELD_RELATIONSHIP_MASTER_FIELD, "=", $insert_id)->execute();
							
							// Add new records
							
							for($item_loop = 1; $item_loop < $num_items; $item_loop++)
							{
								DB::insert(trim($table_name))->set(array(
									DBFieldMeta::FIELD_RELATIONSHIP_MASTER_FIELD => $insert_id,
									DBFieldMeta::FIELD_RELATIONSHIP_DETAIL_FIELD => $items_to_save[$item_loop]
								))->execute();
							}
						}
					}
				}
			}

            // Process and upload files afresh with their new destination names

            $media_destination_path = $media_upload_path == null ?
                    UPLOADPATH : UPLOADPATH.rtrim($media_upload_path, "/")."/";

            foreach($object_meta_data as $object_meta_data_item)
            {
                if($object_meta_data_item->{Extension_ObjectMeta::SEGMENT_OBJECT_META_CONTROL} == DBFieldMeta::CONTROL_FILE)
                {
                    if($_FILES[$object_meta_data_item->object_meta_slug]["error"] == UPLOAD_ERR_OK)
                    {
                        $uploaded_file = $_FILES[$object_meta_data_item->object_meta_slug]["tmp_name"];
                        $file_name = $_FILES[$object_meta_data_item->object_meta_slug]["name"];
                        move_uploaded_file($uploaded_file, $media_destination_path.$insert_id."_".$file_name);
                    }
                }
            }
        }

		return $update_object;
	}
}
