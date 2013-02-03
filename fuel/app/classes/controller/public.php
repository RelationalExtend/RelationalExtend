<?php
/**
 * The main public controller. Will be used to render the theme
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Controller_Public extends Controller {

    /**
     * Initialize the controller
     *
     * @return void
     */
    public function before()
    {
        CMSInit::init_componenets();
        
        parent::before();
    }

    /**
     * Renders a processed Lex document
     *
     * @param $layout_slug
     * @param array $parameters
     * @return html_content
     */
    protected function render_layout($layout_slug, $parameters = array())
    {
        return Response::forge(Extension::execute_extensions($parameters, $layout_slug));
    }
}
