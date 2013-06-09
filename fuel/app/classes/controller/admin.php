<?php

/**
 * Main Admin Controller. We will be extending this in the sub classes
 *
 * @author     Ahmed Maawy and Mostafa Elaghil
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Controller_Admin extends Controller_Template {

    // Constants

    const MENU_DASHBOARD = "index";
    const MENU_CONTENT = "content";
    const MENU_THEMES = "themes";
    const MENU_NAVIGATION = "navigation";
    const MENU_EXTENSIONS = "extensions";
    const MENU_SETTINGS = "settings";
    const MENU_USERS = "users";
    const MENU_LOGOUT = "logout";
    
    const PAGINATION_ENABLED = "pagination_enabled";
    const PAGINATION_NUM_PAGES = "num_pages";
    const PAGINATION_CURRENT_PAGE = "current_page";
    const PAGINATION_LINK = "pagination_link";
    const PAGINATION_SIZE = "pagination_size";

    const FUNC_ADMIN_SETTINGS = "admin_settings";
    const FUNC_ADMIN_NAVIGATION = "admin_navigation";
    const FUNC_ADMIN_USERS = "admin_users";
    const MEDIA_UPLOAD_PATH = "media/";

    // Protected variables

    protected $user_id = 0;
    protected $user_details = null;
    protected $logged_in = false;

    protected $admin_settings_function = false;
    protected $admin_navigation_function = false;
    protected $admin_users_function = false;
    
	protected $user_can_access_content = true;
	protected $user_can_access_developer = true;
	protected $user_can_access_admin = true;

    protected $is_abstract_controller = true;

    protected $controller_path = "admin/";
    protected $action_path = null;
    protected $default_admin_extension_path = "admin/";
    protected $logout_path = "cms/cms/logout";
	
	protected $msh_site = false;

    // Admin layout

    public $template = "admin/layout";

    /**
     * Validate if the page has been authenticated
     *
     * @return bool
     */

    protected function validate_authentication()
    {
        // Within any of the public URLs?

        $uri_string = explode('/', Uri::string());

        if (count($uri_string) > 2 and ($uri_string[0] == 'cms' and $uri_string[1] == 'cms') and
                ($uri_string[2] == 'login' or $uri_string[2] == 'signup'))
        {
            $this->logged_in = false;

            return true;
        }

        // Validation data here. Returns boolean status of valid authentication

        if(Auth::check())
        {
            $user = Auth::instance()->get_user_id();
            $this->user_id = $user[1];
            $this->user_details = $this->get_user_details($this->user_id);
			
			$this->user_can_access_admin = false;
			$this->user_can_access_developer = false;
			$this->user_can_access_content = false;
			
			// Set up roles
			
			$roles = Auth::group()->get_roles($this->user_details["group"]);
			
			if(in_array("Admin", $roles))
				$this->user_can_access_admin = true;
			
			if(in_array("Developer", $roles))
				$this->user_can_access_developer = true;
			
			if(in_array("Content", $roles))
				$this->user_can_access_content = true;
            
            $this->logged_in = true;

            return true;
        }
        else
        {
            $this->template->logged_in = false;
            Response::redirect(Uri::base()."cms/cms/login");

            $this->logged_in = false;

            return false;
        }
    }

	/**
     * Check on authentication status
     *
     * @param string $login_url
     * @param bool $redirect
     * @return bool
     */

    public function check_auth_status($login_url = "admin/login", $redirect = false)
    {

        $this->public_urls = array(
            "login",
            "signup"
        );

        $uri_string = explode('/', Uri::string());

        $num_params = count($uri_string);

        if ($uri_string[$num_params - 2] == 'admin' and
                ($uri_string[$num_params -1] == 'login' or $uri_string[$num_params -1] == 'signup' or
                 $uri_string[$num_params -1] == 'dologin'))
        {
            return false;
        }
        else
        {
            if(Auth::check())
            {
                $user = Auth::instance()->get_user_id();
                $this->user_id = $user[1];
                $this->user_details = $this->get_user_details($this->user_id);

                return true;
            }
            else
            {
                if($redirect) {
                    Response::redirect(Uri::base().$login_url);
                }
                else {
                    return false;
                }
            }
        }
    }

	/**
	 * Check to see if access is available to admin level
	 */

	protected function check_access_level_admin()
	{
		if(!$this->user_can_access_admin)
		{
			Response::redirect(Uri::base().$this->controller_path."unauthorizedaccess");
		}
	}
	
	/**
	 * Check to see if access is available to developer level
	 */
	
	protected function check_access_level_developer()
	{
		if(!$this->user_can_access_developer)
		{
			Response::redirect(Uri::base().$this->controller_path."unauthorizedaccess");
		}
	}
	
	/**
	 * Check to see if access is available to content level
	 */
	
	protected function check_access_level_content()
	{
		if(!$this->user_can_access_content)
		{
			Response::redirect(Uri::base().$this->controller_path."unauthorizedaccess");
		}
	}

    /**
     * Builds a menu item into a standard object
     *
     * @param $text
     * @param $link
     * @param bool $active
     * @return menu_item
     */

    protected function build_menu_item($text, $link, $active = false)
    {
        $menu_item = new stdClass();

        $menu_item->text = $text;
        $menu_item->link = Uri::base().$link;
        $menu_item->active = $active ? "class = 'active'" : "";

        return $menu_item;
    }

    /**
     * Builds the active extensions menu items
     * 
     * @return array of menu items
     */

    protected function build_active_extensions_menu()
    {
        $menu_items = array();
        $active_extensions = Extension::get_installed_extensions(true);

        foreach($active_extensions as $active_extension) {
            $menu_items[] = $this->build_menu_item(
                $active_extension->extension_name, $active_extension->extension_folder."/admin/index");
        }

        return $menu_items;
    }

    /**
     * Builds the admin UI menu items
     *
     * @return array of links
     */

    // Core menu items

    protected function build_admin_menu()
    {
        $links = array(
            self::MENU_DASHBOARD => $this->build_menu_item("Dashboard", $this->default_admin_extension_path.self::MENU_DASHBOARD),
            self::MENU_CONTENT => $this->build_active_extensions_menu(),
      	);
		
		if(!$this->msh_site)
		{
			// Themes and extensions	
				
			$links[self::MENU_THEMES] = $this->build_menu_item("Themes", $this->default_admin_extension_path.self::MENU_THEMES);
			$links[self::MENU_EXTENSIONS] = $this->build_menu_item("Extensions", $this->default_admin_extension_path.self::MENU_EXTENSIONS);
			
			// Navigation
			
			if($this->admin_navigation_function)
            	$links[self::MENU_NAVIGATION] = $this->build_menu_item("Navigation", $this->default_admin_extension_path.self::MENU_NAVIGATION);
		}

     	if($this->admin_settings_function)
            $links[self::MENU_SETTINGS] = $this->build_menu_item("Settings", $this->default_admin_extension_path.self::MENU_SETTINGS);

     	if($this->admin_users_function){
            $links[self::MENU_USERS] = $this->build_menu_item("Users", $this->default_admin_extension_path.self::MENU_USERS);
     	}
		
		if(Auth::check()){
        	$links[self::MENU_LOGOUT] = $this->build_menu_item("Logout", $this->logout_path);
        }
		
        return $links;
    }

    /**
     * Get metadata for a specific table / object
     *
     * @param $object_name
     * @return meta_data
     */

    protected function get_object_meta_data($object_name)
    {
        $object_meta_data = new ObjectModel_ObjectMetaData($object_name);
        return $object_meta_data->get_meta_data();
    }

    /**
     * Feeds items to a tabular layout template
     *
     * @param $tabular_view_descriptor
     * @param $action_name
     * @return view content
     */
    protected function build_admin_ui_tabular_list($tabular_view_descriptor, $action_name)
    {
        // Tabular layout data here

        $pagination_records = $tabular_view_descriptor->paged_results($action_name);
        $record_array = $tabular_view_descriptor->execute();

        $view = View::forge("admin/partials/table-view", array("table_rows" => $record_array,
             "page_title" => $tabular_view_descriptor->page_title, "page_title_content" => $tabular_view_descriptor->page_content,
             "bottom_buttons" => $tabular_view_descriptor->get_bottom_buttons(), "pagination_records" => $pagination_records,
             "return_path" => $tabular_view_descriptor->return_path, "additional_fields" => $tabular_view_descriptor->get_additional_fields(),
			 "column_titles" => $tabular_view_descriptor->column_titles, "bulk_actions_enabled" => $tabular_view_descriptor->is_bulk_actions_enabled(),
			 "bulk_actions" => $tabular_view_descriptor->get_bulk_actions(), "controller_path" => Uri::base().$this->controller_path,
			 "return_path" => $tabular_view_descriptor->return_path));

        return $view;
    }

    /**
     * Feeds items to a thumbnail view (must be images)
     *
     * @param $tabular_view_descriptor
     * @param $action_name
     * @return view content
     */

    protected function build_admin_ui_thumbnail_list($tabular_view_descriptor, $action_name)
    {
        // Tabular layout data here

        $pagination_records = $tabular_view_descriptor->paged_results($action_name);
        $record_array = $tabular_view_descriptor->execute();

        $media_path = Uri::base().basename(UPLOADPATH)."/media/";
        
        $view = View::forge("admin/partials/thumbnail-view", array("table_rows" => $record_array,
             "page_title" => $tabular_view_descriptor->page_title, "page_title_content" => $tabular_view_descriptor->page_content,
             "bottom_buttons" => $tabular_view_descriptor->get_bottom_buttons(), "pagination_records" => $pagination_records,
             "return_path" => $tabular_view_descriptor->return_path, "media_path" => $media_path));

        return $view;
    }

    /**
     * The UI that renders a page that displays the data to be created / edited
     *
     * @param $page_title
     * @param $page_content
     * @param $table_slug
     * @param $form_action
     * @param int $record_id
     * @param string $return_path
     * @return view content
     */
    
    protected function build_admin_ui_form_view($page_title, $page_content, $table_slug, $form_action,
        $record_id = 0, $return_path = "")
    {
        // Record view data here

        $form_view = new ObjectModel_FormView($table_slug, $record_id, $return_path);
        $form_view->page_title = $page_title;
        $form_view->page_content = $page_content;
        $form_view->form_action = $form_action;
        $form_view->preset_form_fields = $this->preset_form_fields($table_slug);

        $view = View::forge("admin/partials/record-view", array("page_rows" => $form_view->get_object_meta_data(),
                    "record_id" => $record_id, "page_title" => $page_title, "page_title_content" => $page_content,
                    "form_action" => Uri::base().$this->controller_path.$form_view->form_action, "object" => $table_slug,
                    "record" => $form_view->get_records(), "form_values" => $form_view->preset_form_fields,
                    "return_path" => $form_view->return_path));

        return $view;
    }

    /**
     * Builds the admin interface layout
     *
     * @param $html_body
     * @param $html_head
     * @return void
     */

    protected function build_admin_interface($html_body, $html_head = null)
    {
        $html_head_segment = $html_head;

        if($html_head == null)
            $html_head_segment = View::forge("admin/partials/html-head");

        $this->template->menu_items = $this->build_admin_menu();
        $this->template->html_head = $html_head_segment;
        $this->template->html_body = $html_body;
    }

    /**
     * Gets the previous referring URL
     *
     * @return referring url
     */

    protected function get_referring_url()
    {
        if(isset($_SERVER["HTTP_REFERER"]))
            return $_SERVER["HTTP_REFERER"];
        else
            return Uri::base().$this->controller_path;
    }

    /**
     * Build a bootstrap button
     *
     * @param $url
     * @param $text
     * @return stdClass
     */

    protected function build_bootstrap_button($url, $text)
    {
        return AdminHelpers::build_bootstrap_button($this->controller_path, $url, $text);
    }

    /**
     * Override this for special fields
     *
	 * @param $object
     * @param $field_name
     * @param $value_sets
     * @return null
     */

    public function special_field_operation($object, $field_name, $value_sets)
    {
        // To be overridden
        return null;
    }

    /**
     * Override this to set form field values
     *
     * @return array()
     */

    protected function preset_form_fields($table_slug)
    {
        // To be overridden
        return array();
    }

    /**
     * Get user details
     *
     * @param $user_id
     * @return null
     */

    private function get_user_details($user_id)
    {
        $user = DB::select("*")->from("users")->where("id", "=", $user_id);
        $user = $user->execute()->as_array();

        if(count($user) > 0) {
            return $user[0];
        }

        return null;
    }

    /**
     * Get all users
     *
     * @return null
     */

    public function get_all_users()
    {
        $user = DB::select("*")->from("users")->order_by("username");
        $user = $user->execute()->as_array();

        if(count($user) > 0) {
            return $user;
        }

        return null;
    }

    /**
     * Gets extension installation status
     *
     * @param $extension
     * @return bool
     */

    protected function is_extension_installed($extension)
    {
        return CMSInit::is_extension_installed($extension);
    }

    /**
     * Gets theme installation status
     *
     * @param $theme
     * @return bool
     */

    protected function is_theme_installed($theme)
    {
        return CMSInit::is_theme_installed($theme);
    }
	
	/**
	 * Gets a setting for an extension
	 * 
	 * @param $extension_slug
	 * @param $extension_setting_slug
	 * @return setting
	 */

	protected function get_extension_setting($extension_slug, $extension_setting_slug)
	{
		$extension = Extension::get_installed_extension_meta_data_by_slug($extension_slug);
		
		if(count($extension) < 1)
			throw new Exception_Extension("Extension $extension_slug does not exist");
		
		$extension_id = $extension[0]->extension_id;
		
		$setting = Extension::get_extension_settings($extension_id, $extension_setting_slug);
		
		if(count($setting) < 1)
			throw new Exception_Extension("Extension setting $extension_setting_slug does not exist");
			
		return $setting[0]->value;
	}
	
	/**
	 * Gets a setting for the site
	 * 
	 * @param $site_setting_slug
	 * @return setting
	 */
	
	protected function get_site_setting($site_setting_slug)
	{
		$setting = Extension::get_extension_settings(0, $site_setting_slug);
		
		if(count($setting) < 1)
			throw new Exception_Extension("Site setting $site_setting_slug does not exist");
			
		return $setting[0]->value;
	}
	
	/**
	 * Gets selected checkboxes with ids
	 * 
	 * @return array of ids
	 */
	
	protected function get_selected_checkboxes()
	{
		$selected_items = array();
		$selected_boxes = Input::post("chk_ids", null);
		
		if(is_array($selected_boxes))
		{
			foreach($selected_boxes as $selected_box)
			{
				$selected_items[] = $selected_box;
			}
		}
		
		return $selected_items;
	}
	
	/**
	 * Gets the selected bulk action
	 * 
	 * @return action_name
	 */
	
	protected function get_selected_bulk_action()
	{
		return Input::post("submit", null);
	}
	
	/**
	 * Gets the return path, if it exists in a post
	 * 
	 * @return action_name
	 */
	
	protected function get_form_return_path()
	{
		return Input::post("return_path", null);
	}
	
	/**
	 * Build a confirmation message
	 * 
	 * @return view
	 */
	
	protected function build_confirm_message($message, $confirm_url)
	{
		$url_from = rtrim($this->get_referring_url(), "/")."/";
        $url_from = str_replace(Uri::base().$this->controller_path, "", $url_from);
		
		$url_to = rtrim($confirm_url, "/");
        $url_to = str_replace(Uri::base().$this->controller_path, "", $url_to);

        $btn_yes = $this->build_bootstrap_button($url_to, "Yes");
        $btn_no = $this->build_bootstrap_button($url_from, "No");

        return View::forge("admin/partials/default-confirm", 
        	array("message" => $message,"buttons" => array($btn_yes, $btn_no)));
	}
	

    /**
     * The first function to be executed before the controller is executed fully
     */

    public function before()
    {
        if($this->is_abstract_controller)
            throw new HttpNotFoundException();

        parent::before();
        
        CMSInit::init_componenets();

        if(method_exists($this, self::FUNC_ADMIN_SETTINGS))
            $this->admin_settings_function = true;

        if(method_exists($this, self::FUNC_ADMIN_NAVIGATION))
            $this->admin_navigation_function = true;

        if(method_exists($this, self::FUNC_ADMIN_USERS))
            $this->admin_users_function = true;

        // Activate authentication if authentication system works

        if($this->admin_users_function)
            $this->validate_authentication();
    }

    public function action_unauthorizedaccess()
	{
		$this->build_admin_interface(
			View::forge("admin/partials/unauthorized-access")
		);
	}

    // Default actions

    /**
     * Admin UI dashboard
     *
     * @return void
     */

    public function action_index()
    {
        $this->check_access_level_content();
			
		// Get data to render in the dashboard	
        
        $installed_extensions = Extension::get_installed_extensions(true);
        $installed_themes = CMSTheme::get_installed_themes();
		
		foreach($installed_extensions as $extension)
		{
			$control_panel_button = AdminHelpers::build_bootstrap_button("", $extension->extension_folder."/admin", "Control Panel");
			$extension->buttons = AdminHelpers::bootstrap_buttons(array($control_panel_button));
		}
		
		foreach($installed_themes as $theme)
        {
        	$manage_button = AdminHelpers::build_bootstrap_button("", $this->controller_path."managetheme/".$theme->theme_id, "Manage theme");
			$theme->buttons = AdminHelpers::bootstrap_buttons(array($manage_button));
        }
		
        $this->build_admin_interface(
            View::forge("admin/partials/default-dashboard",
				array("extensions" => $installed_extensions, "themes" => $installed_themes))
        );
    }

    /**
     * Default edit interface
     *
     * @param $table
     * @param int $record_id
     * @param string $return_path
     * @param string $success_string
     * @return void
     */

    public function action_edit($table, $record_id = 0, $return_path = "", $success_string = "")
    {
    	$this->check_access_level_content();
		
        // Default edit interface

        $action = "Edit";
        $records = null;

        if($record_id == 0)
            $action = "Add";

        $page_title = "$action $table record";
        $view = "";

        if($success_string != "success")
            $view = "admin/partials/default-edit";
        else
            $view = "admin/partials/default-edit-success";

        $page_content = View::forge($view, array("action" => $action, "table" => $table));

        $this->build_admin_interface(
            $this->build_admin_ui_form_view($page_title, $page_content, $table, "update", $record_id, $return_path)
        );
    }

    /**
     * Default update action
     *
     * @param null $media_upload_path
     * @return void
     */

    public function action_update($media_upload_path = null)
    {
    	$this->check_access_level_content();
		
        $record_id = Input::post("record_id");
        $object = Input::post("object");
        $save_value = Input::post(AdminHelpers::save_button_value(), false);
        $save_and_exit_value = Input::post(Utility::slugify(AdminHelpers::save_and_exit_button_value()), false);

        $url_from = $this->get_referring_url();

        $object_meta_data = $this->get_object_meta_data($object);
        $update_object = null;

        if($object_meta_data == null)
            throw new Exception_CMS("The object $object does not exist in the database");
		
		// Get the stuff to save to the database

        $database_array = ObjectModel_FormView::get_records_to_save($object, $object_meta_data, $this);

        // Save records and get record ids

        $result = ObjectModel_FormView::save_records_to_db($object, $record_id, $object_meta_data, $database_array, $media_upload_path);
		$last_insert_id = ObjectModel_FormView::$last_insert_id;
		
		if($record_id > 0)
			$last_insert_id = $record_id;

        // Finalize
        
        $this->after_update($last_insert_id, $media_upload_path);

        $strlen_url_from = strlen($url_from);

        if($save_value != false || $save_and_exit_value != false) {
            if($save_and_exit_value) {
                $redirect_url = Uri::base().$this->controller_path;

                if(trim(Input::post("return_path")) != "")
                {
                    if(trim(Input::post("return_path")) != "null") {
                        $redirect_url = rtrim($redirect_url, "/")."/".ltrim(Input::post("return_path"), "/");
                    }
                }
                
                Response::redirect($redirect_url);
            }
            elseif($save_value) {
				
                $redirect_url = strpos($url_from, "/null/success", $strlen_url_from - 8) == FALSE ? "$url_from/null/success" : $url_from;
                Response::redirect($redirect_url);
            }
        }
    }

	/**
     * Default post_update action
     *
	 * @param record_id
     * @param null $media_upload_path
     * @return status
     */

	protected function after_update($record_id, $media_upload_path = null)
	{
		return true;
	}

    /**
     * Default delete interface
     *
     * @param $table
     * @param $record_id
     * @param int $confirm
     * @return void
     */

    public function action_delete($table, $record_id, $confirm = 0)
    {
    	$this->check_access_level_content();
		
        // Default delete interface

        $url_from = rtrim($this->get_referring_url(), "/")."/";

        $url_from = str_replace(Uri::base().$this->controller_path, "", $url_from);

        $btn_yes = $this->build_bootstrap_button("delete/$table/$record_id/1", "Yes");
        $btn_no = $this->build_bootstrap_button($url_from, "No");

        if($confirm == 1) {
            DB::delete($table)->where("id", "=", $record_id)->execute();
            Response::redirect(Uri::base().$this->controller_path."index");
        }

        $this->build_admin_interface(
            View::forge("admin/partials/default-delete", array("buttons" => array($btn_yes, $btn_no)))
        );
    }

    // Core actions

    /**
     * Themes core action
     *
     * @return void
     */

    public function action_themes()
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        $core_themes = CMSTheme::get_public_themes();

        // Autoload not installed themes for their meta data

        foreach($core_themes as $core_theme) {
            if(!Module::loaded(basename($core_theme))) {
                CMSInit::load_theme(basename($core_theme));
            }
        }

        $core_theme_meta_data = CMSTheme::get_public_theme_meta_data($core_themes);
        $installed_themes = CMSTheme::get_installed_themes();

        foreach($core_theme_meta_data as $theme_folder => &$theme_data) {
            $theme_installed = false;
            $theme_id = 0;
            $theme_active = 0;

            foreach($installed_themes as $installed_theme) {
                if($installed_theme->theme_folder == $theme_folder) {
                    $theme_installed = true;
                    $theme_id = $installed_theme->theme_id;
                    $theme_active = $installed_theme->theme_active;
                }
            }

            if($theme_installed) {
                $activate_button = $this->build_bootstrap_button("activatetheme/$theme_id", "Activate");
                $uninstall_button = $this->build_bootstrap_button("uninstalltheme/$theme_id", "Delete");
				$manage_button = $this->build_bootstrap_button("managetheme/$theme_id", "Manage");

                if($theme_active == 1) {
                    $theme_data["buttons"] = array($uninstall_button, $manage_button);
                }
                else {
                    $theme_data["buttons"] = array($activate_button, $uninstall_button, $manage_button);
                }
            }
            else {
                $install_only_button = $this->build_bootstrap_button("installtheme/$theme_folder/0", "Install");
                $install_and_activate_button = $this->build_bootstrap_button("installtheme/$theme_folder/1", "Install and Activate");

                $theme_data["buttons"] = array($install_only_button, $install_and_activate_button);
            }
        }

        $this->build_admin_interface(
            View::forge("admin/partials/themes", array("page_title" => "Themes",
                "page_title_content" => "Manage the site's themes",
                "themes" => $core_theme_meta_data))
        );
    }

    /**
     * Installs a theme at a specific theme index
     *
     * @param $theme_folder
     * @param int $activate
     * @return void
     */

    public function action_installtheme($theme_folder, $activate = 0)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        if(!Module::loaded($theme_folder)) {
            CMSInit::load_theme($theme_folder);
        }

        $core_themes = CMSTheme::get_public_themes();
        $core_themes_meta_data = CMSTheme::get_public_theme_meta_data($core_themes);

        $selected_theme = $core_themes_meta_data[$theme_folder];
        $theme_folder =$selected_theme[ThemeInfo::SEGMENT_THEME_FOLDER];

        CMSTheme::install_theme(THEMEPATH.$theme_folder.DIRECTORY_SEPARATOR, ($activate == 1 ? true : false));

        Response::redirect(Uri::base().$this->controller_path."themes");
    }

    /**
     * Uninstalls a theme
     *
     * @param $theme_id
     * @return void
     */

    public function action_uninstalltheme($theme_id, $confirm = 0)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
    	$url_from = $this->get_referring_url();

        $url_from = str_replace(Uri::base().$this->controller_path, "", $url_from);

        $btn_yes = $this->build_bootstrap_button("uninstalltheme/$theme_id/1", "Yes");
        $btn_no = $this->build_bootstrap_button($url_from, "No");
		
		if($confirm == 1)
		{
			// Confirm delete
			CMSTheme::uninstall_theme($theme_id);
	        Response::redirect(Uri::base().$this->controller_path."themes");	
		}
		else 
		{
			// Show interface to confirm
		 	$this->build_admin_interface(
	            View::forge("admin/partials/theme-delete", array("buttons" => array($btn_yes, $btn_no)))
	        );
		}
    }

    /**
     * Activates a specific theme
     *
     * @param $theme_id
     * @return void
     */

    public function action_activatetheme($theme_id)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        CMSTheme::set_default_theme($theme_id);

        Response::redirect(Uri::base().$this->controller_path."themes");
    }
	
	/**
	 * Manage individual CMS theme components
	 * 
	 * @param theme_id
	 * @param theme_component
	 * @param component_id
	 */
	
	public function action_managetheme($theme_id, $theme_component = CMSTheme::LAYOUT_PREFIX, $component_id = 0, $confirm = 0)
	{
		if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
		$this->check_access_level_developer();
					
		if(intval($theme_id) < 1)
			throw new Exception_Theme("Invalid theme selected");	
			
		$language_mode = "";
		$editor_code = "";
		
		switch($theme_component)
		{
			case CMSTheme::LAYOUT_PREFIX:
				$language_mode = ObjectModel_CodeView::LAYOUT_VIEW;
				
				if($component_id > 0)
				{
					$editor_code = CMSTheme::get_installed_layout_content("", $theme_id, $component_id);
				}
				
				break;
			case CMSTheme::PARTIAL_PREFIX:
				$language_mode = ObjectModel_CodeView::PARTIAL_VIEW;
				
				if($component_id > 0)
				{
					$editor_code = CMSTheme::get_installed_partial_content("", $theme_id, $component_id);
				}
				
				break;
			case CMSTheme::JS_PREFIX:
				$language_mode = ObjectModel_CodeView::JS_VIEW;
				
				if($component_id > 0)
				{
					$editor_code = CMSTheme::get_installed_javascript_content("", $theme_id, $component_id);
				}
				
				break;
			case CMSTheme::CSS_PREFIX:
				$language_mode = ObjectModel_CodeView::CSS_VIEW;
				
				if($component_id > 0)
				{
					$editor_code = CMSTheme::get_installed_style_content("", $theme_id, $component_id);
				}
				
				break;
		}
		
		$code_view = new ObjectModel_CodeView($language_mode);
		$theme_data = CMSTheme::get_theme_components($theme_id);
		
		$sidebar = View::forge("admin/partials/code-editor-sidebar", 
			array("controller_path" => Uri::base().$this->controller_path,
				"theme_id" => $theme_id,
				"layouts" => $theme_data->{CMSTheme::LAYOUT_PREFIX},
				"partials" => $theme_data->{CMSTheme::PARTIAL_PREFIX},
				"javascripts" => $theme_data->{CMSTheme::JS_PREFIX},
				"stylesheets" => $theme_data->{CMSTheme::CSS_PREFIX}));
		
		$code_view_editor = "";
		
		if($component_id == 0)
		{
			$code_view_editor = View::forge("admin/partials/code-editor-empty");
		}
		else
		{
			$component_name = "";
			$component_slug = "";
			
			switch($theme_component)
			{
				case CMSTheme::LAYOUT_PREFIX:
					
					foreach($theme_data->{CMSTheme::LAYOUT_PREFIX} as $layout)
					{
						if($layout->theme_layout_id == $component_id)
						{
							$component_name = $layout->theme_layout_name;
							$component_slug = $layout->theme_layout_slug;
						}
					}
					
					break;
				case CMSTheme::PARTIAL_PREFIX:
					
					foreach($theme_data->{CMSTheme::PARTIAL_PREFIX} as $partial)
					{
						if($partial->theme_partial_id == $component_id)
						{
							$component_name = $partial->theme_partial_name;
							$component_slug = $partial->theme_partial_slug;
						}
					}
					
					break;
				case CMSTheme::JS_PREFIX:
					
					foreach($theme_data->{CMSTheme::JS_PREFIX} as $javascript)
					{
						if($javascript->theme_js_id == $component_id)
						{
							$component_name = $javascript->theme_js_name;
							$component_slug = $javascript->theme_js_slug;
						}
					}
					
					break;
				case CMSTheme::CSS_PREFIX:
					
					foreach($theme_data->{CMSTheme::CSS_PREFIX} as $style)
					{
						if($style->theme_css_id == $component_id)
						{
							$component_name = $style->theme_css_name;
							$component_slug = $style->theme_css_slug;
						}
					}
					
					break;
			}
			
			$code_view_editor = View::forge("admin/partials/code-editor-code-panel",
				array("editor_code" => $editor_code,
					"editor_language" => $code_view->get_code_language(),
					"controller_path" => $this->controller_path,
					"theme_id" => $theme_id,
					"theme_component" => $theme_component,
					"component_id"=> $component_id,
					"confirm" => $confirm,
					"component_name" => $component_name,
					"component_slug" => $component_slug));	
		}
		
		$this->build_admin_interface(View::forge("admin/partials/code-editor",
			array("editor_sidebar" => $sidebar, "editor_editor" => $code_view_editor)));
	}

	/**
	 * Saves a specific component of a theme
	 * 
	 * @param theme_id
	 * @param theme_component
	 * @param component_id
	 */

	public function action_savethemecomponent($theme_id, $theme_component = CMSTheme::LAYOUT_PREFIX, $component_id = 0)
	{
		if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
		$this->check_access_level_developer();
		
		$content = Input::post("code", false);
		
		if($content != false)
		{
			switch($theme_component)
			{
				case CMSTheme::LAYOUT_PREFIX:
					CMSTheme::save_layout_component($theme_id, $component_id, $content);
					break;
				case CMSTheme::PARTIAL_PREFIX:
					CMSTheme::save_partial_component($theme_id, $component_id, $content);
					break;
				case CMSTheme::JS_PREFIX:
					CMSTheme::save_javascript_component($theme_id, $component_id, $content);
					break;
				case CMSTheme::CSS_PREFIX:
					CMSTheme::save_style_component($theme_id, $component_id, $content);
					break;
			}
			
			Response::redirect($this->controller_path."managetheme/$theme_id/$theme_component/$component_id/1");
		}
		
		Response::redirect($this->controller_path."managetheme/$theme_id/$theme_component/$component_id/0");
	}

    /**
     * The navigation management module
     *
     * @return void
     */

    public function action_navigation($confirm = 0)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_content();
		
        if($this->admin_navigation_function) {
            $this->admin_navigation($confirm);
        }
        else {
           throw new HttpNotFoundException();
        }
    }

    /**
     * The extensions management module
     *
     * @return void
     */

    public function action_extensions()
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        $core_extensions = Extension::get_core_extensions();

        // Autoload not installed extensions to as to get the meta data

        foreach($core_extensions as $core_extension) {
            if(!Module::loaded(basename($core_extension))) {
                CMSInit::load_extension(basename($core_extension));
            }
        }

        $core_extensions_meta_data = Extension::get_core_extension_meta_data($core_extensions);
        $installed_extensions = Extension::get_installed_extensions();

        foreach($core_extensions_meta_data as $extension_folder => &$extension_meta_data)
        {
            $extension_installed = false;
            $extension_active = 0;
            $extension_id = 0;
            
            foreach($installed_extensions as $extension) {
                if($extension->extension_folder == $extension_folder) {
                    $extension_installed = true;
                    $extension_active = $extension->extension_active;
                    $extension_id = $extension->extension_id;
                }
            }

            if($extension_installed) {
                $activate_button = $this->build_bootstrap_button("activateextension/$extension_id", "Activate");
                $deactivate_button = $this->build_bootstrap_button("deactivateextension/$extension_id", "Deactivate");
                $uninstall_button = $this->build_bootstrap_button("uninstallextension/$extension_id", "Delete");

                if($extension_active == 1) {
                    $extension_meta_data["buttons"] = array($deactivate_button, $uninstall_button);
                }
                else {
                    $extension_meta_data["buttons"] = array($activate_button, $uninstall_button);
                }
            }
            else {
                $install_only_button = $this->build_bootstrap_button("installextension/$extension_folder", "Install");

                $extension_meta_data["buttons"] = array($install_only_button);
            }
        }

        $this->build_admin_interface(
            View::forge("admin/partials/extensions", array("page_title" => "Extensions",
                "page_title_content" => "Manage the site's extensions",
                "extensions" => $core_extensions_meta_data))
        );
    }

    /**
     * Installs an extension at a specific index
     *
     * @param $extension_folder
     * @return void
     */

    public function action_installextension($extension_folder)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        if(!Module::loaded($extension_folder)) {
            CMSInit::load_extension($extension_folder);
        }

        Extension::install_extension(EXTPATH.$extension_folder.DIRECTORY_SEPARATOR);

        Response::redirect(Uri::base().$this->controller_path."extensions");
    }

    /**
     * Uninstalls an extension
     *
     * @param $extension_id
     * @return void
     */

    public function action_uninstallextension($extension_id, $confirm = 0)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        $url_from = $this->get_referring_url();

        $url_from = str_replace(Uri::base().$this->controller_path, "", $url_from);

        $btn_yes = $this->build_bootstrap_button("uninstallextension/$extension_id/1", "Yes");
        $btn_no = $this->build_bootstrap_button($url_from, "No");
			
		if($confirm == 1)
		{
			// Confirm delete
			Extension::uninstall_extension($extension_id);
        	Response::redirect(Uri::base().$this->controller_path."extensions");
		}
		else 
		{
			// Show interface to confirm
		 	$this->build_admin_interface(
	            View::forge("admin/partials/extension-delete", array("buttons" => array($btn_yes, $btn_no)))
	        );		
		}	
    }

    /**
     * Activates a specific extension
     *
     * @param $extension_id
     * @return void
     */

    public function action_activateextension($extension_id)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        Extension::activate_extension($extension_id);

        Response::redirect(Uri::base().$this->controller_path."extensions");
    }

    /**
     * Deactivates a specific extension
     *
     * @param $extension_id
     * @return void
     */

    public function action_deactivateextension($extension_id)
    {
    	if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
    	$this->check_access_level_developer();
		
        Extension::deactivate_extension($extension_id);

        Response::redirect(Uri::base().$this->controller_path."extensions");
    }

    /**
     * Settings management module
     *
     * @return void
     */

    public function action_settings($extension_id = 0, $confirm = 0)
    {
    	$this->check_access_level_admin();
		
        if($this->admin_settings_function) {
            $this->admin_settings($extension_id, $confirm);
        }
        else {
            throw new HttpNotFoundException();
        }
    }

    /**
     * Users module
     *
     * @return void
     */

    public function action_users($status = "")
    {
    	$this->check_access_level_admin();
		
        if($this->admin_users_function) {
            $this->admin_users($status);
        }
        else {
            throw new HttpNotFoundException();
        }
    }
	
	/**
     * Performs bulk action operations
     *
     * @return void
     */
	
	public function action_bulkactions()
	{
		Response::redirect(Input::post("return_path", Uri::base().$this->controller_path."index"));
	}
}
