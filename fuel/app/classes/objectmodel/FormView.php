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
}
