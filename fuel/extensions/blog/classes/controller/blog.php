<?php
/**
 * Main Public Controller for the Blog Module.
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;
 
class Controller_Blog extends \Controller_Public {
    public function action_index()
    {
        return $this->render_layout("blog_layout_file");
    }

    public function action_post($year, $month, $day, $slug)
    {
        return $this->render_layout("blog_post_layout_file", array("year" => $year,
            "month" => $month, "day" => $day, "slug" => $slug));
    }
}
