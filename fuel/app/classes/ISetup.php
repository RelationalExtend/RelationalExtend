<?php
/**
 * Interface specifications for a setup class
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

interface ISetup {
    /**
     * Logic will be used to setup an extension
     * 
     * @abstract
     * @return void
     */
    public function install_extension();

    /**
     * Logic will be used to uninstall an extension
     *
     * @abstract
     * @param $extension_id
     * @return void
     */
    public function uninstall_extension($extension_id);
}
