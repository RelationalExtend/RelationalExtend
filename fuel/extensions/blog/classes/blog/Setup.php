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
    const TABLE_CATEGORIES = "blog_categories";
	const TABLE_CATEGORIES_RELATIONSHIPS = "blog_categories_relationship";

    /**
     * Install the extension
     * 
     * @return array
     */

    public function install_extension()
    {
        $tables = array();

        // Main blog table

        $blog_meta_data = array();

        $blog_meta_data[self::META_TABLE_NAME] = self::TABLE_BLOG;

        $blog_meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Blog title", "blog_title", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_SIMPLE_TEXT, ""),
            new \DBFieldMeta("Blog cover image", "blog_cover_image", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_FILE, ""),
            new \DBFieldMeta("Blog slug","blog_slug", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Blog category","blog_category", \DBFieldMeta::FIELD_BIGINT, 0, \DBFieldMeta::CONTROL_MULTISELECT,
                "table=".self::TABLE_CATEGORIES."|id_field=id|description_field=blog_category|relationship_table=".self::TABLE_CATEGORIES_RELATIONSHIPS),
            new \DBFieldMeta("Blog summary","blog_summary", \DBFieldMeta::FIELD_TEXT, 0, \DBFieldMeta::CONTROL_RICH_EDIT, ""),
            new \DBFieldMeta("Blog content","blog_content", \DBFieldMeta::FIELD_TEXT, 0, \DBFieldMeta::CONTROL_RICH_EDIT, ""),
            new \DBFieldMeta("Blog post creation time","blog_post_creation_time", \DBFieldMeta::FIELD_TIMESTAMP, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
            new \DBFieldMeta("Post status","post_status", \DBFieldMeta::FIELD_LIST, 0, \DBFieldMeta::CONTROL_LIST, "Draft|Live"),
            new \DBFieldMeta("Post active", "post_active", \DBFieldMeta::FIELD_INT, 0, \DBFieldMeta::CONTROL_CHECKBOX, ""),
        );

        $blog_meta_data[self::META_SETTINGS] = array(
            new \DBFieldMeta("Default number of posts to display per page", "posts_per_page", \DBFieldMeta::FIELD_INT, 0, \DBFieldMeta::CONTROL_SIMPLE_TEXT, "20"),
        );

        $tables[] = $blog_meta_data;

        // Categories table

        $categories_meta_data = array();

        $categories_meta_data[self::META_TABLE_NAME] = self::TABLE_CATEGORIES;

        $categories_meta_data[self::META_FIELDS] = array(
            new \DBFieldMeta("Blog category", "blog_category", \DBFieldMeta::FIELD_VARCHAR, 200, \DBFieldMeta::CONTROL_SIMPLE_TEXT, ""),
        );

        $categories_meta_data[self::META_SETTINGS] = array();

        $tables[] = $categories_meta_data;
		
		// Relationship table
		
		$categories_relationship_data = array();
		
		$categories_relationship_data[self::META_TABLE_NAME] = self::TABLE_CATEGORIES_RELATIONSHIPS;
		
		$categories_relationship_data[self::META_FIELDS] = array(
			new \DBFieldMeta(\DBFieldMeta::FIELD_RELATIONSHIP_MASTER_FIELD_CAPTION, \DBFieldMeta::FIELD_RELATIONSHIP_MASTER_FIELD, \DBFieldMeta::FIELD_BIGINT, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
			new \DBFieldMeta(\DBFieldMeta::FIELD_RELATIONSHIP_DETAIL_FIELD_CAPTION, \DBFieldMeta::FIELD_RELATIONSHIP_DETAIL_FIELD, \DBFieldMeta::FIELD_BIGINT, 0, \DBFieldMeta::CONTROL_HIDDEN, ""),
		);
		
		$categories_relationship_data[self::META_SETTINGS] = array();
		
		$tables[] = $categories_relationship_data;

        // Build the setup

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
        $tables = array(self::TABLE_BLOG, self::TABLE_CATEGORIES, self::TABLE_CATEGORIES_RELATIONSHIPS);
        parent::uninstall($extension_id, $tables);
    }
}
