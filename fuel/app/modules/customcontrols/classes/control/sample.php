<?php

namespace customcontrols;

class Control_sample
{
	// Member variables
	private $control_name;
	private $control_values;
	
	/**
	 * Constructor
	 * 
	 * @param $object_meta_data
	 */
	 
	public function __construct($control_name = null, $control_values = null)
	{
        $this->control_name = $control_name;
        $this->control_values = $control_values;
	}
	
	/**
	 * Info
	 * 
	 * @return array of info data
	 */
	
	public function info()
	{
        return array(
            "control_name" => "Sample control",
            "control_desctiption" => "Demonstrates custom controls",
        );
	}
	
	/**
	 * Get the control's value
	 * 
	 * @return value
	 */
	
	public function control_value()
	{
		return \Fuel\Core\Input::post($this->control_name, null);
	}
	
	/**
	 * Render the control
	 * 
	 * @return string
	 */
	
	public function render_control()
	{
		return 
			"
			<select name='".$this->control_name."'>
				<option value='1'>Item 1</option>
				<option value='2'>Item 2</option>
				<option value='3'>Item 3</option>
				<option value='4'>Item 4</option>
			</select>
			";
	}
    
    /**
     * Presets the control's value
     * 
     * @param type $values
     */

    public function preset_values($values)
    {
        if($values != null)
        {
            $this->preset_value = $values;
        }
    }
    
    /**
     * Gets the field type
     * 
     * @return string
     */
    
    public function control_field_type()
    {
        return "INT";
    }
	
	/**
	 * An array of custom control parameters
	 * 
	 * @return param
	 */
	
	public function custom_control_parameters()
	{
		$default_value_parameter = new CustomControlParameters();
		
		$default_value_parameter->parameter_control = \DBFieldMeta::CONTROL_LIST;
		$default_value_parameter->parameter_id = "default_value";
		$default_value_parameter->parameter_name = "Default value";
		$default_value_parameter->parameter_values = array(
			"1" => "Item 1", 
			"2" => "Item 2", 
			"3" => "Item 3", 
			"4" => "Item 4",
		);
		
		return array($default_value_parameter);
	}
}
