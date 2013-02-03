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

    public static function load_extension($extension_folder)
    {
        Module::load($extension_folder, EXTPATH.$extension_folder.DIRECTORY_SEPARATOR);
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
}
