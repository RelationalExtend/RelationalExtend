<?php
/**
 * Info class for the Media extension
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

namespace media;

class Media_Info extends \ExtensionInfo implements \IInfo {

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
        $this->build_extension_data("Media", "Implements the media module", $this->install_folder, "1.0");
        return array($this->extension_data);
    }
}
