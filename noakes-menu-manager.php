<?php
/*!
 * Plugin Name: Nav Menu Manager
 * Plugin URI:  https://wordpress.org/plugins/noakes-menu-manager/
 * Description: Simplifies nav menu maintenance and functionality providing more control over nav menus with less coding.
 * Version:     3.2.3
 * Author:      Robert Noakes
 * Author URI:  https://robertnoakes.com/
 * Text Domain: noakes-menu-manager
 * Domain Path: /languages/
 * Copyright:   (c) 2016-2023 Robert Noakes (mr@robertnoakes.com)
 * License:     GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
 
/**
 * Main plugin file.
 * 
 * @since 3.2.2 Removed PHP_INT_MAX fallback.
 * @since 3.2.0 Added fallback for PHP_INT_MAX.
 * @since 3.0.0
 * 
 * @package Nav Menu Manager
 */
 
if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Setup autoloading for plugin classes.
 *
 * @since 3.0.2 Improved conditions.
 * @since 3.0.0
 */
spl_autoload_register(function ($class)
{
	$base_class = 'Noakes_Menu_Manager';
	$includes_path = dirname(__FILE__) . '/includes/';

	if (strpos($class, $base_class) === 0)
	{
		$core_path = $includes_path . 'core/class-';
		$static_path = $includes_path . 'static/class-';
		$standalone_path = $includes_path . 'standalone/class-';
		$fields_path = $includes_path . 'fields/class-';
		$plugins_path = $includes_path . 'plugins/class-';

		$file_name = ($class === $base_class)
		? 'base'
		: strtolower(str_replace(array($base_class . '_', '_'), array('', '-'), $class));

		$file_name .= '.php';

		if (file_exists($core_path . $file_name))
		{
			require_once($core_path . $file_name);
		}
		else if (file_exists($static_path . $file_name))
		{
			require_once($static_path . $file_name);
		}
		else if (file_exists($standalone_path . $file_name))
		{
			require_once($standalone_path . $file_name);
		}
		else if (file_exists($fields_path . $file_name))
		{
			require_once($fields_path . $file_name);
		}
		else if (file_exists($plugins_path . $file_name))
		{
			require_once($plugins_path . $file_name);
		}
	}
	else if ($class === 'Noakes_Menu_Widget')
	{
		require_once($includes_path . 'standalone/class-noakes-menu-widget.php');
	}
	else if ($class === 'WP_Screen')
	{
		require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
	}
});

/**
 * Returns the main instance of Noakes_Menu_Manager.
 *
 * @since 3.0.0
 *
 * @param  string          $file Optional main plugin file name.
 * @return Noakes_Menu_Manager       Main Noakes_Menu_Manager instance.
 */
function Noakes_Menu_Manager($file = '')
{
	return Noakes_Menu_Manager::_get_instance($file);
}

Noakes_Menu_Manager(__FILE__);
