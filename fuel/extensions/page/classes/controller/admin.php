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
    
    public function before()
    {
        parent::before();

        if(\CMSInit::is_extension_installed(self::MEDIA_EXTENSION))
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

        $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, Page_Setup::TABLE_PAGES,
            "id", "page_title", "");
        $table_view_descriptor->page_number = $page_number;

        $list_interface = $this->build_admin_ui_tabular_list($table_view_descriptor, "index");

        $main_interface = $nav_interface.$list_interface;

        $this->build_admin_interface(
            $main_interface
        );
    }

    public function action_media($page_number = 1)
    {
        if($this->media_active)
        {
            $nav_interface = \Fuel\Core\View::forge("admin/tabs", $this->build_nav_menu_vars(false, true));

            $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, self::MEDIA_EXTENSION,
                "id", "media_description", "media_item");

            $table_view_descriptor->page_number = $page_number;

            $table_view_descriptor->add_button_visible = false;
            $table_view_descriptor->delete_button_visible = false;
            $table_view_descriptor->edit_button_visible = false;
            
            $table_view_descriptor->set_additional_buttonfields(array("Select" => "addmedia/{{ table }}/{{ record_id }}"));

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

    public function action_addmedia($table, $record, $page_id = 0)
    {
        // Add media to this page or to all pages
    }

    protected function special_field_operation($field_name, $value_sets)
    {
        $value = null;

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
