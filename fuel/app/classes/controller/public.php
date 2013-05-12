<?php
/**
 * The main public controller. Will be used to render the theme
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Controller_Public extends Controller {

    protected $controller_path = "public/";

    // Protected variables

    protected $div_container_class = "";
    protected $div_content_class = "";
    protected $div_form_class = "";

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

    /**
     * Preset form field values
     *
     * @return array
     */

    protected function preset_form_fields()
    {
        // To be overridden
        return array();
    }

    /**
     * Value for calculated form fields
     *
     * @param $field_name
     * @param $value_sets
     * @return null
     */

    protected function special_field_operation($field_name, $value_sets)
    {
        // To be overridden
        return null;
    }

    /**
     * Renders a form to the frontend
     *
     * @param $page_title
     * @param $page_content
     * @param $table_slug
     * @param $form_action
     * @param int $record_id
     * @param string $return_path
     *
     * @return view contents
     */

    protected function build_frontend_ui_form_view($page_title, $page_content, $table_slug, $form_action,
        $record_id = 0, $return_path = "")
    {
        // Record view data here

        $form_view = new ObjectModel_FormView($table_slug, $record_id, $return_path);
        $form_view->page_title = $page_title;
        $form_view->page_content = $page_content;
        $form_view->form_action = $form_action;
        $form_view->preset_form_fields = $this->preset_form_fields();
        $form_view->div_container_class = $this->div_container_class;
        $form_view->div_content_class = $this->div_content_class;
        $form_view->div_form_class = $this->div_form_class;

        $view = View::forge("frontend/partials/record-view", array("page_rows" => $form_view->get_object_meta_data(),
                    "record_id" => $record_id, "page_title" => $page_title, "page_title_content" => $page_content,
                    "form_action" => Uri::base().$this->controller_path.$form_view->form_action, "object" => $table_slug,
                    "record" => $form_view->get_records(), "form_values" => $form_view->preset_form_fields,
                    "div_form_class" => $form_view->div_form_class, "div_content_class" => $form_view->div_content_class,
                    "div_container_class" => $form_view->div_container_class, "return_path" => $form_view->return_path));

        return $view;
    }

	/**
	 * Gets a setting for an extension
	 * 
	 * @param $extension_slug
	 * @param $extension_setting_slug
	 * @return setting
	 */

	protected function get_extension_setting($extension_slug, $extension_setting_slug)
	{
		$extension = Extension::get_installed_extension_meta_data_by_slug($extension_slug);
		
		if(count($extension) < 1)
			throw new Exception_Extension("Extension $extension_slug does not exist");
		
		$extension_id = $extension[0]->extension_id;
		
		$setting = Extension::get_extension_settings($extension_id, $extension_setting_slug);
		
		if(count($setting) < 1)
			throw new Exception_Extension("Extension setting $extension_setting_slug does not exist");
			
		return $setting[0]->value;
	}
	
	/**
	 * Gets a setting for the site
	 * 
	 * @param $site_setting_slug
	 * @return setting
	 */
	
	protected function get_site_setting($site_setting_slug)
	{
		$setting = Extension::get_extension_settings(0, $site_setting_slug);
		
		if(count($setting) < 1)
			throw new Exception_Extension("Site setting $site_setting_slug does not exist");
			
		return $setting[0]->value;
	}
}
