<?php
/**
 * Plugin file for the Blog extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;

class Blog_Plugin implements \IPlugin {
    public function posts($attributes, $content)
    {
        $blog_posts = Blog::get_posts();

        foreach($blog_posts as &$blog_post)
        {
            $blog_post["post_url"] = (date("Y/m/d", strtotime($blog_post["blog_post_creation_time"])))."/".$blog_post["blog_slug"];
        }

        return $blog_posts;
    }
}
