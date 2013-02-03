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
        $blog_posts = \Fuel\Core\DB::select("*")->from(Blog_Setup::TABLE_BLOG)->execute()->as_array();

        return $blog_posts;
    }
}
