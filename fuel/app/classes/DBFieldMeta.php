<?php
/**
 * Database Field Meta Data
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class DBFieldMeta {
    const FIELD_INT = "int";
    const FIELD_BIGINT = "bigint";
    const FIELD_VARCHAR = "varchar";
    const FIELD_DATE = "date";
    const FIELD_DATE_TIME = "datetime";
    const FIELD_TIMESTAMP = "timestamp";
    const FIELD_TEXT = "text";
    const FIELD_LIST = "enum";

    const CONTROL_SIMPLE_TEXT = "simpletext";
    const CONTROL_NUMERIC = "numeric";
    const CONTROL_CALENDAR = "calendar";
    const CONTROL_MULTI_TEXT = "multitext";
    const CONTROL_RICH_EDIT = "richedit";
    const CONTROL_LIST = "list";
    const CONTROL_HIDDEN = "hidden";
    const CONTROL_CHECKBOX = "checkbox";
    const CONTROL_FILE = "file";

    public $field_name;
    public $field_slug;
    public $field_type;
    public $field_size;
    public $control_type;
    public $control_values;

    /**
     * Automatically construct a meta data field
     * 
     * @param $field_name
     * @param $field_slug
     * @param $field_type
     * @param $field_size
     * @param $control_type
     * @param $control_values
     */

    public function __construct($field_name, $field_slug, $field_type, $field_size, $control_type, $control_values)
    {
        $this->field_name = $field_name;
        $this->field_slug = $field_slug;
        $this->field_type = $field_type;
        $this->field_size = $field_size;
        $this->control_type = $control_type;
        $this->control_values = $control_values;
    }
}
