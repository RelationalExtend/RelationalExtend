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
	
	const FIELD_RELATIONSHIP_MASTER_FIELD_CAPTION = "ID Master";
	const FIELD_RELATIONSHIP_DETAIL_FIELD_CAPTION = "ID Detail";
	
	const FIELD_RELATIONSHIP_MASTER_FIELD = "id_master";
	const FIELD_RELATIONSHIP_DETAIL_FIELD = "id_detail";
	
	// Core controls

    const CONTROL_SIMPLE_TEXT = "simpletext";
    const CONTROL_NUMERIC = "numeric";
    const CONTROL_CALENDAR = "calendar";
    const CONTROL_MULTI_TEXT = "multitext";
    const CONTROL_RICH_EDIT = "richedit";
    const CONTROL_LIST = "list";
    const CONTROL_TABULAR_LIST = "external_list";
    const CONTROL_HIDDEN = "hidden";
    const CONTROL_CHECKBOX = "checkbox";
	const CONTROL_MULTISELECT = "multiselect";
    const CONTROL_FILE = "file";
	
	// Custom control
	
	const CONTROL_CUSTOM = "custom";
	
	// Core controls array
	
	public static $CONTROL_ARRAY = array(
		self::CONTROL_SIMPLE_TEXT => "Simple Text Box",
		self::CONTROL_NUMERIC => "Numeric Box",
		self::CONTROL_CALENDAR => "Calendar",
		self::CONTROL_MULTI_TEXT => "Multiple Line Text Box",
		self::CONTROL_RICH_EDIT => "Rich Text Editor",
		self::CONTROL_LIST => "List Control",
		self::CONTROL_TABULAR_LIST => "External Table List",
		self::CONTROL_HIDDEN => "Hidden Field",
		self::CONTROL_CHECKBOX => "Check Box",
		self::CONTROL_MULTISELECT => "Multiple Option",
		self::CONTROL_FILE => "File",
	);
	
	// Field members

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

    public function __construct($field_name, $field_slug, $field_type = null, $field_size = null, $control_type = null, 
    	$control_values = null)
    {
        $this->field_name = $field_name;
        $this->field_slug = $field_slug;
        $this->field_type = $field_type;
        $this->field_size = $field_size;
        $this->control_type = $control_type;
        $this->control_values = $control_values;
    }
}
