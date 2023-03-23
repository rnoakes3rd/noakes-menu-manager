<?php
/*!
 * Plugin plugins functions.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Plugins
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement plugins functions.
 *
 * @since 3.0.0
 */
final class Noakes_Menu_Manager_Plugins
{
	/**
	 * Get the data for a plugin.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public static
	 * @param  string $base_name Base name to the plugin to get the data for.
	 * @return string            Plugin data if it's found.
	 */
	public static function get_data($base_name)
	{
		self::_load();

		return (empty($base_name))
		? ''
		: get_plugin_data(WP_PLUGIN_DIR . '/' . $base_name);
	}
	
	/**
	 * Check to see if a plugin is active.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  string  $base_name Base name for the plugin to check.
	 * @return boolean            True if the plugin is active.
	 */
	public static function is_active($base_name)
	{
		self::_load();

		return
		(
			file_exists(WP_PLUGIN_DIR . '/' . $base_name)
			&&
			is_plugin_active($base_name)
		);
	}
	
	/**
	 * Load plugin functionality if necessary.
	 *
	 * @since 3.0.0
	 *
	 * @access private static
	 * @return void
	 */
	private static function _load()
	{
		if (!function_exists('is_plugin_active'))
		{
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}
	}
}
