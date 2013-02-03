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

    /**
     * Install the extension
     *
     * @return array
     */

    public function install_extension()
    {
        $tables = array();

        // Create table meta data
        $meta_data = array();
        $meta_data[self::META_TABLE_NAME] = self::TABLE_PAGES;
        $meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Page title", "page_title", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_SIMPLE_TEXT, ""),
            new \DBFieldMeta("Page slug","page_slug", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Page content","page_content", \DBFieldMeta::FIELD_TEXT, 0, \DBFieldMeta::CONTROL_RICH_EDIT, ""),
            new \DBFieldMeta("Page creation time","page_creation_time", \DBFieldMeta::FIELD_TIMESTAMP, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Page status","page_status", \DBFieldMeta::FIELD_LIST, 0, \DBFieldMeta::CONTROL_LIST, "Draft|Live"),
            new \DBFieldMeta("Page active", "page_active", \DBFieldMeta::FIELD_INT, 0, \DBFieldMeta::CONTROL_CHECKBOX, "1"),
        );
        $meta_data[self::META_SETTINGS] = array();

        $tables[] = $meta_data;

        $page_info = new Page_Info();

        // Install tables
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
        $tables = array(self::TABLE_PAGES);
        parent::uninstall($extension_id, $tables);
    }
}
