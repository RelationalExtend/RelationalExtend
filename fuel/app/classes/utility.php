<?php

/**
 * Utility class
 *
 * @author     Ahmed Maawy
 * @copyright  2011 - 2012 Ahmed Maawy
 */
 
class Utility {
    /**
     * Create a slug out of a string
     *
     * @static
     * @param $string
     * @param string $slug_space_char
     * @return mixed
     */
    public static function slugify($string, $slug_space_char = "_")
    {
        $lower_case = strtolower($string);
        return str_replace(" ", $slug_space_char, $lower_case);
    }
}