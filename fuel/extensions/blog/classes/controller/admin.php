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

    private $posts_url = "";
    private $categories_url = "";

    private function build_nav_menu_vars($posts_active, $categories_active)
    {
        return array(
            "posts_class" => $posts_active? "class='active'" : "",
            "categories_class" => $categories_active? "class='active'" : "",
            "posts_link" => $this->posts_url,
            "categories_link" => $this->categories_url,
        );
    }

    public function before()
    {
        parent::before();

        $nav_url = \Fuel\Core\Uri::base().$this->controller_path;
        $this->posts_url = $nav_url."index";
        $this->categories_url = $nav_url."categories";
    }

    public function action_index($page_number = 1)
    {
        $nav_interface = \Fuel\Core\View::forge("admin/tabs", $this->build_nav_menu_vars(true, false));

        $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, Blog_Setup::TABLE_BLOG,
                "id", "blog_title", "");
        $table_view_descriptor->page_number = $page_number;
        $table_view_descriptor->page_title = "Blog posts";
        $table_view_descriptor->page_content = "Manage blog posts";
		
		$table_view_descriptor->return_path = "index/$page_number";

        $list_interface = $this->build_admin_ui_tabular_list($table_view_descriptor, "index");

        $main_interface = $nav_interface.$list_interface;


        $this->build_admin_interface($main_interface);
    }

    public function action_categories()
    {
        $nav_interface = \Fuel\Core\View::forge("admin/tabs", $this->build_nav_menu_vars(false, true));

        $table_view_descriptor = new \ObjectModel_TabularView($this->controller_path, Blog_Setup::TABLE_CATEGORIES,
                "id", "blog_category", "");
        $table_view_descriptor->paged = false;
        $table_view_descriptor->page_title = "Categories";
        $table_view_descriptor->page_content = "Manage blog categories";
		
		$table_view_descriptor->return_path = "categories";

        $list_interface = $this->build_admin_ui_tabular_list($table_view_descriptor, "categories");

        $main_interface = $nav_interface.$list_interface;


        $this->build_admin_interface($main_interface);
    }

    public function special_field_operation($object, $field_name, $value_sets)
    {
        $value = null;
		
		// If object name is needed
		$object_name = $value_sets['object_name'];

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
