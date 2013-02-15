<?php
/**
 * Pages extension functions
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;

class Page {
    public static function get_pages()
    {
        $pages = \Fuel\Core\DB::select("*")->from(Page_Setup::TABLE_PAGES)
                ->where("page_status", "=", "Live")
                ->and_where("page_active", "=", 1)
                ->execute()->as_array();

        return $pages;
    }

    public static function get_page_by_slug($slug)
    {
        $pages = \Fuel\Core\DB::select("*")->from(Page_Setup::TABLE_PAGES)
                ->where("page_slug", "=", $slug)
                ->and_where("page_status", "=", "Live")
                ->and_where("page_active", "=", 1)
                ->execute()->as_array();

        return $pages;
    }
}
