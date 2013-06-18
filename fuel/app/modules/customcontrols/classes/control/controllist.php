<?php

namespace customcontrols;

class Control_controllist
{
	// Member variables
	private $control_name;
	private $control_values;
        
        private $preset_value;
	
	/**
	 * Constructor
	 * 
	 * @param $object_meta_data
	 */
	 
	public function __construct($control_name = null, $control_values = null)
	{
		$this->control_name = $control_name;
		$this->control_values = $control_values;
                $this->preset_value = null;
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
		return \Fuel\Core\Input::post($this->control_name, null);
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
	 * Render the control
	 * 
	 * @return string
	 */
	
	public function render_control()
	{
		$core_controls = \DBFieldMeta::$CONTROL_ARRAY;
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
			$control_output.="<option value='$list_value_key'".($list_value_key == $this->preset_value ? " selected" : "").">$list_value_value</option>";
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