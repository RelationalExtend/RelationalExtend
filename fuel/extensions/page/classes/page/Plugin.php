<?php
/**
 * Plugin file for the Pages extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;
 
class Page_Plugin implements \IPlugin {
    public function pages($attributes, $content)
    {
        $pages = array();

        if(isset($attributes["slug"])) {
            $slug = $attributes["slug"];
            $pages = Page::get_page_by_slug($slug);
        }
        else {
            $pages = Page::get_pages();
        }
        
        return $pages;
    }
}
