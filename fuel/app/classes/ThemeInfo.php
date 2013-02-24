<?php
/**
 * General info class for themes to use
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

 
class ThemeInfo {
    const SEGMENT_THEME_NAME = "theme_name";
    const SEGMENT_THEME_SLUG = "theme_slug";
    const SEGMENT_THEME_VERSION = "theme_version";
    const SEGMENT_THEME_DESCRIPTION = "theme_description";
    const SEGMENT_THEME_FOLDER = "theme_folder";
    const SEGMENT_THEME_CORE = "theme_core";
    const SEGMENT_THEME_ACTIVE = "theme_active";
    const SEGMENT_THEME_INSTALL_TIME = "theme_install_time";

	const SEGMENT_THEME_LAYOUT_ID = "theme_layout_id";
    const SEGMENT_THEME_LAYOUT_NAME = "theme_layout_name";
    const SEGMENT_THEME_LAYOUT_SLUG = "theme_layout_slug";
    const SEGMENT_THEME_LAYOUT_CONTENT = "theme_layout_content";
    const SEGMENT_THEME_LAYOUT_ACTIVE = "theme_layout_active";
    const SEGMENT_THEME_LAYOUT_DEFAULT = "theme_layout_default";
    const SEGMENT_THEME_LAYOUT_LAST_EDIT_TIME = "theme_layout_last_edit_time";
    const SEGMENT_THEME_LAYOUT_THEME_ID = "theme_layout_theme_id";

    const SEGMENT_THEME_PARTIAL_ID = "theme_partial_id";
    const SEGMENT_THEME_PARTIAL_NAME = "theme_partial_name";
    const SEGMENT_THEME_PARTIAL_SLUG = "theme_partial_slug";
    const SEGMENT_THEME_PARTIAL_CONTENT = "theme_partial_content";
    const SEGMENT_THEME_PARTIAL_ACTIVE = "theme_partial_active";
    const SEGMENT_THEME_PARTIAL_LAST_EDIT_TIME = "theme_partial_last_edit_time";
    const SEGMENT_THEME_PARTIAL_THEME_ID = "theme_partial_theme_id";

    const SEGMENT_THEME_JS_ID = "theme_js_id";
    const SEGMENT_THEME_JS_NAME = "theme_js_name";
    const SEGMENT_THEME_JS_SLUG = "theme_js_slug";
    const SEGMENT_THEME_JS_CONTENT = "theme_js_content";
    const SEGMENT_THEME_JS_THEME_ID = "theme_js_theme_id";

    const SEGMENT_THEME_CSS_ID = "theme_css_id";
    const SEGMENT_THEME_CSS_NAME = "theme_css_name";
    const SEGMENT_THEME_CSS_SLUG = "theme_css_slug";
    const SEGMENT_THEME_CSS_CONTENT = "theme_css_content";
    const SEGMENT_THEME_CSS_THEME_ID = "theme_css_theme_id";

    protected $theme_data;
    protected $layout_data = array();
    protected $partial_data = array();
    protected $js_data = array();
    protected $css_data = array();

    /**
     * Initialize the object
     */
    public function __construct()
    {
        $this->theme_data = new stdClass();
    }

    /**
     * Gets the theme's base folder
     *
     * @param $folder
     * @return string
     */
    protected function get_base_folder($folder)
    {
        return basename(realpath($folder.'/../../').DIRECTORY_SEPARATOR);
    }

    /**
     * Build the theme's meta data
     *
     * @param $name
     * @param $description
     * @param $install_folder
     * @param $install_time
     * @param int $active
     * @param string $version
     * @return void
     */

    protected function build_theme_data($name, $description, $install_folder, $install_time, $active = 0, $version = "1.0")
    {
        $theme_data = &$this->theme_data;

        $slug = Utility::slugify($name);

        $theme_data->{self::SEGMENT_THEME_NAME} = $name;
        $theme_data->{self::SEGMENT_THEME_SLUG} = $slug;
        $theme_data->{self::SEGMENT_THEME_VERSION} = $version;
        $theme_data->{self::SEGMENT_THEME_FOLDER} = $install_folder;
        $theme_data->{self::SEGMENT_THEME_DESCRIPTION} = $description;
        $theme_data->{self::SEGMENT_THEME_INSTALL_TIME} = $install_time;
        $theme_data->{self::SEGMENT_THEME_ACTIVE} = $active;
    }

    /**
     * Build a layout's data items
     *
     * @param $layout_file
     * @param $name
     * @param $content
     * @param $theme_id
     * @param int $active
     * @param int $default
     * @return void
     */

    protected function build_theme_layout_data($layout_file, $name, $content, $theme_id, $active = 1, $default = 0)
    {
        $this->layout_data[$layout_file] = new stdClass();
        $layout_data = &$this->layout_data[$layout_file];

        $slug = Utility::slugify($name);

        $layout_data->{self::SEGMENT_THEME_LAYOUT_NAME} = $name;
        $layout_data->{self::SEGMENT_THEME_LAYOUT_SLUG} = $slug;
        $layout_data->{self::SEGMENT_THEME_LAYOUT_CONTENT} = $content;
        $layout_data->{self::SEGMENT_THEME_LAYOUT_ACTIVE} = $active;
        $layout_data->{self::SEGMENT_THEME_LAYOUT_DEFAULT} = $default;
        $layout_data->{self::SEGMENT_THEME_LAYOUT_LAST_EDIT_TIME} = time();
        $layout_data->{self::SEGMENT_THEME_LAYOUT_THEME_ID} = $theme_id;
    }

    /**
     * Build a partial's data items
     *
     * @param $layout_file
     * @param $name
     * @param $content
     * @param $theme_id
     * @param int $active
     *
     */

    protected function build_theme_partial_data($layout_file, $name, $content, $theme_id, $active = 0)
    {
        $this->partial_data[$layout_file] = new stdClass();
        $partial_data = &$this->partial_data[$layout_file];

        $slug = Utility::slugify($name);

        $partial_data->{self::SEGMENT_THEME_PARTIAL_NAME} = $name;
        $partial_data->{self::SEGMENT_THEME_PARTIAL_SLUG} = $slug;
        $partial_data->{self::SEGMENT_THEME_PARTIAL_CONTENT} = $content;
        $partial_data->{self::SEGMENT_THEME_PARTIAL_ACTIVE} = $active;
        $partial_data->{self::SEGMENT_THEME_PARTIAL_LAST_EDIT_TIME} = time();
        $partial_data->{self::SEGMENT_THEME_PARTIAL_THEME_ID} = $theme_id;
    }

    /**
     * Builds a Javascript theme component
     *
     * @param $javascript_file
     * @param $name
     * @param $content
     * @return void
     */

    protected function build_theme_javascript_data($javascript_file, $name, $content)
    {
        $this->js_data[$javascript_file] = new stdClass();
        $javascript_data = &$this->js_data[$javascript_file];

        $slug = Utility::slugify($name);

        $javascript_data->{self::SEGMENT_THEME_JS_NAME} = $name;
        $javascript_data->{self::SEGMENT_THEME_JS_SLUG} = $slug;
        $javascript_data->{self::SEGMENT_THEME_JS_CONTENT} = $content;
    }

    /**
     * Builds a CSS theme component
     *
     * @param $style_file
     * @param $name
     * @param $content
     * @return void
     */

    protected function build_theme_style_data($style_file, $name, $content)
    {
        $this->css_data[$style_file] = new stdClass();
        $style_data = &$this->css_data[$style_file];

        $slug = Utility::slugify($name);

        $style_data->{self::SEGMENT_THEME_CSS_NAME} = $name;
        $style_data->{self::SEGMENT_THEME_CSS_SLUG} = $slug;
        $style_data->{self::SEGMENT_THEME_CSS_CONTENT} = $content;
    }
}