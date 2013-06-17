<?php

namespace customcontrols;

class CustomControls
{
	// Member variables
	
	private $control_name;
	private $control_values;
	private $control_type;
	
	/**
	 * Consructor
	 */
	
	public function __construct($control_name = null, $control_values = null)
	{	
		if($control_values != null)
		{
			$this->values_array = explode($control_values, "|");
			
			if(is_array($control_values))
				$this->control_type = $this->values_array[0];
		}
	}
	
	/**
	 * Draws the control
	 * 
	 * @return string to render
	 */
	
	public function render_control()
	{
		$control_class_name = "Control_".$this->control_type;
		$control_object = new $control_class_name($this->control_name, $this->values_array);
		
		return $control_object->render_control();
	}
	
	/**
	 * Returns the value of the control
	 * 
	 * @return the control's set value
	 */
	
	public function control_value()
	{
		$control_class_name = "Control_".$this->control_type;
		$control_object = new $control_class_name($this->control_name, $this->values_array);
		
		return $control_object->control_value();
	}
	
	/**
	 * Gets a list of all custom controls
	 * 
	 * @return array of controls
	 */
	
	public static function get_custom_controls()
	{
		$directory = rtrim(__DIR__, "/")."/control";
		$files = scandir($directory);
		
		$control_info = array();
		
		foreach($files as $file)
		{
			if($file != "." && $file != "..")
			{
				if(strripos($file, ".php", -4))
				{
					$current_control_id = str_replace(".php", "", $file);
					$class_name = "\\customcontrol\\Control_".str_replace(".php", "", $file);
					$object = new $class_name(null);
					$control_info[$current_control_id] = $object->info();
				}
			}
		}
		
		return $control_info;
	}
}
