<?php
/**
 * Navigation management
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Navigation {
	
	const TABLE_NAVIGATION = 'navigation';
	
	const NAV_MODULE = 'module';
	const NAV_PAGE = 'page';
	
	const NAV_ITEM_ID = 'nav_id';
	const NAV_ITEM_URL = 'url';
	const NAV_ITEM_TEXT = 'text';
	const NAV_ITEM_TYPE = 'nav_type';
	const NAV_ITEM_OBJECT_ID = 'object_id';
	const NAV_ITEM_NAV_ORDER = 'navigation_order';
	
	/**
	 * Gets a page slug from page id
	 * 
	 * @param $page_id	
	 * @return $slug 
	 */
	
	private static function get_page_slug_by_id($page_id)
	{
		$page = DB::select('page_slug')->from(\page\Page_Setup::TABLE_PAGES)->where('id', "=", $page_id)
			->as_object()->execute();
			
		if(count($page) > 0)
			return $page[0]->page_slug;
		
		return null;
	}
	
	/**
	 * Gets an extension folder from id
	 * 
	 * @param $extension_id
	 * @return $folder
	 */
	
	private static function get_extension_folder_by_id($extension_id)
	{
		$extension = DB::select('extension_folder')->from(Extension::EXTENSIONS_TABLE)
			->where('extension_id', '=', $extension_id)->as_object()->execute();
			
		if(count($extension) > 0)
			return $extension[0]->extension_folder;
		
		return null;
	}
	
	/**
	 * Creates a navigation item
	 * 
	 * @param text
	 * @param type
	 * @param $object_id
	 * @param $order
	 * @return $insert_result
	 */
	
	private static function create_navigation_item($text, $type, $object_id, $order)
	{
		return DB::insert(self::TABLE_NAVIGATION)->set(array(
				'navigation_text' => $text,
				'navigation_type' => $type,
				'navigation_object_id' => $object_id,
				'navigation_order' => $order))
			->execute();
	}
	
	/**
	 * Removes an item from the navigation
	 * 
	 * @param $navigation_id
	 */
	
	private static function get_navigation_item($navigation_item_id)
	{
		$nav_record = DB::select('navigation_order')->from(self::TABLE_NAVIGATION)
				->where('navigation_id', "=", $navigation_item_id)->as_object()->execute();
				
		return $nav_record;
	}
	
	/**
	 * Gets all navigation items
	 * 
	 * @param $short_urls = array()
	 * @param $as_array = false
	 * @return $navigation_items
	 */
	
	public static function get_all_navigation_items($short_urls = array(), $as_array = false)
	{
		$navigation_records = DB::select("*")->from(self::TABLE_NAVIGATION)->order_by('navigation_order', 'asc')
			->as_object()->execute();
			
		$page_module_installed = CMSInit::is_extension_installed(self::NAV_PAGE);
		$navigation_items = array();
		
		Config::load("navigation", "nav");
		
		foreach($navigation_records as $navigation_record)
		{
			if($navigation_record->navigation_type == self::NAV_PAGE)
			{
				$page_urls = Uri::base()."page/page/index/";
				
				$nav_url = Config::get("nav.page.short_route", null);
				
				if($nav_url != null)
					$page_urls = Uri::base().$nav_url;
				
				if(in_array(self::NAV_PAGE, $short_urls))
				{
					$page_urls = Uri::base().rtrim($short_urls[self::NAV_PAGE], "/")."/";
				}
				
				if($page_module_installed)
				{
					$navigation_entry = new stdClass;
					$navigation_entry->{self::NAV_ITEM_ID} = $navigation_record->navigation_id;
					$navigation_entry->{self::NAV_ITEM_OBJECT_ID} = $navigation_record->navigation_object_id;
					$navigation_entry->{self::NAV_ITEM_TYPE} = $navigation_record->navigation_type;
					$navigation_entry->{self::NAV_ITEM_TEXT} = $navigation_record->navigation_text;
					$navigation_entry->{self::NAV_ITEM_NAV_ORDER} = $navigation_record->navigation_order;
					$navigation_entry->{self::NAV_ITEM_URL} = $page_urls.(self::get_page_slug_by_id($navigation_record->navigation_object_id));
					
					if(!$as_array)
						$navigation_items[] = $navigation_entry;
					else
						$navigation_items[] = (array) $navigation_entry;
				}
			}
			else if($navigation_record->navigation_type == self::NAV_MODULE)
			{
				$extension_folder = self::get_extension_folder_by_id($navigation_record->navigation_object_id);
				
				$extension_url = Uri::base()."$extension_folder/$extension_folder";
				
				if(CMSInit::is_extension_installed($extension_folder))
				{
					$nav_url = Config::get("nav.".$extension_folder.".short_route", null);
					
					if($nav_url != null)
						$extension_url = Uri::base().$nav_url;
					
					if(in_array($extension_folder, $short_urls))
					{
						$extension_url = Uri::base().rtrim($short_urls[$extension_folder], "/")."/";
					}
				}
				
				$navigation_entry = new stdClass;
				$navigation_entry->{self::NAV_ITEM_ID} = $navigation_record->navigation_id;
				$navigation_entry->{self::NAV_ITEM_OBJECT_ID} = $navigation_record->navigation_object_id;
				$navigation_entry->{self::NAV_ITEM_TYPE} = $navigation_record->navigation_type;
				$navigation_entry->{self::NAV_ITEM_TEXT} = $navigation_record->navigation_text;
				$navigation_entry->{self::NAV_ITEM_NAV_ORDER} = $navigation_record->navigation_order;
				$navigation_entry->{self::NAV_ITEM_URL} = $extension_url;
				
				if(!$as_array)
					$navigation_items[] = $navigation_entry;
				else
					$navigation_items[] = (array) $navigation_entry;
			}
		}

		return $navigation_items;
	}

	/**
	 * Gets the landing page slug
	 * 
	 * @return page_id
	 */

	public static function get_landing_page()
	{
		$navigation_records = DB::select("*")->from(self::TABLE_NAVIGATION)->order_by('navigation_order', 'asc')
			->where("navigation_type", "=", self::NAV_PAGE)
			->limit(1)
			->as_object()->execute();
		

		if(count($navigation_records) > 0)
			return $navigation_records[0]->navigation_object_id;	
	
		return null;	
	}

	/**
	 * Saves an item to the navigation table
	 * 
	 * @param $navigation_text
	 * @param $navigation_type
	 * @param $object_id
	 * @param $after_navigation_id = 0
	 */
	
	public static function save_navigation($navigation_text, $navigation_type, $object_id, $after_navigation_id = 0,
		$before_navigation_id = 0)
	{
		$navigation_order = 0;
		
		if(($navigation_type != self::NAV_MODULE) && ($navigation_type != self::NAV_PAGE))
			throw new Exception_Navigation("Invalid navigation type supplied");
		
		if($after_navigation_id == 0 && $before_navigation_id == 0)
		{
			$nav_record = DB::select(DB::expr('MAX(navigation_order) AS nav_order'))->from(self::TABLE_NAVIGATION)
				->as_object()->execute();
			$navigation_order = intval($nav_record[0]->nav_order);
			
			self::create_navigation_item($navigation_text, $navigation_type, $object_id,($navigation_order + 1));
		}
		else if($after_navigation_id > 0)
		{
			$nav_record = self::get_navigation_item($after_navigation_id);
			
			if(count($nav_record) < 1)
				throw new Exception_Navigation("Navigation record does not exist");
			
			$navigation_order = intval($nav_record[0]->navigation_order);			
			
			$update_order_query = "UPDATE ".DB::table_prefix(self::TABLE_NAVIGATION)." SET navigation_order = (navigation_order + 1) ";
			$update_order_query.= "WHERE navigation_order > $navigation_order";
			
			$num_rows = DB::query($update_order_query)->execute();
			
			self::create_navigation_item($navigation_text, $navigation_type, $object_id, ($navigation_order + 1));
		}
		else if($before_navigation_id > 0)
		{
			$nav_record = self::get_navigation_item($before_navigation_id);
			
			if(count($nav_record) < 1)
				throw new Exception_Navigation("Navigation record does not exist");
			
			$navigation_order = intval($nav_record[0]->navigation_order);			
			
			$update_order_query = "UPDATE ".DB::table_prefix(self::TABLE_NAVIGATION)." SET navigation_order = (navigation_order + 1) ";
			$update_order_query.= "WHERE navigation_order >= $navigation_order";
			
			$num_rows = DB::query($update_order_query)->execute();
			
			self::create_navigation_item($navigation_text, $navigation_type, $object_id, ($navigation_order));
		}
	}
	
	/**
	 * Updates a navigation item
	 * 
	 * @param text
	 * @param type
	 * @param $object_id
	 * @param $navigation_id
	 * @return update_result
	 */
	
	public static function update_navigation($text, $type, $object_id, $navigation_id)
	{
		if(($type != self::NAV_MODULE) && ($type != self::NAV_PAGE))
			throw new Exception_Navigation("Invalid navigation type supplied");
		
		return DB::update(self::TABLE_NAVIGATION)->set(array(
			'navigation_text' => $text,
			'navigation_type' => $type,
			'navigation_object_id' => $object_id
		))
		->where('navigation_id', '=', $navigation_id)->execute();
	}
	
	/**
	 * Removes an item from the navigation
	 * 
	 * @param $navigation_id
	 */
	
	public static function remove_navigation($navigation_id)
	{
		$nav_record = self::get_all_navigation_items(array());
		
		if(count($nav_record) < 1)
				throw new Exception_Navigation("Navigation record does not exist");
		
		$navigation_order = $nav_record[0]->{self::NAV_ITEM_NAV_ORDER};
		$delete_result = DB::delete(self::TABLE_NAVIGATION)->where("navigation_id", "=", $navigation_id)->execute();
		
		$update_order_query = "UPDATE ".DB::table_prefix(self::TABLE_NAVIGATION)." SET navigation_order = (navigation_order - 1) ";
		$update_order_query.= "WHERE navigation_order > $navigation_order";
		
		$num_rows = DB::query($update_order_query)->execute();
	}
}
