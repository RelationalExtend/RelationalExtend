<?php
/**
 * Code view management
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class ObjectModel_CodeView
{
	const LAYOUT_VIEW = "layout_view";
	const PARTIAL_VIEW = "partial_view";
	const JS_VIEW = "js_view";
	const CSS_VIEW = "css_view";
	
	private $code_language;
	
	/**
	 * Constructor
	 * 
	 * @param language
	 */
	
	public function ObjectModel_CodeView($language)
	{
		switch($language)
		{
			case self::LAYOUT_VIEW:
			case self::PARTIAL_VIEW:
				$this->code_language = "xml";
				break;
			case self::JS_VIEW:
				$this->code_language = "javascript";
				break;
			case self::CSS_VIEW:
				$this->code_language = "css";
				break;
		}
	}
	
	/**
	 * Gets the code language to use
	 * 
	 * @return code_language
	 */
	
	public function get_code_language()
	{
		return $this->code_language;
	}
} 