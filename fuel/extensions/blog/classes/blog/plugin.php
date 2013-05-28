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
			$route_path = "blog/blog/posts";
			
			\Config::load("navigation", "nav");
			$short_path = \Config::get("nav.blog_posts.short_route", null);
			
			if($short_path != null)
				$route_path = $short_path;
			
			$blog_post["post_url"] = \Fuel\Core\Uri::base().$route_path.$blog_post["post_url"];
        }

        return $blog_posts;
    }
}
