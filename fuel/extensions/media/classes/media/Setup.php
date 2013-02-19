<?php
/**
 * Setup class for the Media extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace media;

class Media_Setup extends \ExtensionSetup implements \ISetup {

    const TABLE_MEDIA = "media";

    /**
     * Install the extension
     * 
     * @return array
     */

    public function install_extension()
    {
        $tables = array();

        // Main media table

        $meta_data[self::META_TABLE_NAME] = self::TABLE_MEDIA;

        $meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Media item", "media_item", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_FILE, ""),
            new \DBFieldMeta("Object Slug", "object_slug", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Object ID", "object_id", \DBFieldMeta::FIELD_BIGINT, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Media creation time", "media_creation_time", \DBFieldMeta::FIELD_TIMESTAMP, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
        );

        $meta_data[self::META_SETTINGS] = array();

        $tables[] = $meta_data;

        // Build the setup

        $media_info = new Media_Info();

        parent::install($tables, $media_info->info());

        // Create the media folder in the uploads folder if it does not exist

        if(!is_dir(UPLOADPATH.self::TABLE_MEDIA))
            mkdir(UPLOADPATH.self::TABLE_MEDIA);

        // Return required data

        return $tables;
    }

    /**
     * Uninstall the extension
     * 
     * @param $extension_id
     * @return void
     */

    public function uninstall_extension($extension_id)
    {
        $tables = array(self::TABLE_MEDIA);
        parent::uninstall($extension_id, $tables);
    }
}
