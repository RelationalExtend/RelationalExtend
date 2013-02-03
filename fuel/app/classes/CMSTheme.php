<?php
/**
 * Manage site-wide themes
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class CMSTheme {

    // Constants

    const INFO_FILE = "Info.php";
    const INFO_PREFIX = "Info";
    const INFO_FUNCTION = "info";

    const TABLE_THEMES = "themes";
    const TABLE_THEME_LAYOUTS = "theme_layouts";
    const TABLE_THEME_PARTIALS = "theme_partials";
    const TABLE_JAVASCRIPT = "theme_javascript";
    const TABLE_STYLES = "theme_styles";

    const CLASSES_FOLDER = "classes/";
    const LAYOUTS_FOLDER = "layouts/";
    const PARTIALS_FOLDER = "partials/";
    const STYLES_FOLDER = "css/";
    const JAVASCRIPT_FOLDER = "js/";
    const IMAGES_FOLDER = "images/";

    const THEME_PREFIX = "theme";
    const LAYOUT_PREFIX = "layouts";
    const PARTIAL_PREFIX = "partials";
    const JS_PREFIX = "js";
    const CSS_PREFIX = "css";

    // Static variables

    public static $theme_data;

    public function __construct()
    {
        // Object constructor
    }

    /**
     * Checks the validity of a theme
     *
     * @static
     * @param $theme_path
     * @return bool
     */

    private static function is_valid_theme($theme_path)
    {
        $base_folder_name = basename($theme_path);
        $path_to_class_files = $theme_path.self::CLASSES_FOLDER.$base_folder_name.DIRECTORY_SEPARATOR;

        $info_file = $path_to_class_files.self::INFO_FILE;
        $layouts_dir = $theme_path.self::LAYOUTS_FOLDER;
        $partials_dir = $theme_path.self::PARTIALS_FOLDER;
        $javascript_dir = $theme_path.self::JAVASCRIPT_FOLDER;
        $styles_dir = $theme_path.self::STYLES_FOLDER;
        $images_dir = $theme_path.self::IMAGES_FOLDER;

        if(file_exists($info_file) && is_dir($layouts_dir) && is_dir($partials_dir)
            && is_dir($javascript_dir) && is_dir($styles_dir) && is_dir($images_dir)) {
            // Valid theme
            return true;
        }

        return false;
    }

    /**
     * Loads the default system theme
     *
     * @param $layout_slug
     * @return theme_layout
     */

    private static function load_default_theme_layout($layout_slug)
    {
        // Load the default theme
        $default_theme = DB::select("theme_id")->from(self::TABLE_THEMES)
                ->where("theme_active", "=", 1)->as_object()->execute();


        // Default theme does not exist
        if(count($default_theme) < 1)
            throw new Exception_Theme("No default theme set");

        $default_theme_id = $default_theme[0]->theme_id;

        $layout = DB::select("theme_layout_content")->from(self::TABLE_THEME_LAYOUTS)
                ->where("theme_layout_slug", "=", $layout_slug)
                ->and_where("theme_layout_theme_id", "=", $default_theme_id)
                ->as_object()->execute();

        // The layout required does not exist
        if(count($layout) < 1)
            throw new Exception_Theme("Slug $layout_slug specified does not exist");

        // Return the layout's html content
        return $layout[0]->theme_layout_content;
    }

    /**
     * Sets the default system theme
     *
     * @param $theme_id
     * @return array
     */

    public static function set_default_theme($theme_id)
    {
        // Set a specific theme as the default theme

        // Deactivate all themes
        $deactivate_result = DB::update(self::TABLE_THEMES)->set(array("theme_active" => 0))->execute();

        // Activate a theme
        $activate_result = DB::update(self::TABLE_THEMES)->set(array("theme_active" => 1))
                ->where("theme_id", "=", $theme_id)
                ->execute();

        return array($deactivate_result, $activate_result);
    }

    /**
     * Gets public themes from a specific folder
     *
     * @return array of themes
     */

    public static function get_public_themes()
    {
        // Gets publicly submitted themes

        $folders = array();
        $extensions = array();

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(THEMEPATH),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($iterator as $file) {
            if($file->isDir()) {
                $folders[] = $file.DIRECTORY_SEPARATOR;
            }
        }

        foreach($folders as $folder) {
            if(self::is_valid_theme($folder)) {
                $extensions[] = $folder;
            }
        }

        return $extensions;
    }

    /**
     * Gets meta data for public themes
     *
     * @param array $themes
     * @return array of theme meta data
     */

    public static function get_public_theme_meta_data($themes = array())
    {
        // Public theme meta data

        $theme_meta_data = array();

        if(!is_array($themes))
            throw new Exception_Theme("Themes supplied are not in an array");

        foreach($themes as $theme) {
            if(self::is_valid_theme($theme)) {
                $theme_info_class = "\\".basename($theme)."\\".ucfirst(basename($theme))."_".self::INFO_PREFIX;
                $theme_info_object = new $theme_info_class();

                if(method_exists($theme_info_object, self::INFO_FUNCTION)) {
                    $meta_data = $theme_info_object->{self::INFO_FUNCTION}();
                    $directory_name = array("theme_folder" => basename($theme));

                    $theme_meta_data[basename($theme)] = array_merge($meta_data, $directory_name);
                }
            }
        }

        return $theme_meta_data;
    }

    /**
     * Gets system's core themes
     *
     * @return array of themes
     */

    public static function get_core_themes()
    {
        // Themes from the core team

        return self::get_public_themes();
    }

    /**
     * Get all partial files in a theme folder
     *
     * @static
     * @throws Exception_Theme
     * @param $theme_path
     * @return array
     */

    public static function get_theme_layouts($theme_path)
    {
        $partial_files = array();

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        $layouts_iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::LAYOUTS_FOLDER),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($layouts_iterator as $file) {
            if($file->isFile() && ($file->getExtension() == "html")) {
                $partial_files[basename($file)] = $file;
            }
        }

        return $partial_files;
    }

    /**
     * Get all layout files in a theme folder
     *
     * @static
     * @throws Exception_Theme
     * @param $theme_path
     * @return array
     */

    public static function get_theme_partials($theme_path)
    {
        $layout_files = array();

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        $layouts_iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::PARTIALS_FOLDER),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($layouts_iterator as $file) {
            if($file->isFile() && ($file->getExtension() == "html")) {
                $layout_files[basename($file)] = $file;
            }
        }

        return $layout_files;
    }

    /**
     * Get all javascript files in a theme folder
     *
     * @static
     * @throws Exception_Theme
     * @param $theme_path
     * @return array
     */

    public static function get_theme_javascript($theme_path)
    {
        $layout_files = array();

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        $layouts_iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::JAVASCRIPT_FOLDER),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($layouts_iterator as $file) {
            if($file->isFile() && ($file->getExtension() == "js")) {
                $layout_files[basename($file)] = $file;
            }
        }

        return $layout_files;
    }

    /**
     * Get all stylesheet files in a theme folder
     *
     * @static
     * @throws Exception_Theme
     * @param $theme_path
     * @return array
     */

    public static function get_theme_styles($theme_path)
    {
        $layout_files = array();

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        $layouts_iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::STYLES_FOLDER),
            RecursiveIteratorIterator::SELF_FIRST);

        foreach($layouts_iterator as $file) {
            if($file->isFile() && ($file->getExtension() == "css")) {
                $layout_files[basename($file)] = $file;
            }
        }

        return $layout_files;
    }

    /**
     * Get layout content for a specific theme
     *
     * @throws Exception_Theme
     * @param $theme_path
     * @param $layout_file
     * @return string
     */

    public static function get_layout_content($theme_path, $layout_file)
    {
        $file_path = THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::LAYOUTS_FOLDER.$layout_file;
        
        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        if(!file_exists($file_path))
            throw new Exception_Theme("The layout file $layout_file does not exist");

        $theme_contents = file_get_contents($file_path);

        return $theme_contents;
    }

    /**
     * Get partial content for a specific theme
     *
     * @throws Exception_Theme
     * @param $theme_path
     * @param $partial_name
     * @return string
     */

    public static function get_partial_content($theme_path, $partial_name)
    {
        $file_path = THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::PARTIALS_FOLDER.$partial_name;

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        if(!file_exists($file_path))
            throw new Exception_Theme("The partial file $partial_name does not exist");

        $theme_contents = file_get_contents($file_path);

        return $theme_contents;
    }

    /**
     * Get javascript content for a specific theme
     *
     * @throws Exception_Theme
     * @param $theme_path
     * @param $javascript_name
     * @return string
     */

    public static function get_javascript_content($theme_path, $javascript_name)
    {
        $file_path = THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::JAVASCRIPT_FOLDER.$javascript_name;

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        if(!file_exists($file_path))
            throw new Exception_Theme("The javascript file $javascript_name does not exist");

        $theme_contents = file_get_contents($file_path);

        return $theme_contents;
    }

    /**
     * Get style content for a specific theme
     *
     * @throws Exception_Theme
     * @param $theme_path
     * @param $style_name
     * @return string
     */

    public static function get_style_content($theme_path, $style_name)
    {
        $file_path = THEMEPATH.basename($theme_path).DIRECTORY_SEPARATOR.self::STYLES_FOLDER.$style_name;

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme at $theme_path is not a valid theme");

        if(!file_exists($file_path))
            throw new Exception_Theme("The style file $style_name does not exist");

        $theme_contents = file_get_contents($file_path);

        return $theme_contents;
    }

    /**
     * Gets the default installed theme_id
     *
     * @static
     * @throws Exception_Theme
     * @return
     */

    public static function get_installed_default_theme_id()
    {
        $installed_theme = DB::select("theme_id")->from(self::TABLE_THEMES)
                ->where(ThemeInfo::SEGMENT_THEME_ACTIVE, "=", 1)->as_object()->execute();

        if(count($installed_theme) < 1)
            throw new Exception_Theme("There is no default theme set up");

        return $installed_theme[0]->theme_id;
    }

    /**
     * Gets a layout entry item
     *
     * @static
     * @throws Exception_Theme
     * @param $layout_slug
     * @param int $theme_id
     * @return
     */

    public static function get_installed_layout_content($layout_slug, $theme_id = 0)
    {
        $selected_theme_id = $theme_id;

        if($theme_id == 0)
            $selected_theme_id = self::get_installed_default_theme_id();

        $layout_content = DB::select(ThemeInfo::SEGMENT_THEME_LAYOUT_CONTENT)->from(self::TABLE_THEME_LAYOUTS)
                ->where(ThemeInfo::SEGMENT_THEME_LAYOUT_SLUG, "=", $layout_slug)
                ->and_where(ThemeInfo::SEGMENT_THEME_LAYOUT_THEME_ID, "=", $selected_theme_id)
                ->as_object()->execute();

        if(count($layout_content) < 1)
            throw new Exception_Theme("Layout slug $layout_slug does not exist");

        return $layout_content[0]->{ThemeInfo::SEGMENT_THEME_LAYOUT_CONTENT};
    }

    /**
     * Gets a partial entry item
     *
     * @static
     * @throws Exception_Theme
     * @param $partial_slug
     * @param int $theme_id
     * @return
     */

    public static function get_installed_partial_content($partial_slug, $theme_id = 0)
    {
        $selected_theme_id = $theme_id;

        if($theme_id == 0)
            $selected_theme_id = self::get_installed_default_theme_id();

        $partial_content = DB::select(ThemeInfo::SEGMENT_THEME_PARTIAL_CONTENT)->from(self::TABLE_THEME_PARTIALS)
                ->where(ThemeInfo::SEGMENT_THEME_PARTIAL_SLUG, "=", $partial_slug)
                ->and_where(ThemeInfo::SEGMENT_THEME_PARTIAL_THEME_ID, "=", $selected_theme_id)
                ->as_object()->execute();

        if(count($partial_content) < 1)
            throw new Exception_Theme("Partial slug $partial_slug does not exist");

        return $partial_content[0]->{ThemeInfo::SEGMENT_THEME_PARTIAL_CONTENT};
    }

    /**
     * Gets a javascript entry item
     *
     * @static
     * @throws Exception_Theme
     * @param $js_slug
     * @param int $theme_id
     * @return
     */

    public static function get_installed_javascript_content($js_slug, $theme_id = 0)
    {
        $selected_theme_id = $theme_id;

        if($theme_id == 0)
            $selected_theme_id = self::get_installed_default_theme_id();

        $js_content = DB::select(ThemeInfo::SEGMENT_THEME_JS_CONTENT)->from(self::TABLE_JAVASCRIPT)
                ->where(ThemeInfo::SEGMENT_THEME_JS_SLUG, "=", $js_slug)
                ->and_where(ThemeInfo::SEGMENT_THEME_JS_THEME_ID, "=", $selected_theme_id)
                ->as_object()->execute();

        if(count($js_content) < 1)
            throw new Exception_Theme("Javascript slug $js_slug does not exist");

        return $js_content[0]->{ThemeInfo::SEGMENT_THEME_JS_CONTENT};
    }

    /**
     * Gets a stylesheet entry item
     *
     * @static
     * @throws Exception_Theme
     * @param $css_slug
     * @param int $theme_id
     * @return
     */

    public static function get_installed_style_content($css_slug, $theme_id = 0)
    {
        $selected_theme_id = $theme_id;

        if($theme_id == 0)
            $selected_theme_id = self::get_installed_default_theme_id();

        $css_content = DB::select(ThemeInfo::SEGMENT_THEME_CSS_CONTENT)->from(self::TABLE_STYLES)
                ->where(ThemeInfo::SEGMENT_THEME_CSS_SLUG, "=", $css_slug)
                ->and_where(ThemeInfo::SEGMENT_THEME_CSS_THEME_ID, "=", $selected_theme_id)
                ->as_object()->execute();

        if(count($css_content) < 1)
            throw new Exception_Theme("Stylesheet slug $css_slug does not exist");

        return $css_content[0]->{ThemeInfo::SEGMENT_THEME_CSS_CONTENT};
    }

    /**
     * Installs a theme from a public / theme folder
     *
     * @param $theme_path
     * @param bool $activate_theme
     */

    public static function install_theme($theme_path, $activate_theme = false)
    {
        // Install a theme for the current user

        if(!self::is_valid_theme($theme_path))
            throw new Exception_Theme("The theme $theme_path is not a valid theme");

        $themes_meta_data = self::get_public_theme_meta_data(array($theme_path));

        foreach($themes_meta_data as $theme_name => $theme_meta_data) {
            if(!isset($theme_meta_data[self::LAYOUT_PREFIX]) || !isset($theme_meta_data[self::PARTIAL_PREFIX]))
                throw new Exception_Theme("Data not supplied on layouts or partials from info()");

            if(!is_array($theme_meta_data[self::LAYOUT_PREFIX]))
                throw new Exception_Theme("Layout data supplied in the info() function is not in an array");

            if(!is_array($theme_meta_data[self::PARTIAL_PREFIX]))
                throw new Exception_Theme("Partial data supplied in the info() function is not in an array");

            // Arrays to use

            $layouts_array = array();
            $partials_array = array();
            $javascript_array = array();
            $styles_array = array();

            // Gets the layouts and partials from the file system

            $system_layouts = self::get_theme_layouts($theme_path);
            $system_partials = self::get_theme_partials($theme_path);
            $system_js = self::get_theme_javascript($theme_path);
            $system_css = self::get_theme_styles($theme_path);

            // Info file data supplied

            $info_theme = $theme_meta_data[self::THEME_PREFIX];
            $info_layouts = $theme_meta_data[self::LAYOUT_PREFIX];
            $info_partials = $theme_meta_data[self::PARTIAL_PREFIX];
            $info_js = $theme_meta_data[self::JS_PREFIX];
            $info_css = $theme_meta_data[self::CSS_PREFIX];

            // Cross tally

            DB::start_transaction();

            $base_theme_dir = basename($theme_path);

            list($theme_id, $num_records) = DB::insert(self::TABLE_THEMES)->set(array(
                ThemeInfo::SEGMENT_THEME_NAME => $info_theme->{ThemeInfo::SEGMENT_THEME_NAME},
                ThemeInfo::SEGMENT_THEME_SLUG => $info_theme->{ThemeInfo::SEGMENT_THEME_SLUG},
                ThemeInfo::SEGMENT_THEME_VERSION => $info_theme->{ThemeInfo::SEGMENT_THEME_VERSION},
                ThemeInfo::SEGMENT_THEME_FOLDER => $base_theme_dir,
                ThemeInfo::SEGMENT_THEME_ACTIVE => ($activate_theme ? 1 : 0),
                ThemeInfo::SEGMENT_THEME_DESCRIPTION => $info_theme->{ThemeInfo::SEGMENT_THEME_DESCRIPTION},
            ))->execute();

            // Layouts

            foreach($info_layouts as $layout_file => $layout_description) {
                if(array_key_exists($layout_file, $system_layouts)) {
                    $layout_details = array();
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_NAME] = $layout_description->{ThemeInfo::SEGMENT_THEME_LAYOUT_NAME};
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_SLUG] = $layout_description->{ThemeInfo::SEGMENT_THEME_LAYOUT_SLUG};
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_CONTENT] = self::get_layout_content($theme_path, $layout_file);
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_ACTIVE] = $layout_description->{ThemeInfo::SEGMENT_THEME_LAYOUT_ACTIVE};
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_DEFAULT] = $layout_description->{ThemeInfo::SEGMENT_THEME_LAYOUT_DEFAULT};
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_LAST_EDIT_TIME] = $layout_description->{ThemeInfo::SEGMENT_THEME_LAYOUT_LAST_EDIT_TIME};
                    $layout_details[ThemeInfo::SEGMENT_THEME_LAYOUT_THEME_ID] = $theme_id;

                    $layouts_array[] = $layout_details;
                }
            }

            // Partials

            foreach($info_partials as $partial_file => $partial_description) {
                if(array_key_exists($partial_file, $system_partials)) {
                    $partial_details = array();
                    $partial_details[ThemeInfo::SEGMENT_THEME_PARTIAL_NAME] = $partial_description->{ThemeInfo::SEGMENT_THEME_PARTIAL_NAME};
                    $partial_details[ThemeInfo::SEGMENT_THEME_PARTIAL_SLUG] = $partial_description->{ThemeInfo::SEGMENT_THEME_PARTIAL_SLUG};
                    $partial_details[ThemeInfo::SEGMENT_THEME_PARTIAL_CONTENT] = self::get_partial_content($theme_path, $partial_file);
                    $partial_details[ThemeInfo::SEGMENT_THEME_PARTIAL_ACTIVE] = $partial_description->{ThemeInfo::SEGMENT_THEME_PARTIAL_ACTIVE};
                    $partial_details[ThemeInfo::SEGMENT_THEME_PARTIAL_LAST_EDIT_TIME] = $partial_description->{ThemeInfo::SEGMENT_THEME_PARTIAL_LAST_EDIT_TIME};
                    $partial_details[ThemeInfo::SEGMENT_THEME_PARTIAL_THEME_ID] = $theme_id;

                    $partials_array[] = $partial_details;
                }
            }

            // Javascript

            foreach($info_js as $js_file => $js_description) {
                if(array_key_exists($js_file, $system_js)) {
                    $javascript_details = array();
                    $javascript_details[ThemeInfo::SEGMENT_THEME_JS_NAME] = $js_description->{ThemeInfo::SEGMENT_THEME_JS_NAME};
                    $javascript_details[ThemeInfo::SEGMENT_THEME_JS_SLUG] = $js_description->{ThemeInfo::SEGMENT_THEME_JS_SLUG};
                    $javascript_details[ThemeInfo::SEGMENT_THEME_JS_CONTENT] = self::get_javascript_content($theme_path, $js_file);
                    $javascript_details[ThemeInfo::SEGMENT_THEME_JS_THEME_ID] = $theme_id;

                    $javascript_array[] = $javascript_details;
                }
            }

            // Stylesheets

            foreach($info_css as $css_file => $css_description) {
                if(array_key_exists($css_file, $system_css)) {
                    $styles_details = array();
                    $styles_details[ThemeInfo::SEGMENT_THEME_CSS_NAME] = $css_description->{ThemeInfo::SEGMENT_THEME_CSS_NAME};
                    $styles_details[ThemeInfo::SEGMENT_THEME_CSS_SLUG] = $css_description->{ThemeInfo::SEGMENT_THEME_CSS_SLUG};
                    $styles_details[ThemeInfo::SEGMENT_THEME_CSS_CONTENT] = self::get_style_content($theme_path, $css_file);
                    $styles_details[ThemeInfo::SEGMENT_THEME_CSS_THEME_ID] = $theme_id;

                    $styles_array[] = $styles_details;
                }
            }

            // Insert all to database

            foreach($layouts_array as $layout_array_item) {
                DB::insert(self::TABLE_THEME_LAYOUTS)->set($layout_array_item)->execute();
            }

            foreach($partials_array as $partial_array_item) {
                DB::insert(self::TABLE_THEME_PARTIALS)->set($partial_array_item)->execute();
            }

            foreach($javascript_array as $javascript_array_item) {
                DB::insert(self::TABLE_JAVASCRIPT)->set($javascript_array_item)->execute();
            }

            foreach($styles_array as $styles_array_item) {
                DB::insert(self::TABLE_STYLES)->set($styles_array_item)->execute();
            }

            if($activate_theme) {
                self::set_default_theme($theme_id);
            }

            DB::commit_transaction();
        }
    }

    /**
     * Uninstalls a theme
     *
     * @static
     * @param $theme_id
     * @return void
     */

    public static function uninstall_theme($theme_id)
    {
        DB::start_transaction();

        DB::delete(self::TABLE_THEMES)->where("theme_id", "=", $theme_id)->execute();
        DB::delete(self::TABLE_THEME_LAYOUTS)->where(ThemeInfo::SEGMENT_THEME_LAYOUT_THEME_ID, "=", $theme_id)->execute();
        DB::delete(self::TABLE_THEME_PARTIALS)->where(ThemeInfo::SEGMENT_THEME_PARTIAL_THEME_ID, "=", $theme_id)->execute();
        DB::delete(self::TABLE_JAVASCRIPT)->where(ThemeInfo::SEGMENT_THEME_JS_THEME_ID, "=", $theme_id)->execute();
        DB::delete(self::TABLE_STYLES)->where(ThemeInfo::SEGMENT_THEME_CSS_THEME_ID, "=", $theme_id)->execute();

        DB::commit_transaction();
    }

    /**
     * Gets a list of installed themes
     *
     * @return array of themes
     */

    public static function get_installed_themes()
    {
        // Get installed themes

        $themes = DB::select("*")->from("themes")->as_object()->execute();

        return $themes;
    }

    /**
     * Executes a theme and its logic
     *
     * @param $slug
     * @param $data
     * @return theme_html_content
     */

    public function execute_theme($slug, $data)
    {
        // Executes the theme

        self::$theme_data = $data;

        $default_theme = self::load_default_theme_layout($slug);

        $lex_parser = new LexParser();
        $lex_parser->scopeGlue(':');
		$lex_parser->cumulativeNoParse(TRUE);
        
        $response_content = $lex_parser->parse($default_theme, self::$theme_data, array($this, 'parser_callback'));

        return $response_content;
    }

    /**
     * Deals with arrays when doing lex parser callbacks
     *
     * @param $array_items
     * @param $attributes
     * @param $content
     * @return string
     */

    private function process_array_callback_data($array_items, $attributes, $content)
    {
        $parse_string = "";
        
        foreach($array_items as $array_item) {
            $lex_parser = new \LexParser();
            $parse_string .= $lex_parser->parse($content, $array_item);
        }

        return $parse_string;
    }

    /**
     * Lex parser callback
     *
     * @throws Theme_Exception
     * @param $plugin
     * @param $attributes
     * @param $content
     * @return array
     */

    public function parser_callback($plugin, $attributes, $content)
    {
        $class = "";
        $method = "";

        try {
            list($class, $method) = explode(':', $plugin);
        }
        catch(ErrorException $e) {
            throw new Exception_Theme("Data for the $plugin tag has not been specified");
        }


        // Check out the core plugins first

        if(class_exists("Plugin_".ucfirst($class))) {
            // Use core plugin

            $class_name = "Plugin_".ucfirst($class);

            if(is_callable($class_name."::".$method)) {
                $return_data = call_user_func($class_name."::".$method, $attributes, $content);

                if(is_array($return_data)) {
                    return $this->process_array_callback_data($return_data, $attributes, $content);
                }

                return $return_data;
            }
            else {
                throw new Exception_Theme("Core plugin $class does not have the method $method");
            }
        }
        else {
            // Check out extension plugin

            $extension_meta_data = Extension::get_installed_extension_meta_data_by_slug($class);

            if(count($extension_meta_data) > 0) {
                $return_data = Extension::execute_plugin($extension_meta_data[0], $method, array(
                    "attributes" => $attributes,
                    "content" => $content));

                if(is_array($return_data)) {
                    return $this->process_array_callback_data($return_data, $attributes, $content);
                }

                return $return_data;
            }

            throw new Exception_Theme("Extension slug ".$class." not found");
        }
    }
}
