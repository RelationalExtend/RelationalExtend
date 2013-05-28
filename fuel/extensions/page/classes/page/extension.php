<?php
/**
 * Extension class for the Pages extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;

class Page_Extension implements \IExtension {

    /**
     * Execute the extension
     * @param array $parameters
     * @return void
     */

    public function execute_extension($parameters = array())
    {
        if(isset($parameters["page_slug"]))
        {
            $page = Page::get_page_by_slug($parameters["page_slug"]);

            if(count($page) > 0)
            {
                return array("heading" => $page[0]["page_title"], "content" => $page[0]["page_content"]);
            }
            else
            {
                $page = Page::get_page_by_slug("home");

                if(count($page) > 0)
                {
                    return array("heading" => $page[0]["page_title"], "content" => $page[0]["page_content"]);
                }
                else
                {
                    throw new Exception_Page("No home page specified");
                }
            }
        }

        return array();
    }
}
