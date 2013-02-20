<?php
/**
 * Main Admin controller for the Media extension.
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace media;

class Controller_Admin extends \cms\Controller_CMS {
    protected $controller_path = "media/admin/";

    public function before()
    {
        parent::before();
    }

    public function action_index($page_number = 1)
    {
        $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, Media_Setup::TABLE_MEDIA,
                "id", "media_description", "media_item");
        $table_view_descriptor->page_number = $page_number;
        $table_view_descriptor->page_title = "Media";
        $table_view_descriptor->page_content = "Manage site media";

        $this->build_admin_interface(
            $this->build_admin_ui_thumbnail_list($table_view_descriptor, "index")
        );
    }

    public function action_update($media_path = null)
    {
        parent::action_update(Media_Setup::TABLE_MEDIA);
    }

    protected function special_field_operation($field_name, $value_sets)
    {
        $value = null;

        switch($field_name) {
            case "media_creation_time":
                $value = false;
                break;
        }

        return $value;
    }
}
