<?php
/**
 * Has object setup functionality
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class ExtensionSetup {

    const META_TABLE_NAME = "table";
    const META_FIELDS = "fields";
    const META_SETTINGS = "settings";

    const EXTENSION_TABLE = "extensions";
    const EXTENSION_OBJECTS_TABLE = "extension_objects";
    const EXTENSION_OBJECTS_META_TABLE = "extension_object_meta";
    const EXTENSION_SETTINGS_TABLE = "extension_settings";

    /**
     * Creates objects meta-data in the database
     *
     * @param $object_meta_data_array
     * @param $extension_info
     * @return void
     */
    private function create_meta_data_database_entries($object_meta_data_array, $extension_info)
    {
        $extension_data = $extension_info[0];

        list($extension_id, $rows_affected) = DB::insert(self::EXTENSION_TABLE)->set(array(
            ExtensionInfo::SEGMENT_EXTENSION_NAME => $extension_data->{ExtensionInfo::SEGMENT_EXTENSION_NAME},
            ExtensionInfo::SEGMENT_EXTENSION_VERSION => $extension_data->{ExtensionInfo::SEGMENT_EXTENSION_VERSION},
            ExtensionInfo::SEGMENT_EXTENSION_SLUG => $extension_data->{ExtensionInfo::SEGMENT_EXTENSION_SLUG},
            ExtensionInfo::SEGMENT_EXTENSION_FOLDER => $extension_data->{ExtensionInfo::SEGMENT_EXTENSION_FOLDER},
            ExtensionInfo::SEGMENT_EXTENSION_ACTIVE => 1,
            ExtensionInfo::SEGMENT_EXTENSION_DESCRIPTION => $extension_data->{ExtensionInfo::SEGMENT_EXTENSION_DESCRIPTION},
        ))->execute();
        
        foreach($object_meta_data_array as $object_meta_data) {
            $table_name = $object_meta_data[self::META_TABLE_NAME];
            $fields = $object_meta_data[self::META_FIELDS];
            $settings = $object_meta_data[self::META_SETTINGS];

            list($object_id, $object_rows_affected) = DB::insert(self::EXTENSION_OBJECTS_TABLE)->set(array(
                Extension_Objects::SEGMENT_OBJECT_NAME => $table_name,
                Extension_Objects::SEGMENT_OBJECT_SLUG => $table_name,
                Extension_Objects::SEGMENT_OBJECT_TYPE => Extension_Objects::OBJECT_TYPE_RELATION,
                Extension_Objects::SEGMENT_EXTENSION_ID => $extension_id,
            ))->execute();

            // Store field meta data
            foreach($fields as $field) {
                DB::insert(self::EXTENSION_OBJECTS_META_TABLE)->set(array(
                    Extension_ObjectMeta::SEGMENT_OBJECT_ID => $object_id,
                    Extension_ObjectMeta::SEGMENT_OBJECT_META_NAME => $field->field_name,
                    Extension_ObjectMeta::SEGMENT_OBJECT_META_SLUG => Utility::slugify($field->field_name),
                    Extension_ObjectMeta::SEGMENT_OBJECT_META_TYPE => $field->field_type,
                    Extension_ObjectMeta::SEGMENT_OBJECT_META_SIZE => $field->field_size,
                    Extension_ObjectMeta::SEGMENT_OBJECT_META_CONTROL => $field->control_type,
                    Extension_ObjectMeta::SEGMENT_OBJECT_META_VALUES => $field->control_values,
                ))->execute();
            }

            // Store settings meta data
            foreach($settings as $setting) {
                DB::insert(self::EXTENSION_SETTINGS_TABLE)->set(array(
                    Extension_Settings::SEGMENT_SETTING_NAME => $setting->field_name,
                    Extension_Settings::SEGMENT_SETTING_SLUG => Utility::slugify($setting->field_name),
                    Extension_Settings::SEGMENT_SETTING_VALUE => $setting->control_values,
                    Extension_Settings::SEGMENT_SETTING_EXTENSION_ID => $extension_id,
                ))->execute();
            }
        }

		// Add extension permissions to the database
		if(is_array($extension_data->{ExtensionInfo::SEGMENT_EXTENSION_PERMISSIONS}))
		{
			$permissions = $extension_data->{ExtensionInfo::SEGMENT_EXTENSION_PERMISSIONS};
			
			foreach($permissions as $permission)
			{
				if(trim($permission) != "")
					Permissions::create_permission($permission);
			}
		}
    }

    /**
     * Object installer
     *
     * @throws Exception_Setup
     * @param $object_meta_data_array
     * @param $extension_info
     * @return void
     */
    public function install($object_meta_data_array, $extension_info)
    {
        // Install extension meta data

        DB::start_transaction();

        $this->create_meta_data_database_entries($object_meta_data_array, $extension_info);

        DB::commit_transaction();

        // Create tables

        $table_prefix = Config::get("db.table_prefix");

        foreach($object_meta_data_array as $object_meta_data) {

            // Table name defined?
            if(!isset($object_meta_data[self::META_TABLE_NAME])) {
                throw new Exception_Setup("The table name has not been specified");
            }

            // Fields defined?
            if(!isset($object_meta_data[self::META_FIELDS])) {
                throw new Exception_Setup("The fields have not been specified");
            }

            // Are the fields in an array?
            if(!is_array($object_meta_data[self::META_FIELDS])) {
                throw new Exception_Setup("The field data is not an array");
            }

            $table_name = $object_meta_data[self::META_TABLE_NAME];

            $table_fields = array();

            // Primary key field
            $table_fields['id'] = array(
                'type' => 'bigint',
                'auto_increment' => true,
            );

            // Fields
            foreach($object_meta_data[self::META_FIELDS] as $field)
            {
                if($field->field_type == "enum") {
                    $field_values = explode("|", $field->control_values);
                    $enum_values = "'";

                    foreach($field_values as $field_value) {
                        $enum_values.=$field_value."','";
                    }

                    $field->field_type = "enum";

                    $enum_values = rtrim($enum_values, "'");
                    $enum_values = rtrim($enum_values, ",");

                    $table_fields[$field->field_slug] = array(
                        'constraint' => rtrim($enum_values),
                        'type' => $field->field_type,
                    );
                }
                else if($field->field_size > 0) {
                    $table_fields[$field->field_slug] = array(
                        'constraint' => $field->field_size,
                        'type' => $field->field_type,
                    );
                }
                else {
                    $table_fields[$field->field_slug] = array(
                        'type' => $field->field_type,
                    );
                }


            }

            // Create table
            DBUtil::create_table($table_prefix.$table_name,
                $table_fields, array('id'), false, 'InnoDB', 'utf8_unicode_ci');
        }
    }

    /**
     * Object uninstaller
     *
     * @param $extension_id
     * @param $tables
     * @return void
     */
    public function uninstall($extension_id, $tables, $delete_extension = true, 
        $delete_tables = true)
    {
        DB::start_transaction();

        if(!is_array($tables) && $delete_tables) {
            throw new Exception_Setup("Tables specified are not in an array");
        }

        $table_prefix = Config::get("db.table_prefix");

        // Did we want to delete the extension itsself?
        if($delete_extension)
        {
            // Delete meta data
            DB::delete(self::EXTENSION_TABLE)->where("extension_id", "=", $extension_id)->execute();

            $db_objects = DB::select("object_id")->from(self::EXTENSION_OBJECTS_TABLE)
                    ->where("extension_id", "=", $extension_id)->as_object()->execute();

            foreach($db_objects as $db_object) {
                DB::delete(self::EXTENSION_OBJECTS_META_TABLE)->where("object_id", "=", $db_object->object_id)->execute();
            }

            DB::delete(self::EXTENSION_OBJECTS_TABLE)->where("extension_id", "=", $extension_id)->execute();

            // Delete extension settings

            DB::delete(self::EXTENSION_SETTINGS_TABLE)->where("extension_setting_extension_id", "=", $extension_id)->execute();
        }
        
        // Did we want to delete tables?
        if($delete_tables)
        {
            // Delete / Drop tables
            foreach($tables as $table) {
                DBUtil::drop_table($table_prefix.$table);
            }
        }
        
        DB::commit_transaction();
    }
}
