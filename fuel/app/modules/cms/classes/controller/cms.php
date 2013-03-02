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

    public function before()
    {
        parent::before();
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
		
		$users = $this->get_all_users();

        $this->build_admin_interface(
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
				$result = \Auth\Auth::change_password($old_password, $new_password, $user_name);
				
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
	 * Site settings
	 * 
	 * @param $extension_id
	 * @param $confirm
	 */
	
	public function admin_settings($extension_id = 0, $confirm = 0)
	{
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
}
