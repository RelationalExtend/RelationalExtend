<?php
/**
 * General info class for extensions to use
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class ExtensionInfo {
    const SEGMENT_EXTENSION_NAME = "extension_name";
    const SEGMENT_EXTENSION_SLUG = "extension_slug";
    const SEGMENT_EXTENSION_VERSION = "extension_version";
    const SEGMENT_EXTENSION_DESCRIPTION = "extension_description";
    const SEGMENT_EXTENSION_FOLDER = "extension_folder";
    const SEGMENT_EXTENSION_ACTIVE = "extension_active";

    protected $extension_data;

    /**
     * Initialize the object
     */
    public function __construct()
    {
        $this->extension_data = new stdClass();
    }

    /**
     * Gets the extension's base folder
     * 
     * @param $folder
     * @return string
     */
    protected function get_base_folder($folder)
    {
        return basename(realpath($folder.'/../../').DIRECTORY_SEPARATOR);
    }

    /**
     * Build the extension's meta data
     *
     * @param $name
     * @param $description
     * @param $install_folder
     * @param string $version
     * @return void
     */
    protected function build_extension_data($name, $description, $install_folder, $version = "1.0")
    {
        $ex_data = &$this->extension_data;

        $slug = Utility::slugify($name);

        $ex_data->{self::SEGMENT_EXTENSION_NAME} = $name;
        $ex_data->{self::SEGMENT_EXTENSION_SLUG} = $slug;
        $ex_data->{self::SEGMENT_EXTENSION_VERSION} = $version;
        $ex_data->{self::SEGMENT_EXTENSION_FOLDER} = $install_folder;
        $ex_data->{self::SEGMENT_EXTENSION_DESCRIPTION} = $description;
    }
}
