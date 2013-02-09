<?php
/**
 * Main Admin controller for the blog extension.
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;

class Controller_Admin extends \cms\Controller_CMS {
    protected $controller_path = "blog/admin/";

    public function action_index($page_number = 1)
    {
        $this->build_admin_interface(
            $this->build_admin_ui_tabular_list("Blog Posts", "View and manage blog posts", Blog_Setup::TABLE_BLOG,
                "id", "blog_title", true, $page_number, 20)
        );
    }

    protected function special_field_operation($field_name, $value_sets)
    {
        $value = null;

        switch($field_name) {
            case "blog_slug":
                $value = \Utility::slugify($value_sets["blog_title"], "-");
                break;

            case "blog_creation_time":
                $value = false;
                break;
        }

        return $value;
    }
}
