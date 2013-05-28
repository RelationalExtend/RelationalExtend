<?php
/**
 * Main Admin Controller for the Page Module
 *
 * @author     Ahmed Maawy and Nick Hargreaves
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;

class Controller_Admin extends \cms\Controller_CMS {
    protected $controller_path = "page/admin/";

    private $pages_url = "";
    private $media_url = "";

    private $media_active = false;

    const MEDIA_EXTENSION = "media";
    const MEDIA_PAGE_SIZE = 20;

    private function build_nav_menu_vars($pages_active, $media_active)
    {
        return array(
            "pages_class" => $pages_active ? "class='active'" : "",
            "media_class" => $media_active ? "class='active'" : "",
            "pages_link" => $this->pages_url,
            "media_link" => $this->media_url,
            "media_active" => $this->media_active,
        );
    }

    private function tag_page_media($page_id, $media_id)
    {
        $num_rows_query = \Fuel\Core\DB::select(\Fuel\Core\DB::expr("COUNT(*) AS num_rows"))->from(Page_Setup::TABLE_MEDIA)
            ->where("page_id", "=", $page_id)
            ->and_where("media_id", "=", $media_id)
            ->as_object()->execute();

        if(intval($num_rows_query[0]->num_rows) < 1)
        {
            list($insert_id, $rows_affected) = \Fuel\Core\DB::insert(Page_Setup::TABLE_MEDIA)
                ->set(array(
                          "page_id" => $page_id,
                          "media_id" => $media_id
                      ))
                ->execute();

            return $insert_id;
        }

        return 0;
    }
    
    public function before()
    {
        parent::before();

        if($this->is_extension_installed(self::MEDIA_EXTENSION))
        {
            $this->media_active = true;
        }
        else {
            $this->media_active = false;
        }

        $nav_url = \Fuel\Core\Uri::base().$this->controller_path;
        $this->pages_url = $nav_url."index";
        $this->media_url = $nav_url."media";
    }

    public function action_index($page_number = 1)
    {
        $nav_interface = \Fuel\Core\View::forge("admin/tabs", $this->build_nav_menu_vars(true, false));

        // Build the tabular view

        $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, Page_Setup::TABLE_PAGES,
            "id", "page_title", "");
        $table_view_descriptor->page_number = $page_number;
        $table_view_descriptor->page_title = "Pages";
        $table_view_descriptor->page_content = "Manage site pages";

        if($this->media_active)
            $table_view_descriptor->set_additional_buttonfields(array("Add media" => "media/{{ record_id }}/1"));

        // Build the UI

        $list_interface = $this->build_admin_ui_tabular_list($table_view_descriptor, "index");

        $main_interface = $nav_interface.$list_interface;

        $this->build_admin_interface(
            $main_interface
        );
    }

    public function action_media($record_id = 0, $page_number = 1)
    {
        if($this->media_active)
        {
            $nav_interface = \Fuel\Core\View::forge("admin/tabs", $this->build_nav_menu_vars(false, true));

            // Get already selected media items

            $selected_items = array();

            $selected_records = \Fuel\Core\DB::select("media_id")->from(Page_Setup::TABLE_MEDIA)
                    ->where("page_id" ,"=" , $record_id)->as_object()->execute();

            foreach($selected_records as $selected_record)
            {
                $selected_items[] = $selected_record->media_id;
            }

            // Build the thumbnail view

            $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, self::MEDIA_EXTENSION,
                "id", "media_description", "media_item");

            $table_view_descriptor->page_number = $page_number;
            $table_view_descriptor->page_title = "Page media";
            $table_view_descriptor->page_content = "Manage media linked to pages";

            $table_view_descriptor->add_button_visible = false;
            $table_view_descriptor->delete_button_visible = false;
            $table_view_descriptor->edit_button_visible = false;

            if(count($selected_items) > 0)
                $table_view_descriptor->records_not_in = array('id' => $selected_items);
            
            $table_view_descriptor->set_additional_buttonfields(array("Select" => "addmedia/$record_id/{{ record_id }}/$page_number"));

            // Build the UI

            $list_interface = $this->build_admin_ui_thumbnail_list($table_view_descriptor, "media");

            $main_interface = $nav_interface.$list_interface;
    
            $this->build_admin_interface(
                $main_interface
            );
        }
        else
        {
            throw new \Fuel\Core\HttpNotFoundException();
        }
    }

    public function action_addmedia($record_id, $media_id, $page_number)
    {
        // Add media to this page or to all pages
        if($this->media_active)
        {
            $this->tag_page_media($record_id, $media_id);

            $this->action_media($record_id, $page_number);
        }
        else
        {
            throw new \Fuel\Core\HttpNotFoundException();
        }
    }

    public function special_field_operation($object, $field_name, $value_sets)
    {
        $value = null;
		
		// If object name is needed
		$object_name = $value_sets['object_name'];

        switch($field_name) {
            case "page_slug":
                $value = \Utility::slugify($value_sets["page_title"], "-");
                break;

            case "page_creation_time":
                $value = false;
                break;
        }

        return $value;
    }
}
