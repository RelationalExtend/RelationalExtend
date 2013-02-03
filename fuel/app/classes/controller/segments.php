<?php
/**
 * Segments controller. Generates segments of the theme
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Controller_Segments extends Controller {

    public function action_js($slug)
    {
        echo(CMSTheme::get_installed_javascript_content($slug));
    }

    public function action_css($slug)
    {
        echo(CMSTheme::get_installed_style_content($slug));
    }
}
