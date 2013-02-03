<?php
/**
 * Main Public Controller for the Page Module
 *
 * @author     Ahmed Maawy and Nick Hargreaves
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;
 
class Controller_Page extends \Controller_Public {
    public function action_index($page_slug)
    {
        return $this->render_layout("main_layout_file", array("page_slug" => $page_slug));
    }
}
