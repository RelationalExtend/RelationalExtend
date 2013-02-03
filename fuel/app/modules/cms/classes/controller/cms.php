<?php
/**
 * CMS
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace cms;

class Controller_CMS extends \Controller_Admin {

    protected $controller_path = "cms/cms/";
    protected $default_admin_extension_path = "cms/cms/";
    
    protected $is_abstract_controller = false;

    public function before()
    {
        parent::before();
    }
}
