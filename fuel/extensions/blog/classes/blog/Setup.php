<?php
/**
 * Setup class for the Blog extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;

class Blog_Setup extends \ExtensionSetup implements \ISetup {

    const TABLE_BLOG = "blog";

    /**
     * Install the extension
     * 
     * @return array
     */

    public function install_extension()
    {
        $tables = array();

        $meta_data[self::META_TABLE_NAME] = self::TABLE_BLOG;

        $meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Blog title", "blog_title", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_SIMPLE_TEXT, ""),
            new \DBFieldMeta("Blog slug","blog_slug", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Blog summary","blog_summary", \DBFieldMeta::FIELD_TEXT, 0, \DBFieldMeta::CONTROL_RICH_EDIT, ""),
            new \DBFieldMeta("Blog content","blog_content", \DBFieldMeta::FIELD_TEXT, 0, \DBFieldMeta::CONTROL_RICH_EDIT, ""),
            new \DBFieldMeta("Blog post creation time","blog_post_creation_time", \DBFieldMeta::FIELD_TIMESTAMP, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Post status","post_status", \DBFieldMeta::FIELD_LIST, 0, \DBFieldMeta::CONTROL_LIST, "Draft|Live"),
            new \DBFieldMeta("Post active", "post_active", \DBFieldMeta::FIELD_INT, 0, \DBFieldMeta::CONTROL_CHECKBOX, ""),
        );

        $meta_data[self::META_SETTINGS] = array(
            new \DBFieldMeta("Default number of posts to display per page", "posts_per_page", \DBFieldMeta::FIELD_INT, 0, \DBFieldMeta::CONTROL_SIMPLE_TEXT, "20"),
        );

        $tables[] = $meta_data;

        $blog_info = new Blog_Info();

        parent::install($tables, $blog_info->info());

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
        $tables = array(self::TABLE_BLOG);
        parent::uninstall($extension_id, $tables);
    }
}
