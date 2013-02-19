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

    protected $is_abstract_controller = true;

    protected $controller_path = "admin/";
    protected $action_path = null;
    protected $default_admin_extension_path = "admin/";
    protected $logout_path = "cms/cms/logout";

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
            
            $this->logged_in = true;

            return true;
        }
        else
        {
            $this->template->logged_in = false;
            Response::redirect(Uri::base().$this->controller_path."login");

            $this->logged_in = false;

            return false;
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
            self::MENU_THEMES => $this->build_menu_item("Themes", $this->default_admin_extension_path.self::MENU_THEMES),
            self::MENU_EXTENSIONS => $this->build_menu_item("Extensions", $this->default_admin_extension_path.self::MENU_EXTENSIONS),
         
      );

         if($this->admin_settings_function)
            $links[self::MENU_SETTINGS] = $this->build_menu_item("Settings", $this->default_admin_extension_path.self::MENU_SETTINGS);

        if($this->admin_navigation_function)
            $links[self::MENU_NAVIGATION] = $this->build_menu_item("Navigation", $this->default_admin_extension_path.self::MENU_NAVIGATION);

         if($this->admin_users_function){
            $links[self::MENU_USERS] = $this->build_menu_item("Users", $this->default_admin_extension_path.self::MENU_USERS);
            if(Auth::check()){
            $links[self::MENU_LOGOUT] = $this->build_menu_item("Logout", $this->logout_path);
            }
            
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
        $objects_table = ExtensionSetup::EXTENSION_OBJECTS_TABLE;
        $objects_meta_table = ExtensionSetup::EXTENSION_OBJECTS_META_TABLE;

        $object = DB::select("object_id")->from($objects_table)
                ->where("object_slug", "=", $object_name)->as_object()->execute();

        if(count($object) < 1)
            return null;

        $object_id = $object[0]->object_id;

        $object_meta_data = DB::select("*")->from($objects_meta_table)
                ->where("object_id", "=", $object_id)->as_object()->execute();

        return $object_meta_data;
    }

    /**
     * Feeds items to a tabular layout template
     *
     * @param $page_title
     * @param $page_content
     * @param $table_name
     * @param $id_field
     * @param $description_field
     * @param string $return_path
     * @param bool $paged
     * @param int $page_number
     * @param int $page_size
     * @param null $action_name
     * @param null $order_by
     * @param string $direction
     * @return view content
     */
    protected function build_admin_ui_tabular_list($page_title, $page_content, $table_name, $id_field, $description_field,
        $return_path = "", $paged = false, $page_number = 1, $page_size = 20, $action_name = null, $order_by = null, $direction = 'asc')
    {
        // Tabular layout data here

        $records = DB::select(array($id_field, 'id_field'), array($description_field, 'description_field'))
            ->from($table_name);

        if($order_by != null)
            $records->order_by($order_by, $direction);

        $pagination_records = CMSUtil::create_pagination_records($table_name, $records, $paged, $page_number,
            $this->controller_path, $action_name, $page_size);

        $record_array = $records->as_object()->execute();

        // Embed button info

        foreach($record_array as $key => $record_array_item)
        {
            $btn_edit = (trim($return_path) == "") ?
                    $this->build_bootstrap_button("edit/$table_name/".$record_array_item->id_field, "Edit") :
                    $this->build_bootstrap_button("edit/$table_name/".$record_array_item->id_field."/$return_path", "Edit");
            $btn_delete = $this->build_bootstrap_button("delete/$table_name/".$record_array_item->id_field, "Delete");

            $record_array[$key]->buttons = array($btn_edit, $btn_delete);
        }

        $bottom_buttons = array(
            "btn_add" => (trim($return_path) == "") ?
                    $this->build_bootstrap_button("edit/$table_name/0", "Add new") :
                    $this->build_bootstrap_button("edit/$table_name/0/$return_path", "Add new")
        );

        $view = View::forge("admin/partials/table-view", array("table_rows" => $record_array,
             "page_title" => $page_title, "page_title_content" => $page_content,
             "bottom_buttons" => $bottom_buttons, "pagination_records" => $pagination_records,
             "return_path" => $return_path));

        return $view;
    }

    /**
     * Feeds items to a thumbnail view (must be images)
     *
     * @param $page_title
     * @param $page_content
     * @param $table_name
     * @param $id_field
     * @param $thumbnail_field
     * @param $description_field
     * @param string $return_path
     * @param bool $paged
     * @param int $page_number
     * @param int $page_size
     * @param null $action_name
     * @param null $order_by
     * @param string $direction
     * @return view content
     */

    protected function build_admin_ui_thumbnail_list($page_title, $page_content, $table_name, $id_field, $thumbnail_field, $description_field,
        $return_path = "", $paged = false, $page_number = 1, $page_size = 20, $action_name = null, $order_by = null, $direction = 'asc')
    {
        // Tabular layout data here

        $records = DB::select(array($id_field, 'id_field'), array($thumbnail_field, 'thumbnail_field'),
            array($description_field, 'description_field'))->from($table_name);

        if($order_by != null)
            $records->order_by($order_by, $direction);

        $pagination_records = CMSUtil::create_pagination_records($table_name, $records, $paged, $page_number,
            $this->controller_path, $action_name, $page_size);

        $record_array = $records->as_object()->execute();

        // Embed button info

        foreach($record_array as $key => $record_array_item)
        {
            $btn_edit = (trim($return_path) == "") ?
                    $this->build_bootstrap_button("edit/$table_name/".$record_array_item->id_field, "Edit") :
                    $this->build_bootstrap_button("edit/$table_name/".$record_array_item->id_field."/$return_path", "Edit");
            $btn_delete = $this->build_bootstrap_button("delete/$table_name/".$record_array_item->id_field, "Delete");

            $record_array[$key]->buttons = array($btn_edit, $btn_delete);
        }

        $bottom_buttons = array(
            "btn_add" => (trim($return_path) == "") ?
                    $this->build_bootstrap_button("edit/$table_name/0", "Add new") :
                    $this->build_bootstrap_button("edit/$table_name/0/$return_path", "Add new")
        );

        $media_path = Uri::base().basename(UPLOADPATH)."/media/";
        
        $view = View::forge("admin/partials/thumbnail-view", array("table_rows" => $record_array,
             "page_title" => $page_title, "page_title_content" => $page_content,
             "bottom_buttons" => $bottom_buttons, "pagination_records" => $pagination_records,
             "return_path" => $return_path, "media_path" => $media_path));

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

        $object_meta_data = $this->get_object_meta_data($table_slug);
        $record = array();

        if($record_id > 0)
            $record = DB::select("*")->from($table_slug)->where("id", "=", $record_id)->as_object()->execute();

        if(count($record) > 0)
            $record = $record[0];
        else
            $record = null;

        $view = View::forge("admin/partials/record-view", array("page_rows" => $object_meta_data,
                    "record_id" => $record_id, "page_title" => $page_title, "page_title_content" => $page_content,
                    "form_action" => Uri::base().$this->controller_path.$form_action, "object" => $table_slug,
                    "record" => $record, "return_path" => $return_path));

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
        $my_button = new stdClass();

        $my_button->button_link = Uri::base().$this->controller_path.$url;
        $my_button->button_text = $text;

        return $my_button;
    }

    /**
     * Override this for special fields
     *
     * @param $field_name
     * @param $value_sets
     * @return null
     */

    protected function special_field_operation($field_name, $value_sets)
    {
        // To be overriden
        return null;
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

    /**
     * Default login action
     *
     * @return void
     */

    public function action_login()
    {
        $user_name = Input::post("username", null);
        $password = Input::post("password", null);
       
        if($user_name != null && $password != null)
        {   
            $auth = Auth::instance();
            if($auth->login($user_name, $password))
                Response::redirect(Uri::base().$this->controller_path."index");
        }

        $this->build_admin_interface(
            View::forge("admin/partials/login", array('form_action' => Uri::base().$this->controller_path))
        );
    }

    /**
     * Logs out the current user
     *
     * @return void
     */

    public function action_logout()
    {
        Auth::logout();

        Response::redirect(Uri::base().$this->controller_path."login");
    }
    
    /**
     * Creates a user
     *
     * @param string $status
     * @return void
     */

    public function action_createuser($status = "")
    {
        $user_name = Input::post("username", null);
        $email_address = Input::post("password", null);
        $password = Input::post("password", null);
        $group = Input::post("group", null);

        if($user_name != null && $password != null && $email_address != null && $group != null)
        {
            $result = Auth::instance()->create_user($user_name, $password, $email_address, $group);

            if($result == false) {
                Response::redirect(Uri::base().$this->controller_path."createuser/nosuccess");
            }
            else {
                Response::redirect(Uri::base().$this->controller_path."users/successfulcreate");
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
        if($user_name != "") {
            $result = Auth::instance()->delete_user($user_name);

            if($result == false) {
                // What happens when we do not delete?
            }
        }
    }

    // Default actions

    /**
     * Admin UI dashboard
     *
     * @return void
     */

    public function action_index()
    {
        // TODO: Dashboard
        
        $this->build_admin_interface(
            View::forge("admin/partials/default-dashboard")
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
        $record_id = Input::post("record_id");
        $object = Input::post("object");
        $save_value = Input::post(AdminHelpers::save_button_value(), false);
        $save_and_exit_value = Input::post(Utility::slugify(AdminHelpers::save_and_exit_button_value()), false);

        $url_from = $this->get_referring_url();

        $object_meta_data = $this->get_object_meta_data($object);
        $update_object = null;

        if($object_meta_data == null)
            throw new Exception_CMS("The object $object does not exist in the database");

        if($record_id > 0) {
            $update_object = DB::update($object)->where("id", "=", $record_id);
        }
        else {
            $update_object = DB::insert($object);
        }

        $database_array = array();

        // Process each field

        foreach($object_meta_data as $object_meta_data_item)
        {
            $value_to_set = "";
            $skip_control = false;
            
            switch($object_meta_data_item->{Extension_ObjectMeta::SEGMENT_OBJECT_META_CONTROL}) {
                case DBFieldMeta::CONTROL_SIMPLE_TEXT:
                    $value_to_set = trim(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_NUMERIC:
                    $value_to_set = floatval(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_MULTI_TEXT:
                    $value_to_set = trim(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_RICH_EDIT:
                    $value_to_set = trim(Input::post($object_meta_data_item->object_meta_slug));
                    break;
                case DBFieldMeta::CONTROL_LIST:
                    $value_to_set = Input::post($object_meta_data_item->object_meta_slug);
                    break;
                case DBFieldMeta::CONTROL_TABULAR_LIST:
                    $value_to_set = Input::post($object_meta_data_item->object_meta_slug);
                    break;
                case DBFieldMeta::CONTROL_CHECKBOX:
                    $value_to_set = isset($_POST[$object_meta_data_item->object_meta_slug]) ? 1 : 0;
                    break;
                case DBFieldMeta::CONTROL_HIDDEN:
                    $value_to_set = $this->special_field_operation($object_meta_data_item->object_meta_slug, Input::post());
                        
                    if($value_to_set == false) {
                        $skip_control = true;
                    }
                    break;
                case DBFieldMeta::CONTROL_FILE:

                    $upload_error = $_FILES[$object_meta_data_item->object_meta_slug]["error"];

                    $value_to_set = ($upload_error == UPLOAD_ERR_OK) ?
                            $_FILES[$object_meta_data_item->object_meta_slug]["name"] : "";

                    if($upload_error != UPLOAD_ERR_OK)
                    {
                        $skip_control = true;
                    }

                    break;
            }

            if(!$skip_control)
                $database_array[$object_meta_data_item->object_meta_slug] = $value_to_set;
        }

        // Save records and get record ids

        $insert_id = 0;
        $rows_affected = 0;

        if(count($database_array) > 0)
        {
            if($record_id > 0)
            {
                $rows_affected = $update_object->set($database_array)->execute();
                $insert_id = $record_id;
            }
            else {
                list($insert_id, $rows_affected) = $update_object->set($database_array)->execute();
            }

            // Process and upload files afresh with their new destination names

            $media_destination_path = $media_upload_path == null ?
                    UPLOADPATH : UPLOADPATH.rtrim($media_upload_path, "/")."/";

            foreach($object_meta_data as $object_meta_data_item)
            {
                if($object_meta_data_item->{Extension_ObjectMeta::SEGMENT_OBJECT_META_CONTROL} == DBFieldMeta::CONTROL_FILE)
                {
                    if($_FILES[$object_meta_data_item->object_meta_slug]["error"] == UPLOAD_ERR_OK)
                    {
                        $uploaded_file = $_FILES[$object_meta_data_item->object_meta_slug]["tmp_name"];
                        $file_name = $_FILES[$object_meta_data_item->object_meta_slug]["name"];
                        move_uploaded_file($uploaded_file, $media_destination_path.$insert_id."_".$file_name);
                    }
                }
            }
        }

        // Finalize

        $strlen_url_from = strlen($url_from);

        if($save_value != false || $save_and_exit_value != false) {
            if($save_and_exit_value) {
                $redirect_url = Uri::base().$this->controller_path;

                if(trim(Input::post("return_path")) != "")
                {
                    if(trim(Input::post("return_path")) != "null") {
                        $redirect_url = $redirect_url."/".ltrim(Input::post("return_path"), "/");
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
     * Default delete interface
     *
     * @param $table
     * @param $record_id
     * @param int $confirm
     * @return void
     */

    public function action_delete($table, $record_id, $confirm = 0)
    {
        // Default delete interface

        $url_from = $this->get_referring_url();

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

                if($theme_active == 1) {
                    $theme_data["buttons"] = array($uninstall_button);
                }
                else {
                    $theme_data["buttons"] = array($activate_button, $uninstall_button);
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

    public function action_uninstalltheme($theme_id)
    {
        CMSTheme::uninstall_theme($theme_id);

        Response::redirect(Uri::base().$this->controller_path."themes");
    }

    /**
     * Activates a specific theme
     *
     * @param $theme_id
     * @return void
     */

    public function action_activatetheme($theme_id)
    {
        CMSTheme::set_default_theme($theme_id);

        Response::redirect(Uri::base().$this->controller_path."themes");
    }

    /**
     * The navigation management module
     *
     * @return void
     */

    public function action_navigation()
    {
        if($this->admin_navigation_function) {
            $this->admin_navigation();
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

    public function action_uninstallextension($extension_id)
    {
        Extension::uninstall_extension($extension_id);

        Response::redirect(Uri::base().$this->controller_path."extensions");
    }

    /**
     * Activates a specific extension
     *
     * @param $extension_id
     * @return void
     */

    public function action_activateextension($extension_id)
    {
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
        Extension::deactivate_extension($extension_id);

        Response::redirect(Uri::base().$this->controller_path."extensions");
    }

    /**
     * Settings management module
     *
     * @return void
     */

    public function action_settings()
    {
        if($this->admin_settings_function) {
            $this->admin_settings();
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

    public function action_users()
    {
        if($this->admin_users_function) {
            $this->admin_users();
        }
        else {
            throw new HttpNotFoundException();
        }
    }
}
