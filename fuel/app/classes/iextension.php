<?php
/**
 * Interface specifications for an extension file
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

interface IExtension {
    /**
     * Logic to execute the extension before rendering a theme
     * @abstract
     * @return void
     */
    public function execute_extension();
}
