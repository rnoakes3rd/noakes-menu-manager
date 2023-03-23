<?php
/*!
 * Plugin utility functions.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Utilities
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement utility functions.
 *
 * @since 3.0.0
 */
final class Noakes_Menu_Manager_Utilities
{
	/**
	 * Check a value to see if it is an array or convert to an array if necessary.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  mixed $value        Value to turn into an array.
	 * @param  mixed $return_empty True if an empty value should be returned as-is.
	 * @return mixed               Checked value as an array.
	 */
	public static function check_array($value, $return_empty = false)
	{
		$is_empty = empty($value);
		
		if
		(
			$is_empty
			&&
			$return_empty
		)
		{
			return $value;
		}

		if ($is_empty)
		{
			$value = array();
		}

		if (!is_array($value))
		{
			$value = array($value);
		}

		return $value;
	}

	/**
	 * Remove comments, line breaks and tabs from provided code.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  string $code Raw code to clean up.
	 * @return string       Code without comments, line breaks and tabs.
	 */
	public static function clean_code($code)
	{
		$code = preg_replace('/<!--(.*)-->/Uis', '', $code);
		$code = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $code);

		return str_replace(array(PHP_EOL, "\r", "\n", "\t"), '', $code);
	}
	
	/**
	 * Check to see if a full string end with a specified string.
	 * 
	 * @since 3.0.2 Improved condition.
	 * @since 3.0.0
	 * 
	 * @access public static
	 * @param  string  $needle   String to check for.
	 * @param  string  $haystack Full string to check.
	 * @return boolean           True if the full string ends with the specified string.
	 */
	public static function ends_with($needle, $haystack)
	{
		$length = strlen($needle);
		
		if ($length === 0)
		{
			return true;
		}
		
		return (substr($haystack, -$length) === $needle);
	}
	
	/**
	 * Check to see if a variable is a valid field object.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  mixed   $variable Variable to check.
	 * @return boolean           True if the variable is a valid field object.
	 */
	public static function is_field($variable)
	{
		return
		(
			is_object($variable)
			&&
			self::starts_with('Noakes_Menu_Manager_Field', get_class($variable))
			&&
			!is_a($variable, 'Noakes_Menu_Manager_Field_Tab')
		);
	}

	/**
	 * Load and decode JSON from a provided file path.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  string $file_path   Path to the JSON file.
	 * @param  string $plugin_path Path for the current plugin.
	 * @return string              Decoded JSON file.
	 */
	public static function load_json($file_path, $plugin_path = '')
	{
		if (empty($plugin_path))
		{
			$plugin_path = Noakes_Menu_Manager()->plugin;
		}
		
		$file = plugin_dir_path($plugin_path) . $file_path;

		if (!file_exists($file))
		{
			return '';
		}

		ob_start();

		require($file);

		return json_decode(ob_get_clean(), true);
	}
	
	/**
	 * Removes leading and trailing redundancies from a custom field value.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public static
	 * @param  string $value Raw value to check for redundancies.
	 * @return string        Modified value without redundancies.
	 */
	public static function remove_redundancies($value)
	{
		return trim($value, "?&# \t\n\r\0\x0B");
	}
	
	/**
	 * Sanitize CSS classes.
	 * 
	 * @since 3.1.0
	 * 
	 * @access public static
	 * @param  string $value Raw value to sanitize.
	 * @return string        Sanitized CSS class(es).
	 */
	public static function sanitize_classes($value)
	{
		$classes = explode(' ', preg_replace('/\s\s+/', ' ', trim($value)));
		$class_count = count($classes);

		for ($i = 0; $i < $class_count; $i++)
		{
			$classes[$i] = sanitize_html_class($classes[$i]);
		}

		return implode(' ', array_filter($classes));
	}
	
	/**
	 * Check to see if a full string starts with a specified string.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public static
	 * @param  string  $needle   String to check for.
	 * @param  string  $haystack Full string to check.
	 * @return boolean           True if the full string starts with the specified string.
	 */
	public static function starts_with($needle, $haystack)
	{
		return
		(
			empty($needle)
			||
			strpos($haystack, $needle) === 0
		);
	}
}
