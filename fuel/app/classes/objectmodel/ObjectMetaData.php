<?php
/**
 * Object Meta Data
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class ObjectModel_ObjectMetaData {

    private $object_meta_data;

    /**
     * Constructor
     *
     * @param $object_name
     */

    public function __construct($object_name)
    {
        $objects_table = ExtensionSetup::EXTENSION_OBJECTS_TABLE;
        $objects_meta_table = ExtensionSetup::EXTENSION_OBJECTS_META_TABLE;

        $object = DB::select("object_id")->from($objects_table)
                ->where("object_slug", "=", $object_name)->as_object()->execute();

        if(count($object) < 1)
            return null;

        $object_id = $object[0]->object_id;

        $object_meta_data = DB::select("*")->from($objects_meta_table)
                ->where("object_id", "=", $object_id)->as_object()->execute();

        $this->object_meta_data = $object_meta_data;
    }

    /**
     * Get the meta data items
     *
     * @return object meta data
     */

    public function get_meta_data()
    {
        return $this->object_meta_data;
    }
}
