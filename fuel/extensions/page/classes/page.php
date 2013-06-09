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
	
	public static function get_page_by_id($page_id)
    {
        $pages = \Fuel\Core\DB::select("*")->from(Page_Setup::TABLE_PAGES)
                ->where("id", "=", $page_id)
                ->and_where("page_status", "=", "Live")
                ->and_where("page_active", "=", 1)
                ->execute()->as_array();

        return $pages;
    }
	
	public static function get_any_page_by_id($page_id)
    {
        $pages = \Fuel\Core\DB::select("*")->from(Page_Setup::TABLE_PAGES)
                ->where("id", "=", $page_id)
                ->execute()->as_array();

        return $pages;
    }
	
	public static function delete_page($page_id)
	{
		$result = \Fuel\Core\DB::delete(Page_Setup::TABLE_PAGES)
			->where("id", "=", $page_id)
			->execute();
			
		return $result;
	}
	
	public static function activate_page($page_id)
	{
		$result = \Fuel\Core\DB::update(Page_Setup::TABLE_PAGES)
			->set(array("page_active" => 1))
			->where("id", "=", $page_id)
			->execute();
			
		return $result;
	}
	
	public static function deactivate_page($page_id)
	{
		$result = \Fuel\Core\DB::update(Page_Setup::TABLE_PAGES)
			->set(array("page_active" => 0))
			->where("id", "=", $page_id)
			->execute();
			
		return $result;
	}
}
