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

    private function media_thumbnail_list($page_title, $page_content, $action_name, $paged = true, $page_number = 1)
    {
        // Tabular layout data here

        $records = \Fuel\Core\DB::select(array('id', 'id_field'), array('media_item', 'thumbnail_field'),
            array('media_description', 'description_field'))->from(self::MEDIA_EXTENSION);

        $records->order_by('id', 'desc');

        $pagination_records = \CMSUtil::create_pagination_records(self::MEDIA_EXTENSION, $records, $paged, $page_number,
            $this->controller_path, $action_name, 20);

        $record_array = $records->as_object()->execute();

        // Embed button info

        foreach($record_array as $key => $record_array_item)
        {
            $btn_select = $this->build_bootstrap_button("select/".$record_array_item->id_field, "Select");
            $record_array[$key]->buttons = array($btn_select);
        }

        $bottom_buttons = array();

        $media_path = \Fuel\Core\Uri::base().basename(UPLOADPATH)."/media/";

        $view = \Fuel\Core\View::forge("admin/partials/thumbnail-view", array("table_rows" => $record_array,
             "page_title" => $page_title, "page_title_content" => $page_content,
             "bottom_buttons" => $bottom_buttons, "pagination_records" => $pagination_records,
             "return_path" => "media/$page_number", "media_path" => $media_path));

        return $view;
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

        $list_interface = $this->build_admin_ui_tabular_list("Pages", "View and manage pages", Page_Setup::TABLE_PAGES,
                "id", "page_title", "", true, $page_number, 20);

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

            $list_interface = $this->media_thumbnail_list("Media", "Select media items to add", "media",
                true, $page_number);

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

    public function action_selectmediaitem($media_item_id)
    {
        
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
