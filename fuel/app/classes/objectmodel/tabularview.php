<?php
/**
 * Viewing tabular / thumbnail views
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class ObjectModel_TabularView {
    
    public $page_title;
    public $page_content;
    public $return_path = "";
    public $paged = true;
    public $page_number = 1;
    public $page_size = 20;
    public $action_name = null;
    public $order_by = null;
    public $direction = 'asc';
    public $controller_path = "";
    public $records_not_in = array();

    public $add_button_visible = true;
    public $edit_button_visible = true;
    public $delete_button_visible = true;

    private $additional_button_fields = array();
    private $additional_buttons = array();
    private $bottom_buttons = array();
    private $add_button = null;
    private $edit_buttons = array();
    private $delete_buttons = array();

    private $table_name;
    private $id_field;
    private $thumbnail_field;
    private $description_field;

    private $records;
    private $sort_field = "";
    private $sort_direction = "";

    private $executed = false;
    private $record_objects;

    private $pagination_records = null;

    /**
     * Constructor
     *
     * @param $controller_path
     * @param $table_name
     * @param $id_field
     * @param $description_field
     * @param null $thumbnail_field
     * @return \ObjectModel_TabularView
     *
     */

    public function __construct($controller_path, $table_name, $id_field, $description_field, $thumbnail_field = null)
    {
        $this->controller_path = $controller_path;
        $this->table_name = $table_name;
        $this->id_field = $id_field;
        $this->description_field = $description_field;
        $this->thumbnail_field = $thumbnail_field;

        if($thumbnail_field != null)
        {
            $this->records = DB::select(array($id_field, 'id_field'), array($description_field, 'description_field'),
                array($thumbnail_field, 'thumbnail_field'))->from($table_name);
        }
        else {
            $this->records = DB::select(array($id_field, 'id_field'), array($description_field, 'description_field'))
                ->from($table_name);
        }
    }

    /**
     * Sort the records
     *
     * @param $sort_field
     * @param string $sort_direction
     * @return void
     */

    public function set_sort_direction($sort_field, $sort_direction = "asc")
    {
        $this->sort_field = $sort_field;
        $this->sort_direction = $sort_direction;
        $this->records->order_by($sort_field, $sort_direction);
    }

    /**
     * Implement paging
     * @param $action_name
     *
     * @return pagination_records
     */

    public function paged_results($action_name)
    {
        $this->pagination_records = CMSUtil::create_pagination_records($this->table_name, $this->records,
            $this->paged, $this->page_number, $this->controller_path, $action_name, $this->page_size);

        return $this->pagination_records;
    }

    /**
     * Set up additional button parameters
     *
     * @param $additional_buttons
     * @return
     */

    public function set_additional_buttonfields($additional_buttons)
    {
        if(!is_array($additional_buttons))
            return;

        $this->additional_button_fields = $additional_buttons;
    }

    /**
     * Execute the query
     *
     * @return records
     */

    public function execute()
    {
        if(!$this->executed) {
            if(is_array($this->records_not_in))
            {
                if(count($this->records_not_in) > 0)
                {
                    foreach($this->records_not_in as $field => $record_not_in)
                    {
                        if(is_array($record_not_in))
                            $this->records = $this->records->where($field, 'not in', $record_not_in);
                    }
                }
            }

            $this->record_objects = $this->records->as_object()->execute();
            $this->executed = true;

            foreach($this->record_objects as $record)
            {
                $edit_button = AdminHelpers::build_bootstrap_button($this->controller_path,
                    "edit/".$this->table_name."/".$record->id_field, "Edit");
                $delete_button = AdminHelpers::build_bootstrap_button($this->controller_path,
                    "delete/".$this->table_name."/".$record->id_field, "Delete");

                if($this->edit_button_visible) {
                    $record->edit_button = $edit_button;
                    $record->buttons[] = $edit_button;
                }

                if($this->delete_button_visible) {
                    $record->delete_button = $delete_button;
                    $record->buttons[] = $delete_button;
                }
                
                $this->edit_buttons[] = $edit_button;
                $this->delete_buttons[] = $delete_button;

                if(count($this->additional_button_fields) > 0)
                {
                    foreach($this->additional_button_fields as $key => $value)
                    {
                        $button_url = str_replace("{{ table }}", $this->table_name, $value);
                        $button_url = str_replace("{{ record_id }}", $record->id_field, $button_url);
                        $additional_button = AdminHelpers::build_bootstrap_button(
                            $this->controller_path, $button_url, $key);

                        $record->additional_buttons[$key] = $additional_button;
                        $record->buttons[] = $additional_button;
                        $this->additional_buttons[][$key] = $additional_button;
                    }
                }
            }

            $this->add_button = AdminHelpers::build_bootstrap_button($this->controller_path, "edit/".$this->table_name."/0", "Add new");

            if($this->add_button_visible)
                $this->bottom_buttons = array($this->add_button);
        }

        return $this->record_objects;
    }

    /**
     * Gets the table name
     *
     * @return tablename
     */

    public function get_table_name()
    {
        return $this->table_name;
    }

    /**
     * Returns the buttons at the bottom
     *
     * @return array
     */

    public function get_bottom_buttons()
    {
        return $this->bottom_buttons;
    }
}