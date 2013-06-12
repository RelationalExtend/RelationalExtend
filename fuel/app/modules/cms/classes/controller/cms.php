<?php
/**
 * CMS
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace cms;

class Controller_CMS extends \Controller_Admin {

    protected $controller_path = "cms/cms/";
    protected $default_admin_extension_path = "cms/cms/";
    
    protected $is_abstract_controller = false;
	
	/**
	 * Gets all shortened URLs
	 * 
	 * @return array()
	 */
	
	private function get_shortened_urls()
	{
		return array();
	}
	
	/**
	 * Builds the navigation system for the users page tabs
	 * 
	 * @param $users_active
	 * @param $roles_active
	 * @param $permissions_active
	 * @param $user_roles_active
	 * @param $role_permissions_active
	 */
	
	private function build_user_nav_menu_vars($users_active, $roles_active, $permissions_active,
		$user_roles_active, $role_permissions_active)
    {
        return array(
            "users_class" => $users_active ? "class='active'" : "",
            "roles_class" => $roles_active ? "class='active'" : "",
            "permissions_class" => $permissions_active ? "class='active'" : "",
            "user_roles_class" => $user_roles_active ? "class='active'" : "",
            "role_permissions_class" => $role_permissions_active ? "class='active'" : "",
            "users_link" => \Fuel\Core\Uri::base().$this->controller_path."users",
            "roles_link" => \Fuel\Core\Uri::base().$this->controller_path."roles",
            "permissions_link" => \Fuel\Core\Uri::base().$this->controller_path."permissions",
            "user_roles_link" => \Fuel\Core\Uri::base().$this->controller_path."userroles",
            "role_permissions_link" => \Fuel\Core\Uri::base().$this->controller_path."rolespermissions",
        );
    }
	
	/**
	 * Enforce permission for current user
	 * 
	 * @param $permission_slug
	 */
	
	protected function enforce_permission($permission_slug, $force_slug = true)
	{
		$permission_slugified = $permission_slug;
		
		if($force_slug)
			$permission_slugified = \Utility::slugify($permission_slugified);
		
		if(!\Permissions::is_user_permitted($this->user_id, $permission_slugified))
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."unauthorizedaccess");
	}
	
	/**
	 * Controller initialization
	 */

   	public function before()
    {
    	\Config::load("cms::msh", "msh");
    	$this->msh_site = \Config::get("msh.msh_status");
        	
        parent::before();
    }
	
	public function action_index()
	{
		if($this->msh_site)
		{
			// Custom code
			$this->check_access_level_content();
			$this->build_admin_interface(
            	\Fuel\Core\View::forge("admin/msh-dashboard")
	        );
		}
		else 
		{
			// Default code
			parent::action_index();
		}
	}
	
	// Default auth functionality
	
	/**
     * Default login action
     *
     * @return void
     */

    public function action_login($status = "")
    {
    	$messages = array();
		
    	if($status == "nosuccess")
			$messages[] = "Invalid login user name / password";
    	
        $user_name = \Fuel\Core\Input::post("username", null);
        $password = \Fuel\Core\Input::post("password", null);
       
        if($user_name != null && $password != null)
        {   
            $auth = \Auth\Auth::instance();
            if($auth->login($user_name, $password))
			{
				\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."index");
			}    
			else {
				\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."login/nosuccess");
			}
        }
		
		// Create a default user if non exists
		
		$user_query = \Fuel\Core\DB::select(\Fuel\Core\DB::expr("COUNT(*) AS num_users"))->from("users")->as_object()->execute();
		$num_users = $user_query[0]->num_users;
		
		if($num_users < 1)
		{
			$result = \Auth\Auth::instance()->create_user("admin", "admin", "temp@email.com", "100");
		}

        $this->build_admin_interface(
            \Fuel\Core\View::forge("admin/partials/login", 
            array('form_action' => \Fuel\Core\Uri::base().$this->controller_path, 'messages' => $messages))
        );
    }
	
	/**
     * Logs out the current user
     *
     * @return void
     */

    public function action_logout()
    {
        \Auth\Auth::logout();

        \Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."login");
    }
	
	/**
     * Get all users
     *
     * @return null
     */

    public function get_all_users()
    {
        $user = \Fuel\Core\DB::select("*")->from("users")->order_by("username");
        $user = $user->as_object()->execute();
		
		foreach($user as $site_user)
		{
			if($site_user->username != 'admin')
			{
				$site_user->delete_button = \AdminHelpers::build_bootstrap_button($this->controller_path, "deleteuser/".$site_user->username, "Delete user");
			}
		}
		
        if(count($user) > 0) {
            return $user;
        }

        return null;
    }

    /**
     * Default authentication functionality
     *
     * @return void
     */

    public function admin_users($status = "")
    {
    	$this->check_access_level_admin();
		
		$messages = array();

        if($status == "successfulcreate")
		{
			$messages[] = "User created successfully";
		}
		else if($status == "nosuccessfulcreate")
		{
			$messages[] = "User could not be created";
		}
		else if($status == "passwordchangesuccess")
		{
			$messages[] = "Password change successful";
		}
		else if($status == "successfuldelete")
		{
			$messages[] = "Delete successful";
		}
		else if($status == "nosuccessfuldelete")
		{
			$messages[] = "No success in delete";
		}
		else if($status == "successfulchange")
		{
			$messages[] = "Change successful";
		}
		else if($status == "nosuccessfulchange")
		{
			$messages[] = "No success in change";
		}
		
		$nav_interface = \Fuel\Core\View::forge("admin/user-tabs",
			$this->build_user_nav_menu_vars(true, false, false, false, false));
		
		$users = $this->get_all_users();

        $this->build_admin_interface(
        	$nav_interface.
            \Fuel\Core\View::forge("admin/partials/users", array("page_title" => "Users",
                "page_title_content" => "Manage the site's Users", "messages" => $messages,
                "users" => $users, "controller_path" => $this->controller_path))
        );
    }
	
	/**
     * Creates a user
     *
     * @param string $status
     * @return void
     */

    public function action_createuser($status = "")
    {
    	$this->check_access_level_admin();
		
        $user_name = \Fuel\Core\Input::post("user_name", null);
        $email_address = \Fuel\Core\Input::post("email_address", null);
        $password = \Fuel\Core\Input::post("password", null);
        $group = \Fuel\Core\Input::post("user_group", null);

        if($user_name != null && $password != null && $email_address != null && $group != null)
        {
            $result = \Auth\Auth::instance()->create_user($user_name, $password, $email_address, $group);

            if($result == false) {
                \Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."user/nosuccessfulcreate");
            }
            else {
                \Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."users/successfulcreate");
            }
        }
    }
	
	/**
     * Deletes a specific user
     *
     * @param string $user_name
     * @return void
     */

    public function action_deleteuser($user_name = "")
    {
    	$this->check_access_level_admin();
		
        if($user_name != "") {
            $result = \Auth\Auth::instance()->delete_user($user_name);

            if($result == false) {
                \Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."user/nosuccessfuldelete");
            }
			else {
				\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."user/successfuldelete");
			}
        }
    }
	
	/**
	 * Update a user's password
	 * 
	 * @param $user_name
	 * @return void
	 */
	
	public function action_updatepassword($user_name = "")
	{
		$this->check_access_level_admin();
		
		if($user_name != "")
		{
			$old_password = \Fuel\Core\Input::post("old_password", false);
			$new_password = \Fuel\Core\Input::post("new_password", false);
			
			if($old_password != false && $new_password != false)
			{
				$result = \Auth\Auth::instance()->change_password($old_password, $new_password, $user_name);
				
				if($result == false)
				{
					\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."users/nosuccessfulchange");
				}
				else 
				{
					\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."users/successfulchange");
				}
			}
		}
	}
	
	/**
	 * Display roles
	 * 
	 * @return void
	 */
	
	public function action_roles($status = "")
	{
		$this->check_access_level_admin();
		
		$messages = array();

        if($status == "successfulcreate")
		{
			$messages[] = "Role created successfully";
		}
		else if($status == "nosuccessfulcreate")
		{
			$messages[] = "Role could not be created";
		}
		else if($status == "successfuldelete")
		{
			$messages[] = "Delete successful";
		}
		else if($status == "nosuccessfuldelete")
		{
			$messages[] = "No success in delete";
		}
		else if($status == "successfulchange")
		{
			$messages[] = "Change successful";
		}
		else if($status == "nosuccessfulchange")
		{
			$messages[] = "No success in change";
		}
		
		$nav_interface = \Fuel\Core\View::forge("admin/user-tabs",
			$this->build_user_nav_menu_vars(false, true, false, false, false));
		
		$roles = \Permissions::get_roles();
		
		foreach($roles as $role)
		{
			$role->delete_button = \AdminHelpers::build_bootstrap_button($this->controller_path,
				"deleterole/".$role->id, "Delete");
		}

        $this->build_admin_interface(
        	$nav_interface.
            \Fuel\Core\View::forge("admin/roles", array("page_title" => "Roles",
                "page_title_content" => "Manage roles", "roles" => $roles,
                "controller_path" => $this->controller_path, "messages" => $messages))
        );
	}
	
	/**
	 * Create a role
	 * 
	 * @return void
	 */
	
	public function action_createrole()
	{
		$this->check_access_level_admin();
		$role_name = \Fuel\Core\Input::post("role_name", null);
		
		if($role_name != null)
			\Permissions::create_role($role_name);
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."roles");
	}
	
	/**
	 * Update a role
	 * 
	 * @param $role_id
	 * @return void
	 */
	
	public function action_updaterole($role_id)
	{
		$this->check_access_level_admin();
		$new_name = \Fuel\Core\Input::post("new_name", null);
		
		if($new_name != null)
			\Permissions::update_role($role_id, $new_name);
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."roles");
	}
	
	/**
	 * Delete a role
	 * 
	 * @param $role_id
	 * @return void
	 */
	
	public function action_deleterole($role_id)
	{
		$this->check_access_level_admin();
		
		$this->build_admin_interface(
            $this->build_confirm_message("Sure you want to delete this role?",
            	\Fuel\Core\Uri::base().$this->controller_path."dodeleterole/?roleid=$role_id")
        );
	}
	
	/**
	 * Confirm delete of role
	 * 
	 * @return void
	 */
	
	public function action_dodeleterole()
	{
		$this->check_access_level_admin();
		$role_id = \Fuel\Core\Input::get("roleid", 0);
		
		// Do delete
		
		if($role_id > 0)
			\Permissions::delete_role($role_id);
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."roles");
	}
	
	/**
	 * Display permissions
	 * 
	 * @return void
	 */
	
	public function action_permissions($status = "")
	{
		$this->check_access_level_admin();
		
		$messages = array();

        if($status == "successfulcreate")
		{
			$messages[] = "Permission created successfully";
		}
		else if($status == "nosuccessfulcreate")
		{
			$messages[] = "Permission could not be created";
		}
		else if($status == "successfuldelete")
		{
			$messages[] = "Delete successful";
		}
		else if($status == "nosuccessfuldelete")
		{
			$messages[] = "No success in delete";
		}
		else if($status == "successfulchange")
		{
			$messages[] = "Change successful";
		}
		else if($status == "nosuccessfulchange")
		{
			$messages[] = "No success in change";
		}
		
		$nav_interface = \Fuel\Core\View::forge("admin/user-tabs",
			$this->build_user_nav_menu_vars(false, false, true, false, false));
		
		$permissions = \Permissions::get_permissions();
		
		foreach($permissions as $permission)
		{
			$permission->delete_button = \AdminHelpers::build_bootstrap_button($this->controller_path,
				"deletepermission/".$permission->id, "Delete");
		}

        $this->build_admin_interface(
        	$nav_interface.
            \Fuel\Core\View::forge("admin/permissions", array("page_title" => "Permissions",
                "page_title_content" => "Manage permissions", "permissions" => $permissions,
                "controller_path" => $this->controller_path, "messages" => $messages))
        );
	}
	
	/**
	 * Create a permission
	 * 
	 * @return void
	 */
	
	public function action_createpermission()
	{
		$this->check_access_level_admin();
		$permission_name = \Fuel\Core\Input::post("permission_name", null);
		
		if($permission_name != null)
			\Permissions::create_permission($permission_name);
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."permissions");
	}
	
	/**
	 * Update a permission
	 * 
	 * @param $permission_id
	 * @return void
	 */
	
	public function action_updatepermission($permission_id)
	{
		$this->check_access_level_admin();
		$new_name = \Fuel\Core\Input::post("new_name", null);
		
		if($new_name != null)
			\Permissions::update_permission($permission_id, $new_name);
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."permissions");
	}
	
	/**
	 * Delete a permission
	 * 
	 * @param $permission_id
	 * @return void
	 */
	
	public function action_deletepermission($permission_id)
	{
		$this->check_access_level_admin();
		
		$this->build_admin_interface(
            $this->build_confirm_message("Sure you want to delete this permission?",
            	\Fuel\Core\Uri::base().$this->controller_path."dodeletepermission/?permissionid=$permission_id")
        );
	}
	
	/**
	 * Confirm delete of role
	 * 
	 * @return void
	 */
	
	public function action_dodeletepermission()
	{
		$this->check_access_level_admin();
		$permission_id = \Fuel\Core\Input::get("permissionid", 0);
		
		// Do delete
		
		if($permission_id > 0)
			\Permissions::delete_permission($permission_id);
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."permissions");
	}
	
	/**
	 * Show user roles
	 * 
	 * @return void
	 */
	
	public function action_userroles($user_id = 0)
	{
		$this->check_access_level_admin();
		
		// Get users
		$users = $this->get_all_users();
		$user = $users[0];
		$roles = \Permissions::get_roles();
		
		if($user_id > 0)
		{
			$user = \Fuel\Core\DB::select("*")->from("users")
				->where("id", "=", $user_id)
				->as_object()->execute();
			
			if(count($user) < 1)
				throw new \Exception_CMS("User ID $user_id does not exist");
				
			$user = $user[0];
		}
		
		$role_ids = array();
		
		// Get roles array
		if(isset($user->id))
		{
			$roles_for_user = \Permissions::get_user_roles($user->id);
			
			foreach($roles_for_user as $role_for_user)
			{
				if($role_for_user->active == 1)
					$role_ids[] = $role_for_user->role_id;
			}
		}
		
		// Tabs
		$nav_interface = \Fuel\Core\View::forge("admin/user-tabs",
			$this->build_user_nav_menu_vars(false, false, false, true, false));
		
		// Sidebar
		$user_roles_sidebar = \Fuel\Core\View::forge("admin/user-roles-sidebar",
			array("users" => $users, "controller_path" => \Fuel\Core\Uri::base().$this->controller_path));
			
		// Content
		$user_roles_content = \Fuel\Core\View::forge("admin/user-roles-editor", 
			array("roles" => $roles, "user" => $user, "controller_path" => $this->controller_path,
				"assign_roles_url" => \Fuel\Core\Uri::base().$this->controller_path."assignroles",
				"role_ids" => $role_ids));
		
		$this->build_admin_interface(
			$nav_interface.
			\Fuel\Core\View::forge("admin/user-roles", array("user_roles_sidebar" => $user_roles_sidebar,
				"user_roles_content" => $user_roles_content))
		);
	}
	
	/**
	 * Show roles and permissions
	 * 
	 * @return void
	 */
	
	public function action_rolespermissions($role_id = 0)
	{
		$this->check_access_level_admin();
		
		// Get roles
		$roles = \Permissions::get_roles();
		$permissions = \Permissions::get_permissions();
		$role = array();
		
		if($role_id > 0)
		{
			$role = \Fuel\Core\DB::select("*")->from(\Permissions::TABLE_ROLES)
				->where("id", "=", $role_id)
				->as_object()->execute();
			
			if(count($role) < 1)
				throw new \Exception_CMS("Role ID $role_id does not exist");
				
			$role = $role[0];
		}
		else
		{
			if(count($roles) > 0)
				$role = $roles[0];
		}
		
		$permission_ids = array();
		
		if(isset($role->id))
		{
			$permissions_for_role = \Permissions::get_roles_permissions($role->id);
			
			foreach($permissions_for_role as $permission_for_role)
			{
				if($permission_for_role->active == 1)	
					$permission_ids[] = $permission_for_role->permission_id;
			}		
		}
		
		// Tabs
		$nav_interface = \Fuel\Core\View::forge("admin/user-tabs",
			$this->build_user_nav_menu_vars(false, false, false, false, true));
		
		// Sidebar
		$roles_permissions_sidebar = \Fuel\Core\View::forge("admin/roles-permissions-sidebar",
			array("roles" => $roles, "controller_path" => \Fuel\Core\Uri::base().$this->controller_path));
			
		// Content
		$roles_permissions_content = \Fuel\Core\View::forge("admin/roles-permissions-editor", 
			array("permissions" => $permissions, "role" => $role, "controller_path" => $this->controller_path,
				"assign_permissions_url" => \Fuel\Core\Uri::base().$this->controller_path."assignpermissions",
				"permission_ids" => $permission_ids));
		
		$this->build_admin_interface(
			$nav_interface.
			\Fuel\Core\View::forge("admin/roles-permissions", array("roles_permissions_sidebar" => $roles_permissions_sidebar,
				"roles_permissions_content" => $roles_permissions_content))
		);
	}

	/**
	 * Assign roles to a user
	 * 
	 * @return void
	 */

	public function action_assignroles()
	{
		$user_id = \Fuel\Core\Input::post("user_id", 0);
		
		if(intval($user_id) > 0)
		{
			$roles = \Fuel\Core\Input::post("chkroles", null);
			$all_roles = \Permissions::get_roles();
			
			if(is_array($roles))
			{	
				\Permissions::assign_user_roles($user_id, $roles);
				
				foreach($all_roles as $role)
				{
					if(!in_array($role->id, $roles))
						\Permissions::deassign_user_role($user_id, $role->id);
				}
			}
			else 
			{
				$roles = array();
				
				foreach($all_roles as $role)
				{
					if(!in_array($role->id, $roles))
						\Permissions::deassign_user_role($user_id, $role->id);
				}
			}
		}
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."userroles/$user_id");
	}
	
	/**
	 * Assign permissions to a role
	 * 
	 * @return void
	 */
	
	public function action_assignpermissions()
	{
		$role_id = \Fuel\Core\Input::post("role_id", 0);
		
		if(intval($role_id) > 0)
		{
			$permissions = \Fuel\Core\Input::post("chkpermissions", null);
			$all_permissions = \Permissions::get_permissions();
			
			if(is_array($permissions))
			{
				\Permissions::assign_role_permissions($role_id, $permissions);
				
				foreach($all_permissions as $permission)
				{
					if(!in_array($permission->id, $permissions))
						\Permissions::deassign_role_permission($role_id, $permission->id);
				}
			}
			else 
			{
				$permissions = array();
			
				foreach($all_permissions as $permission)
				{
					if(!in_array($permission->id, $permissions))
						\Permissions::deassign_role_permission($role_id, $permission->id);
				}
			}
		}
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."rolespermissions/$role_id");
	}
	
	/**
	 * Site settings
	 * 
	 * @param $extension_id
	 * @param $confirm
	 */
	
	public function admin_settings($extension_id = 0, $confirm = 0)
	{
		$this->check_access_level_developer();
		
		$extensions = \Extension::get_installed_extensions(true);
		$settings = \Extension::get_extension_settings($extension_id);
		
		$component_name = "Sitewide settings";
		$component_slug = null;
		
		if($extension_id > 0)
		{
			$extension_meta = \Extension::get_installed_extension_meta_data($extension_id);
			
			if(count($extension_meta) < 1)
				throw new \Exception_Extension("Extension does not exist");
			
			$component_name = $extension_meta[0]->extension_name;
			$component_slug = $extension_meta[0]->extension_slug;
		}
		
		$settings_sidebar = \Fuel\Core\View::forge("admin/settings-sidebar", 
			array("extensions" => $extensions, "controller_path" => \Fuel\Core\Uri::base().$this->controller_path));
		$settings_content = \Fuel\Core\View::forge("admin/settings-settings-panel", 
			array("settings" => $settings, "component_name" => $component_name, "component_slug" => $component_slug,
				"confirm" => intval($confirm), "controller_path" => $this->controller_path, "extension_id" => $extension_id));
		
		$this->build_admin_interface(
			\Fuel\Core\View::forge("admin/settings", array("settings_sidebar" => $settings_sidebar,
				"settings_editor" => $settings_content))
		);
	}

	/**
	 * Saves settings for an extension
	 *
	 * @param $extension_od
	 */

	public function action_savesettings($extension_id)
	{
		$this->check_access_level_developer();
		
		$extension_settings = \Extension::get_extension_settings($extension_id);
		
		foreach($extension_settings as $extension_setting)
		{
			$setting_id = $extension_setting->id;
			$setting_new_value = \Fuel\Core\Input::post("setting_".$setting_id, false);
			
			if($setting_new_value != false)
			{
				$result = \Extension::save_extension_setting($extension_id, $setting_id, $setting_new_value);
			}
		}
		
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."settings/$extension_id/1");
	}
	
	/**
	 * Handles the navigation
	 * 
	 * @param $confirm
	 */
	
	public function admin_navigation($confirm)
	{
		if($this->msh_site)
		{
			throw new HttpNotFoundException();
		}
		
		$this->check_access_level_developer();
		
		$page_module_enabled = $this->is_extension_installed('page');
		$extension_items = \Extension::get_installed_extensions();
		$page_items = array();
		$navigation_items = \Navigation::get_all_navigation_items($this->get_shortened_urls());
		
		if($page_module_enabled)
		{
			$page_items = \page\Page::get_pages();
		}
		
		$this->build_admin_interface(
			\Fuel\Core\View::forge("admin/navigation", 
				array("page_items" => $page_items,
					"extension_items" => $extension_items,
					"controller_path" => $this->controller_path,
					"navigation_items" => $navigation_items,
					"confirm" => $confirm)
			)
		);
	}
	
	/**
	 * Add an item to the navigation
	 */
	
	public function action_addnavigationafter()
	{
		$this->check_access_level_developer();
		
		$text = \Fuel\Core\Input::post('text', false);
		$type = \Fuel\Core\Input::post('type', false);
		$page_object_id = \Fuel\Core\Input::post('page_object_id', false);
		$extension_object_id = \Fuel\Core\Input::post('extension_object_id', false);
		$after_navigation_id = \Fuel\Core\Input::post('after_navigation_id', null);
		
		$object_id = $page_object_id;
		
		if($type == \Navigation::NAV_MODULE)
			$object_id = $extension_object_id;
		
		if(($text != false) && ($type != false) && ($page_object_id != false) && ($extension_object_id != false)
			&& ($after_navigation_id != null))
		{
			\Navigation::save_navigation($text, $type, $object_id, $after_navigation_id);
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation/1");
		}
		else 
		{
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation");
		}
	}
	
	/**
	 * Add an item to the navigation
	 */
	
	public function action_addnavigationbefore()
	{
		$this->check_access_level_developer();
		
		$text = \Fuel\Core\Input::post('text', false);
		$type = \Fuel\Core\Input::post('type', false);
		$page_object_id = \Fuel\Core\Input::post('page_object_id', false);
		$extension_object_id = \Fuel\Core\Input::post('extension_object_id', false);
		$before_navigation_id = \Fuel\Core\Input::post('before_navigation_id', null);
		
		$object_id = $page_object_id;
		
		if($type == \Navigation::NAV_MODULE)
			$object_id = $extension_object_id;
		
		if(($text != false) && ($type != false) && ($page_object_id != false) && ($extension_object_id != false)
			&& ($before_navigation_id != null))
		{
			\Navigation::save_navigation($text, $type, $object_id, 0, $before_navigation_id);
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation/1");
		}
		else 
		{
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation");
		}
	}
	
	/**
	 * Delete a navigation item
	 * 
	 * @param $navigation_id
	 */
	
	public function action_deletenavigation($navigation_id)
	{
		$this->check_access_level_developer();
		
		\Navigation::remove_navigation($navigation_id);
		\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation/2");
	}
	
	/**
	 * Update a navigation item
	 * 
	 * @param $navigation_id
	 */
	
	public function action_updatenavigation($navigation_id)
	{
		$this->check_access_level_developer();
		
		$text = \Fuel\Core\Input::post('text', false);
		$type = \Fuel\Core\Input::post('type', false);
		$page_object_id = \Fuel\Core\Input::post('page_object_id', false);
		$extension_object_id = \Fuel\Core\Input::post('extension_object_id', false);
		
		$object_id = $page_object_id;
		
		if($type == \Navigation::NAV_MODULE)
			$object_id = $extension_object_id;
		
		if(($text != false) && ($type != false) && ($page_object_id != false) && ($extension_object_id != false))
		{
			\Navigation::update_navigation($text, $type, $object_id, $navigation_id);
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation/1");
		}
		else 
		{
			\Fuel\Core\Response::redirect(\Fuel\Core\Uri::base().$this->controller_path."navigation");
		}
	}
}
