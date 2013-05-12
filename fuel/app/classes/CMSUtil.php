<?php
/**
 * CMS Utilities
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class CMSUtil {
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
}
