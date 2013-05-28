<?php

namespace CustomControls;

class CustomControls
{
	private $control_string;
	private $control_type;
	private $values_array;
	
	private $meta_object;
	private $meta_object_meta_data;
	private $meta_controller;
	
	/**
	 * Consructor
	 */
	
	public function __construct($control_name = null, $control_values = null)
	{
		$this->control_string = "";
		$this->control_type = "";
		
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
		$control_object = new $control_class_name($this->meta_object_meta_data->object_meta_slug);
		
		return $control_object->control_value($this->meta_object, $this->meta_controller);
	}
	
	public function set_meta_data($object, $object_meta_data, $controller)
	{
		$this->meta_object = $object;
		$this->meta_object_meta_data = $object_meta_data;
		$this->meta_controller = $controller;
	}
}
