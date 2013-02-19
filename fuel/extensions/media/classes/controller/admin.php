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

    private $object_slug = null;
    private $object_id = 0;

    public function before()
    {
        parent::before();
    }

    public function action_index($page_number = 1, $object_slug = null, $object_id = 0)
    {
        $this->object_slug = $object_slug;
        $this->object_id = $object_id;

        $this->build_admin_interface(
            $this->build_admin_ui_thumbnail_list("Media items", "View and manage media items", Media_Setup::TABLE_MEDIA,
                "id", "media_item", $object_slug, $object_id, "", true, $page_number, 20)
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
            case "object_slug":
                $value = $this->object_slug;
                break;

            case "object_id":
                $value = $this->object_id;
                break;

            case "media_creation_time":
                $value = false;
                break;
        }

        return $value;
    }
}
