<?php
/**
 * Extension class for the Blog extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;

class Blog_Extension implements \IExtension {

    /**
     * Execute the extension
     * @param array $parameters
     * @return parameters
     */

    public function execute_extension($parameters = array())
    {
        if(isset($parameters["year"]) && isset($parameters["month"]) &&
            isset($parameters["day"]) && isset($parameters["slug"]))
        {
            $blog_post = Blog::get_post_by_parameters($parameters["year"],
                $parameters["month"], $parameters["day"], $parameters["slug"]);

            if(count($blog_post) > 0)
            {
                return array("title" => $blog_post[0]["blog_title"], "body" => $blog_post[0]["blog_content"]);
            }
			else {
				throw new Exception_Blog("Specified blog post does not exist");
			}  
        }
        else
        {
            return array();
        }
    }
}
