<?php
/**
 * Info class for the Pages extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace page;

class Page_Info extends \ExtensionInfo implements \IInfo {

    private $install_folder;

    /**
     * Constructor
     */

    const PERMISSION_CREATE_PAGE = "Create page";
	const PERMISSION_EDIT_PAGE = "Edit page";
	const PERMISSION_DELETE_PAGE = "Delete page";
     
    public function __construct()
    {
        $this->install_folder = $this->get_base_folder(__DIR__);
        parent::__construct();
    }

    /**
     * Supply meta data
     *
     * @return array
     */

   	public function info()
    {
    	// Extension as well as permission data
    	
        $this->build_extension_data("Page", "Implements the page module", $this->install_folder, "1.0",
			array(
					self::PERMISSION_CREATE_PAGE,  
					self::PERMISSION_EDIT_PAGE, 
					self::PERMISSION_DELETE_PAGE
				)
			);
        return array($this->extension_data);
    }
}
