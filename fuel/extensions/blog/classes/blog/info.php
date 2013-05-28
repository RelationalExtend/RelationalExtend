<?php
/**
 * Info class for the Blog extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace blog;

class Blog_Info extends \ExtensionInfo implements \IInfo {

    private $install_folder;

    /**
     * Constructor
     */

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
        $this->build_extension_data("Blog", "Implements the blog module", $this->install_folder, "1.0");
        return array($this->extension_data);
    }
}
