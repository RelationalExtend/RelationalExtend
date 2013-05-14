<?php
/**
 * Core theme plugin
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */

class Plugin_Theme {
    /**
     * Get Javascript content
     *
     * @static
     * @param $attributes
     * @param $content
     * @return tag
     */
    public static function js($attributes, $content)
    {
        $slug = $attributes["slug"];
        
        return "<script type='text/javascript' src='".Uri::base()."segments/js/".$slug."'></script>";
    }

    /**
     * Get CSS content
     *
     * @static
     * @param $attributes
     * @param $content
     * @return tag
     */

    public static function css($attributes, $content)
    {
        $slug = $attributes["slug"];

        return "<link rel='stylesheet' href='".Uri::base()."segments/css/".$slug."'>";
    }
	
	/**
     * Get Theme Image
     *
     * @static
     * @param $attributes
     * @param $content
     * @return tag
     */
	
	public function img($attributes, $content)
	{
		$active_theme = CMSTheme::get_installed_default_theme();
		$theme_folder = $active_theme->theme_folder;
		$image_name = $attributes["name"];
		
		return "<img src='".DOCROOT."assets/theme-images/".$theme_folder."/".$image_name."'/>";
	}

    /**
     * Get partial content
     *
     * @static
     * @param $attributes
     * @param $content
     * @return content
     */

    public static function partial($attributes, $content)
    {
        $slug = $attributes["slug"];

        // Execute lex on the partial

        $partial_theme = CMSTheme::get_installed_partial_content($slug);

        $lex_parser = new LexParser();
        $lex_parser->scopeGlue(':');
		$lex_parser->cumulativeNoParse(TRUE);

        $cms_theme = new CMSTheme();

        $response_content = $lex_parser->parse($partial_theme, CMSTheme::$theme_data, array($cms_theme, 'parser_callback'));
        
        return $response_content;
    }
}
