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
	
	const BULK_DELETE = "Delete selected";
	const BULK_ACTIVATE = "Activate selected";
	const BULK_DEACTIVATE = "Deactivate selected";

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
	
	private function bulk_delete($ids, $return_path = null)
	{
		// Enforce permissions
		$this->enforce_permission(Page_Info::PERMISSION_DELETE_PAGE);
		
		// Delete
		$pages = "<ul>";
		$ids_url = "";
		
		foreach($ids as $page_id)
		{
			$page_data = Page::get_any_page_by_id($page_id);
			
			$pages.="<li>".$page_data[0]["page_title"]."</li>";
			$ids_url.="$page_id,";
		}
		
		$pages .= "</ul>";
		$ids_url = rtrim($ids_url, ",");
		
		$url_return_path = rawurlencode($return_path);
		
		$this->build_admin_interface(
            $this->build_confirm_message(
            	"Are you sure you want to delete these pages?<br/>$pages",
            	\Fuel\Core\Uri::base().$this->controller_path."bulkdelete/$ids_url/?return=$url_return_path"
			)
        );
	}
	
	private function bulk_activate($ids, $return_path = null)
	{
		// Enforce permissions
		$this->enforce_permission(Page_Info::PERMISSION_EDIT_PAGE);
		
		// Activation
		foreach($ids as $page_id)
		{
			Page::activate_page($page_id);
		}
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path.$return_path);
	}
	
	private function bulk_deactivate($ids, $return_path = null)
	{
		// Enforce permissions
		$this->enforce_permission(Page_Info::PERMISSION_EDIT_PAGE);
		
		// Deactivation
		foreach($ids as $page_id)
		{
			Page::deactivate_page($page_id);
		}
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path.$return_path);
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

		$table_view_descriptor->set_bulk_action_buttons(array(self::BULK_DELETE, self::BULK_ACTIVATE, self::BULK_DEACTIVATE));
		$table_view_descriptor->return_path = "index/$page_number";

        // Build the UI

        $list_interface = $this->build_admin_ui_tabular_list($table_view_descriptor, "index");

        $main_interface = $nav_interface.$list_interface;

        $this->build_admin_interface(
            $main_interface
        );
    }

	public function action_edit($table, $record_id = 0, $success_string = "")
	{
		// Enforce permissions
		if($record_id == 0)
		{
			// New record
			$this->enforce_permission(Page_Info::PERMISSION_CREATE_PAGE);
		}
		else 
		{
			// Edit record
			$this->enforce_permission(Page_Info::PERMISSION_EDIT_PAGE);
		}
		parent::action_edit($table, $record_id, $success_string);
	}
	
	public function action_delete($table, $record_id, $confirm = 0)
	{
		// Enforce permissions
		$this->enforce_permission(Page_Info::PERMISSION_DELETE_PAGE);
		
		parent::action_delete($table, $record_id, $confirm = 0);
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
        // Enforce permissions
        $this->enforce_permission(Page_Info::PERMISSION_EDIT_PAGE);
		
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
	
	public function action_bulkactions()
	{
		$selected_ids = $this->get_selected_checkboxes();
		$action = $this->get_selected_bulk_action();
		$return_path = $this->get_form_return_path();
		
		switch($action)
		{
			case self::BULK_DELETE:
				$this->bulk_delete($selected_ids, $return_path);
				break;
			case self::BULK_ACTIVATE:
				$this->bulk_activate($selected_ids, $return_path);
				break;
			case self::BULK_DEACTIVATE:
				$this->bulk_deactivate($selected_ids, $return_path);
				break;
		}
	}
	
	public function action_bulkdelete($ids)
	{
		// Enforce permissions
		$this->enforce_permission(Page_Info::PERMISSION_DELETE_PAGE);
		
		// Perform bulk delete
		$return_path = \Fuel\Core\Input::get("return", null);
		
		if($return_path == null)
			$return_path = Uri::base().$this->controller_path;
		
		$ids_array = explode(",", $ids);
		
		foreach($ids_array as $page_id)
		{
			Page::delete_page($page_id);
		}
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path.$return_path);
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
