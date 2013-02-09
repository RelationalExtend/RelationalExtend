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

    public function action_index($page_number = 1)
    {
        $this->build_admin_interface(
            $this->build_admin_ui_tabular_list("Pages", "View and manage pages", Page_Setup::TABLE_PAGES,
                "id", "page_title", true, $page_number, 20)
        );
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
