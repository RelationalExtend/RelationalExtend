<?php

/**
 * CMS Initialization functions
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class CMSInit {

    /**
     * Loads an extension
     *
     * @param $extension_folder
     * @return void
     */

    private static $installed_extensions = array();
    private static $installed_themes = array();
	
	const TABLE_SETTINGS = "settings";

    public static function load_extension($extension_folder)
    {
        Module::load($extension_folder, EXTPATH.$extension_folder.DIRECTORY_SEPARATOR);
        self::$installed_extensions[] = $extension_folder;
    }

    /**
     * Loads a theme
     *
     * @param $theme_folder
     * @return void
     */

    public static function load_theme($theme_folder)
    {
        Module::load($theme_folder, THEMEPATH.$theme_folder.DIRECTORY_SEPARATOR);
        self::$installed_themes[] = $theme_folder;
    }

    /**
     * Load all extensions and themes
     *
     * @static
     * @return void
     */

    public static function init_componenets()
    {
        $installed_extensions = Extension::get_installed_extensions(true);
        $installed_themes = CMSTheme::get_installed_themes();

        foreach($installed_extensions as $installed_extension) {
            self::load_extension($installed_extension->extension_folder);
        }

        foreach($installed_themes as $installed_theme) {
            self::load_theme($installed_theme->theme_folder);
        }
    }

    /**
     * Verifies if an extension is installed
     *
     * @static
     * @param $extension_name
     * @return bool
     */

    public static function is_extension_installed($extension_name)
    {
        foreach(self::$installed_extensions as $installed_extension)
        {
            if(strtolower($installed_extension) == strtolower($extension_name))
                return true;
        }

        return false;
    }

    /**
     * Verifies if the theme is installed
     *
     * @static
     * @param $theme_name
     * @return bool
     */

    public static function is_theme_installed($theme_name)
    {
        foreach(self::$installed_themes as $installed_theme)
        {
            if(strtolower($installed_theme) == strtolower($theme_name))
                return true;
        }

        return false;
    }
}
