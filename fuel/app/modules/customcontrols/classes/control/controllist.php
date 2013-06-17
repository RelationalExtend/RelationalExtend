<?php

namespace customcontrols;

class Control_controllist
{
	// Member variables
	private $control_name;
	private $control_values;
	
	/**
	 * Constructor
	 * 
	 * @param $object_meta_data
	 */
	 
	public function __construct($control_name, $control_values)
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
			"control_name" => "Control list",
			"control_desctiption" => "List all controls",
		);
	}
	
	/**
	 * Get the control's value
	 * 
	 * @return value
	 */
	
	public function control_value()
	{
		return Input::post($this->control_name, null);
	}
	
	/**
	 * Render the control
	 * 
	 * @return string
	 */
	
	public function render_control()
	{
		$core_controls = \DBFieldMeta::CONTROL_ARRAY;
		$custom_controls = CustomControls::get_custom_controls();
		$list_values = array();
		
		foreach($core_controls as $core_control_key => $core_control_value)
		{
			$list_values[$core_control_key] = $core_control_value;
		}
		
		foreach($custom_controls as $custom_control_key => $custom_control_value)
		{
			$list_values[$custom_control_key] = $custom_control_value["control_name"];
		}
		
		$control_output = "<select name='".$this->control_name."'>";
		
		foreach($list_values as $list_value_key => $list_value_value)
		{
			$control_output.="<option value='$list_value_key'>$list_value_value</option>";
		}
		
		$control_output.="</select>";
		
		return $control_output;
	}
	
	/**
	 * An array of custom control parameters
	 * 
	 * @return param
	 */
	
	public function custom_control_parameters()
	{
		return array();
	}
}