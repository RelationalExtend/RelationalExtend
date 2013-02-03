<?php
/**
 * Manage sitewide settings
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

 
class Settings {

    const EXTENSION_SETTINGS_TABLE = "extension_settings";
    const PUBLIC_SETTINGS_TABLE = "settings";

    /**
     * Initialize the object
     */

    public function __construct()
    {
        // Constructor
    }

    /**
     * Get settings for a specific extension
     * 
     * @param $extension_id
     * @return setting_rows
     */

    public function get_extension_settings($extension_id)
    {
        $extension_settings = DB::select("*")->from(self::EXTENSION_SETTINGS_TABLE)
                ->where("extension_setting_extension_id", "=", $extension_id)->as_object()->execute();

        return $extension_settings;
    }

    /**
     * Get a setting from a specific slug
     *
     * @param $setting_slug
     * @return setting
     */

    public function get_public_setting($setting_slug)
    {
        $settings = DB::select("*")->from(self::PUBLIC_SETTINGS_TABLE)
                ->where("setting_slug", "=", $setting_slug)->as_object()->execute();

        return $settings;
    }
}
