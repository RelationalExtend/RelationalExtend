<?php
/**
 * Blogs extension functions
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;

class Blog {
    public static function get_posts()
    {
        $blog_posts = \Fuel\Core\DB::select("*")->from(Blog_Setup::TABLE_BLOG)
                ->where("post_status", "=", "Live")
                ->and_where("post_active", "=", 1)
                ->execute()->as_array();

        return $blog_posts;
    }

    public static function get_post_by_parameters($year, $month, $day, $slug)
    {
        $blog_post = \Fuel\Core\DB::query("SELECT * FROM ".\Fuel\Core\DB::table_prefix(Blog_Setup::TABLE_BLOG)."
            WHERE blog_post_creation_time BETWEEN '$year-$month-$day 00:00:00' AND '$year-$month-$day 23:59:59'
            AND blog_slug = '$slug'")->execute()->as_array();

        return $blog_post;
    }
}
