<?php
/**
 * CMS Utilities
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class CMSUtil {
	/**
	 * Creates pagination for records
	 * 
	 * @param table
	 * @param query_object
	 * @param conditions = array()
	 * @param paged = true
	 * @param page_number = 1
	 * @param controller_path = ""
	 * @param action_name = ""
	 * @param page_size = 20
	 * 
	 * @return pagination_records
	 */
	 
    public static function create_pagination_records($table, &$query_object, $conditions = array(), $paged = true, $page_number = 1,
        $controller_path = "", $action_name = "", $page_size = 20)
    {
        $pagination_records = new stdClass();
        $pagination_records->{\Controller_Admin::PAGINATION_ENABLED} = $paged;
        $pagination_records->{\Controller_Admin::PAGINATION_CURRENT_PAGE} = $page_number;

        if($action_name != null)
            $pagination_records->{\Controller_Admin::PAGINATION_LINK} = Uri::base().$controller_path."$action_name/";
        else
            $pagination_records->{\Controller_Admin::PAGINATION_LINK} = Uri::base().$controller_path."index/";

        $pagination_records->{\Controller_Admin::PAGINATION_SIZE} = $page_size;

        // Paged result

        if($paged)
        {
            $num_pages_query = DB::select(DB::expr("COUNT(*) AS num_rows"))->from($table);
			
			if(is_array($conditions))
			{
				foreach($conditions as $condition => $value)
				{
					$num_pages_query = $num_pages_query->where($condition, "=", $value);
				}
			}

            $num_pages_query = $num_pages_query->as_object()->execute();


            $num_pages = ceil(($num_pages_query[0]->num_rows) / $page_size);

            $query_object->limit($page_size);
            $query_object->offset(($page_size * $page_number) - $page_size);

            $pagination_records->{\Controller_Admin::PAGINATION_NUM_PAGES} = $num_pages;
        }

        return $pagination_records;
    }

	/**
	 * Function to copy from source to destination all contents of a folder
	 * 
	 * @param source
	 * @param dest
	 */

	public static function xcopy($source, $dest, $permissions = 0755)
	{
	    // Check for symlinks
	    if (is_link($source)) {
	        return symlink(readlink($source), $dest);
	    }
	
	    // Simple copy for a file
	    if (is_file($source)) {
	        return copy($source, $dest);
	    }
	
	    // Make destination directory
	    if (!is_dir($dest)) {
	        mkdir($dest, $permissions);
	    }
	
	    // Loop through the folder
	    $dir = dir($source);
	    while (false !== $entry = $dir->read()) {
	        // Skip pointers
	        if ($entry == '.' || $entry == '..') {
	            continue;
	        }
	
	        // Deep copy directories
	        self::xcopy("$source/$entry", "$dest/$entry");
	    }
	
	    // Clean up
	    $dir->close();
	    return true;
	}
}
