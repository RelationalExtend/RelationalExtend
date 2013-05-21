<?php
/**
 * Manage and execute extensions
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class Extension {
    const EXTENSION_FILE = "Extension.php";
    const PLUGIN_FILE = "Plugin.php";
    const SETUP_FILE = "Setup.php";
    const INFO_FILE = "Info.php";

    const EXTENSION_PREFIX = "Extension";
    const SETUP_PREFIX = "Setup";
    const INFO_PREFIX = "Info";
    const PLUGIN_PREFIX = "Plugin";
    
    const INFO_FUNCTION = "info";
    const INSTALL_FUNCTION = "install_extension";
    const UNINSTALL_FUNCTION = "uninstall_extension";
    const EXECUTABLE_FUNCTION = "execute_extension";

    const EXTENSIONS_TABLE = "extensions";

    const CLASSES_FOLDER = "classes/";

    public function __construct()
    {
        // Object constructor
    }

    /**
     * Verifies the validity of the extension
     * 
     * @param $extension_path
     * @return bool
     */

    private static function is_valid_extension($extension_path)
    {
        $base_folder_name = basename($extension_path);
        $path_to_class_files = $extension_path.self::CLASSES_FOLDER.$base_folder_name.DIRECTORY_SEPARATOR;

        $extension_file = $path_to_class_files.self::EXTENSION_FILE;
        $plugin_file = $path_to_class_files.self::PLUGIN_FILE;
        $setup_file = $path_to_class_files.self::SETUP_FILE;
        $info_file = $path_to_class_files.self::INFO_FILE;

        if(file_exists($extension_file) && file_exists($plugin_file) &&
            file_exists($setup_file) && file_exists($info_file)) {
            // Valid extension
            return true;
        }

        return false;
    }

    /**
     * Gets all the core extensions
     *
     * @return array
     */

    public static function get_core_extensions()
    {
        // All core extensions

        $folders = array();
        $extensions = array();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(EXTPATH),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($iterator as $file) {
            if($file->isDir()) {
                $folders[] = $file.DIRECTORY_SEPARATOR;
            }
        }

        foreach($folders as $folder) {
            if(self::is_valid_extension($folder)) {
                $extensions[] = $folder;
            }
        }

        return $extensions;
    }

    /**
     * Get meta data for core extensions
     *
     * @param array $extensions
     * @return array
     */

    public static function get_core_extension_meta_data($extensions = array())
    {
        // Get meta data for each extension
        $extension_meta_data = array();

        if(!is_array($extensions))
            throw new Exception_Extension("Extensions supplied are not in an array");
        
        foreach($extensions as $extension) {
            if(self::is_valid_extension($extension)) {
                $extension_info_class = "\\".basename($extension)."\\".ucfirst(basename($extension))."_".self::INFO_PREFIX;
                $extension_info_object = new $extension_info_class();

                if(method_exists($extension_info_object, self::INFO_FUNCTION)) {
                    $meta_data = $extension_info_object->{self::INFO_FUNCTION}();
                    $directory_name = array("extension_folder" => basename($extension));

                    $extension_meta_data[basename($extension)] = array_merge($meta_data, $directory_name);
                }
            }
        }

        return $extension_meta_data;
    }

    /**
     * Gets installed extension's meta data using the extension_id
     *
     * @static
     * @param $extension_id
     * @return extension_record
     */

    public static function get_installed_extension_meta_data($extension_id)
    {
        $extension_record = DB::select("*")->from(self::EXTENSIONS_TABLE)
            ->where("extension_id", "=", $extension_id)->as_object()->execute();

        return $extension_record;
    }

    /**
     * Gets installed extension's meta data using the extension_slug
     *
     * @static
     * @param $extension_slug
     * @return extension_record
     */

    public static function get_installed_extension_meta_data_by_slug($extension_slug)
    {
        $extension_record = DB::select("*")->from(self::EXTENSIONS_TABLE)
            ->where("extension_slug", "=", $extension_slug)->as_object()->execute();

        return $extension_record;
    }

    /**
     * Install an extension
     *
     * @param $extension_folder
     * @return array
     */

    public static function install_extension($extension_folder)
    {
        // Install an extension

        $setup_file = $extension_folder.self::CLASSES_FOLDER.basename($extension_folder).DIRECTORY_SEPARATOR.self::SETUP_FILE;
        $base_folder = basename($extension_folder);

        if(file_exists($extension_folder)) {
            if(file_exists($setup_file)) {
                // Can be executed
                $extension_class = "\\".$base_folder."\\".ucfirst(basename($extension_folder))."_".self::SETUP_PREFIX;
                $extension_object = new $extension_class();

                if(method_exists($extension_object, self::INSTALL_FUNCTION)) {
                    $return_data = $extension_object->{self::INSTALL_FUNCTION}();
                    
                    return $return_data;
                }

                throw new Exception_Extension("The extension's ".self::INSTALL_FUNCTION." method was not found");
            }

            // Cannot be executed

            throw new Exception_Extension("Extension file ".$setup_file." not found");
        }

        throw new Exception_Extension("The extension folder ".$extension_folder. " does not exist");
    }

    /**
     * Uninstall an extension
     *
     * @param $extension_id
     * @return bool
     */

    public static function uninstall_extension($extension_id)
    {
        // Uninstall the extension as well as settings
        $extension_meta_data = self::get_installed_extension_meta_data($extension_id);
        $extension_folder = $extension_meta_data[0]->extension_folder;
        $path_extension_folder = EXTPATH.$extension_folder.DIRECTORY_SEPARATOR;
        $base_folder = basename($path_extension_folder);

        $setup_file = EXTPATH.$extension_folder.DIRECTORY_SEPARATOR.self::CLASSES_FOLDER.basename($extension_folder).DIRECTORY_SEPARATOR.self::SETUP_FILE;

        if(file_exists($path_extension_folder)) {
            if(file_exists($setup_file)) {
                $extension_class = "\\".$base_folder."\\".ucfirst(basename($extension_folder))."_".self::SETUP_PREFIX;
                $extension_object = new $extension_class();

                if(method_exists($extension_object, self::UNINSTALL_FUNCTION)) {
                    $return_data = $extension_object->{self::UNINSTALL_FUNCTION}($extension_id);

                    return $return_data;
                }

                throw new Exception_Extension("The extension's ".self::UNINSTALL_FUNCTION." method was not found");
            }

            // Cannot be executed

            throw new Exception_Extension("Extension file ".$setup_file." not found");
        }

        throw new Exception_Extension("The extension folder ".$extension_folder. " does not exist");
    }

    /**
     * List all installed axtensions
     *
     * @param bool $only_active
     * @return array
     */

    public static function get_installed_extensions($only_active = false)
    {
        // Get installed extension

        $installed_extensions = DB::select("*")->from(self::EXTENSIONS_TABLE);

        if($only_active) {
            $installed_extensions->where("extension_active", "=", 1);
        }

        return $installed_extensions->as_object()->execute();
    }

    /**
     * Activates an extension
     *
     * @static
     * @param $extension_id
     * @return update_status
     */

    public static function activate_extension($extension_id)
    {
        return DB::update(self::EXTENSIONS_TABLE)->set(array("extension_active" => 1))
                ->where("extension_id", "=", $extension_id)->execute();
    }

    /**
     * Deactivates an extension
     *
     * @static
     * @param $extension_id
     * @return update_status
     */

    public static function deactivate_extension($extension_id)
    {
        return DB::update(self::EXTENSIONS_TABLE)->set(array("extension_active" => 0))
                ->where("extension_id", "=", $extension_id)->execute();
    }

    /**
     * Execute an extension
     * 
     * @param $extension_meta_data
     * @param array $parameters
     * @return array
     */

    private static function execute_extension($extension_meta_data, $parameters = array())
    {
        // Executes the extension
        $extension_folder = EXTPATH.$extension_meta_data->extension_folder.DIRECTORY_SEPARATOR;
        $classes_folder = $extension_folder.self::CLASSES_FOLDER.$extension_meta_data->extension_folder.DIRECTORY_SEPARATOR;

        if(file_exists($classes_folder.self::EXTENSION_FILE)) {
            // Can be executed
            $extension_class = "\\".$extension_meta_data->extension_folder."\\".ucfirst($extension_meta_data->extension_folder)."_".self::EXTENSION_PREFIX;
            $extension_object = new $extension_class($extension_meta_data);

            if(method_exists($extension_object, self::EXECUTABLE_FUNCTION)) {
                $return_data = $extension_object->{self::EXECUTABLE_FUNCTION}($parameters);

                return $return_data;
            }

            throw new Exception_Extension("The extension's ".self::EXECUTABLE_FUNCTION." method was not found");
        }

        // Cannot be executed

        throw new Exception_Extension("Extension file ".$classes_folder.self::EXTENSION_FILE." not found");
    }

    /**
     * Execute an extension's plugin
     * 
     * @param $extension_meta_data
     * @param $plugin_function
     * @param array $parameters
     * @return array
     */

    public static function execute_plugin($extension_meta_data, $plugin_function, $parameters = array())
    {
        $extension_folder = EXTPATH.$extension_meta_data->extension_folder.DIRECTORY_SEPARATOR;
        $plugin_file = $extension_folder.self::CLASSES_FOLDER.basename($extension_folder).DIRECTORY_SEPARATOR.self::PLUGIN_FILE;

        if(file_exists($plugin_file)) {

            // Can be executed

            $extension_class = "\\".basename($extension_folder)."\\".ucfirst($extension_meta_data->extension_folder)."_".self::PLUGIN_PREFIX;
            $extension_object = new $extension_class($extension_meta_data);

            if(method_exists($extension_object, $plugin_function)) {

                if(!isset($parameters["attributes"]) || !isset($parameters["content"])) {
                    throw new Exception_Extension("Plugin's attributes and content values not supplied");
                }

                $attributes = $parameters["attributes"];
                $content = $parameters["content"];
                $return_data = $extension_object->{$plugin_function}($attributes, $content);

                return $return_data;
            }

            throw new Exception_Extension("The extension plugin's ".$plugin_function." method was not found at ".
                                          $extension_folder.self::PLUGIN_FILE);
        }

        // Cannot be executed

        throw new Exception_Extension("Plugin file ".$plugin_file." not found");
    }

    /**
     * Execute specific extensions
     *
     * @param array $parameters
     * @param string $theme_slug
     * @return array
     */

    public static function execute_extensions($parameters = array(), $theme_slug = "default")
    {
        // Executes all active extensions
        
        $theme = new CMSTheme();

        // Get installed and active extensions

        $extensions = self::get_installed_extensions();
        $extension_data = array();

        // Execute each extension and get the data from each

        foreach($extensions as $extension)
        {
            $return_data = self::execute_extension($extension, $parameters);

            if(!is_array($return_data)) {
                throw new Exception_Extension("The extension ".$extension->extension_slug." does not return an array upon execution");
            }

            $extension_data[$extension->extension_slug] = $return_data;
        }

        $processed_extension_data = array();

        foreach($extension_data as $extension_key => $data_item) {
            foreach($data_item as $data_item_key => $value) {
                $processed_extension_data[$extension_key."_".$data_item_key] = $value;
            }
        }

        // Execute the theme

        $theme_html = $theme->execute_theme($theme_slug, $processed_extension_data);

        return $theme_html;
    }

	/**
	 * Gets settings for the site or specific extension
	 * 
	 * @param $extension_id
	 * @return settings
	 */

	public static function get_extension_settings($extension_id = 0, $setting_slug = "")
	{
		$extension_settings = null;
		
		if($extension_id > 0)
		{
			$extension_settings = DB::select(
				array('extension_setting_id', 'id'),
				array('extension_setting_name', 'name'),
				array('extension_setting_slug', 'slug'),
				array('extension_setting_value', 'value'))
			->from(ExtensionSetup::EXTENSION_SETTINGS_TABLE)
			->where('extension_setting_extension_id', "=", $extension_id);
			
			if($setting_slug != "")
				$extension_settings = $extension_settings->where("extension_setting_slug", "=", $setting_slug);
		}
		else 
		{
			$extension_settings = DB::select(
				array('setting_id', 'id'),
				array('setting_name', 'name'),
				array('setting_slug', 'slug'),
				array('setting_value', 'value'))
			->from(CMSInit::TABLE_SETTINGS);
			
			if($setting_slug != "")
				$extension_settings = $extension_settings->where("setting_slug", "=", $setting_slug);
		}
		
		
		$extension_settings = $extension_settings->as_object()->execute();
		
		return $extension_settings;
	}
	
	/**
	 * Saves a specific setting value
	 * 
	 * @param $extension_id
	 * @param $setting_id
	 * @param $value
	 * @return result
	 */
	
	public static function save_extension_setting($extension_id, $setting_id, $value, $setting_slug = "")
	{
		$field_slug = "";
		$field_value = "";
		$table_name = "";
		$setting_field = "";
			
		if($setting_id == 0 && $setting_slug != "")
		{
			$setting_field = $setting_slug;
			
			if($extension_id > 0)
			{
				$field_slug = "extension_setting_slug";
				$field_value = "extension_setting_value";
				$table_name = ExtensionSetup::EXTENSION_SETTINGS_TABLE;
			}
			else 
			{
				$field_slug = "setting_slug";
				$field_value = "setting_value";
				$table_name = CMSInit::TABLE_SETTINGS;
			}
		}
		else 
		{
			$setting_field = $setting_id;
			
			if($extension_id > 0)
			{
				$field_slug = "extension_setting_id";
				$field_value = "extension_setting_value";
				$table_name = ExtensionSetup::EXTENSION_SETTINGS_TABLE;
			}
			else {
				$field_slug = "setting_id";
				$field_value = "setting_value";
				$table_name = CMSInit::TABLE_SETTINGS;
			}
		}
		
		return $result = DB::update($table_name)->set(array($field_value => $value))
				->where($field_slug, "=", $setting_field)
				->execute();
	}
}
