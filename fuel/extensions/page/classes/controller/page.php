<?php
/**
 * Main Public Controller for the Page Module
 *
 * @author     Ahmed Maawy and Nick Hargreaves
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;
 
class Controller_Page extends \Controller_Public {
    public function action_index($page_slug = "")
    {
		$set_slug = $page_slug;
			
    	if($page_slug == "")
		{
			$page_id = \Navigation::get_landing_page();
			
			if($page_id != null)
			{
				$page = Page::get_page_by_id($page_id);
				$set_slug = $page[0]["page_slug"];
			}
			else 
			{
				throw new \Exception_CMS("No landing page defined");
			}			
		}
		
        return $this->render_layout("main_layout_file", array("page_slug" => $set_slug));
    }
}
