<?php
/**
 * Setup class for the Page extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;

class Page_Setup extends \ExtensionSetup implements \ISetup {

    const TABLE_PAGES = "pages";
    const TABLE_MEDIA = "pages_media";

    /**
     * Install the extension
     *
     * @return array
     */

    public function install_extension()
    {
        $tables = array();

        // Create table meta data

        $pages_meta_data = array();
        $media_meta_data = array();

        // Pages meta data

        $pages_meta_data[self::META_TABLE_NAME] = self::TABLE_PAGES;
        
        $pages_meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Page title", "page_title", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_SIMPLE_TEXT, ""),
            new \DBFieldMeta("Page slug","page_slug", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Page content","page_content", \DBFieldMeta::FIELD_TEXT, 0, \DBFieldMeta::CONTROL_RICH_EDIT, ""),
            new \DBFieldMeta("Page creation time","page_creation_time", \DBFieldMeta::FIELD_TIMESTAMP, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Page status","page_status", \DBFieldMeta::FIELD_LIST, 0, \DBFieldMeta::CONTROL_LIST, "Draft|Live"),
            new \DBFieldMeta("Page active", "page_active", \DBFieldMeta::FIELD_INT, 0, \DBFieldMeta::CONTROL_CHECKBOX, "1"),
        );
        $pages_meta_data[self::META_SETTINGS] = array();

        $tables[] = $pages_meta_data;

        // Page media meta data

        $media_meta_data[self::META_TABLE_NAME] = self::TABLE_MEDIA;

        $media_meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Page ID", "page_id", \DBFieldMeta::FIELD_BIGINT, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Media ID", "media_id", \DBFieldMeta::FIELD_BIGINT, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
        );
        $media_meta_data[self::META_SETTINGS] = array();

        $tables[] = $media_meta_data;

        // Build the installation

        $page_info = new Page_Info();

        parent::install($tables, $page_info->info());

        return $tables;
    }

    /**
     * Uninstall the extension
     *
     * @param $extension_id
     * @return array
     */

    public function uninstall_extension($extension_id)
    {
        $tables = array(self::TABLE_PAGES, self::TABLE_MEDIA);
        parent::uninstall($extension_id, $tables);
    }
}
