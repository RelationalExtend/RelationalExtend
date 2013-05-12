<?php
/**
 * Core navigation plugin
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Plugin_Navigation {
	
	/**
	 * Gets the navigation menu as an array
	 * 
	 * @param $attributes
     * @param $content
     * @return array
	 */
	
    public static function navigation($attributes, $content)
	{
		return Navigation::get_all_navigation_items(array(), true);
	}
}
