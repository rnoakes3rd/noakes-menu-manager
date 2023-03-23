<?php
/*!
 * Plugin setup functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Setup
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the setup functionality.
 *
 * @since 3.0.0
 */
final class Noakes_Menu_Manager_Setup
{
	/**
	 * Check and update the plugin version.
	 *
	 * @since 3.2.1 Added previous version constant.
	 * @since 3.0.3 Improved version sanitization.
	 * @since 3.0.2 Improved condition and changed force previous version definition.
	 * @since 3.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function check_version()
	{
		$current_version =
		(
			!defined('NDT_FORCE_PREVIOUS_VERSION')
			||
			!NDT_FORCE_PREVIOUS_VERSION
		)
		? wp_unslash(get_option(Noakes_Menu_Manager_Constants::OPTION_VERSION))
		: Noakes_Menu_Manager_Constants::VERSION_PREVIOUS;

		if (empty($current_version))
		{
			add_option(Noakes_Menu_Manager_Constants::OPTION_VERSION, sanitize_text_field(Noakes_Menu_Manager_Constants::VERSION));
		}
		else if ($current_version !== Noakes_Menu_Manager_Constants::VERSION)
		{
			update_option(Noakes_Menu_Manager_Constants::OPTION_VERSION, sanitize_text_field(Noakes_Menu_Manager_Constants::VERSION));
			
			if (version_compare($current_version, '3.0.0', '<'))
			{
				$nmm = Noakes_Menu_Manager();
				
				self::_pre_three_zero_zero($nmm, $current_version);
				self::_pre_three_zero_zero_generator($nmm, $current_version);
			}
		}
	}
		
	/**
	 * Clean up settings for plugin versions earlier than 3.0.0.
	 * 
 	 * @since 3.1.0 Minor MySQL query cleanup.
	 * @since 3.0.3 Added option unslashing.
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  Noakes_Menu_Manager $nmm             Main plugin object.
	 * @param  string              $current_version Current plugin version.
	 * @return void
	 */
	private static function _pre_three_zero_zero($nmm, $current_version)
	{
		global $wpdb;
		
		$wpdb->query($wpdb->prepare
		(
			"UPDATE 
				$wpdb->postmeta 
			SET 
				meta_key = %s 
			WHERE 
				meta_key = %s;\n",
				
			Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_ID,
			Noakes_Menu_Manager_Constants::POST_META_PREFIX . 'noakes_id'
		));
		
		$wpdb->query($wpdb->prepare
		(
			"UPDATE 
				$wpdb->postmeta 
			SET 
				meta_key = %s 
			WHERE 
				meta_key = %s;\n",
				
			Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING,
			Noakes_Menu_Manager_Constants::POST_META_PREFIX . 'noakes_query_string'
		));
		
		$wpdb->query($wpdb->prepare
		(
			"UPDATE 
				$wpdb->postmeta 
			SET 
				meta_key = %s 
			WHERE 
				meta_key = %s;\n",
				
			Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_HASH,
			Noakes_Menu_Manager_Constants::POST_META_PREFIX . 'noakes_anchor'
		));
		
		$plugin_settings = Noakes_Menu_Manager_Utilities::check_array(wp_unslash(get_option(Noakes_Menu_Manager_Constants::OPTION_SETTINGS)));
		
		if (is_array($plugin_settings))
		{
			if (version_compare($current_version, '2.0.0', '<'))
			{
				$plugin_settings = self::_pre_two_zero_zero($current_version, $plugin_settings);
			}

			unset($plugin_settings['store_collapsed_states']);
			unset($plugin_settings['disable_help_buttons']);
			unset($plugin_settings['disable_help_tabs']);
			
			if (isset($plugin_settings['enable_anchor']))
			{
				$plugin_settings['enable_hash'] = $plugin_settings['enable_anchor'];
				
				unset($plugin_settings['enable_anchor']);
			}

			update_option(Noakes_Menu_Manager_Constants::OPTION_SETTINGS, $plugin_settings);

			$nmm->settings->load_option($plugin_settings);
		}
	}
		
	/**
	 * Clean up settings for plugin versions earlier than 2.0.0.
	 * 
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  string $current_version Current plugin version.
	 * @param  array  $plugin_settings Loaded plugin settings.
	 * @return array                   Modified plugin settings.
	 */
	private static function _pre_two_zero_zero($current_version, $plugin_settings)
	{
		if (version_compare($current_version, '1.7.0', '<'))
		{
			$plugin_settings = self::_pre_one_seven_zero($current_version, $plugin_settings);
		}

		unset($plugin_settings['enable_collapse_expand']);
		unset($plugin_settings['preserve_options']);
		unset($plugin_settings['preserve_post_meta']);
		unset($plugin_settings['preserve_user_meta']);
		
		return $plugin_settings;
	}

	/**
	 * Clean up settings for plugin versions earlier than 1.7.0.
	 * 
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  string $current_version Current plugin version.
	 * @param  array  $plugin_settings Loaded plugin settings.
	 * @return array                   Modified plugin settings.
	 */
	private static function _pre_one_seven_zero($current_version, $plugin_settings)
	{
		if
		(
			isset($plugin_settings['enable_collapse_expand'])
			&&
			!empty($plugin_settings['enable_collapse_expand'])
		)
		{
			$plugin_settings['store_collapsed_states'] = '1';
		}
		
		return $plugin_settings;
	}
		
	/**
	 * Clean up generator settings for plugin versions earlier than 3.0.0.
	 * 
	 * @since 3.0.3 Added option unslashing.
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  Noakes_Menu_Manager $nmm             Main plugin object.
	 * @param  string              $current_version Current plugin version.
	 * @return void
	 */
	private static function _pre_three_zero_zero_generator($nmm, $current_version)
	{
		$generator_settings = Noakes_Menu_Manager_Utilities::check_array(wp_unslash(get_option(Noakes_Menu_Manager_Constants::OPTION_GENERATOR)));
		
		if (is_array($generator_settings))
		{
			if (version_compare($current_version, '2.0.0', '<'))
			{
				$generator_settings = self::_pre_two_zero_zero_generator($current_version, $generator_settings);
			}

			if
			(
				isset($generator_settings['echoed'])
				&&
				empty($generator_settings['echoed'])
			)
			{
				unset($generator_settings['echoed']);
			}

			if
			(
				isset($generator_settings['depth'])
				&&
				empty($generator_settings['depth'])
			)
			{
				unset($generator_settings['depth']);
			}
			
			update_option(Noakes_Menu_Manager_Constants::OPTION_GENERATOR, $generator_settings);
			
			if (!$nmm->settings->disable_generator)
			{
				$nmm->generator->load_option($generator_settings);
			}
		}
	}
		
	/**
	 * Clean up generator settings for plugin versions earlier than 2.0.0.
	 * 
	 * @since 3.0.2 Improved condition.
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  string $current_version    Current plugin version.
	 * @param  array  $generator_settings Loaded generator settings.
	 * @return array                      Modified generator settings.
	 */
	private static function _pre_two_zero_zero_generator($current_version, $generator_settings)
	{
		if (version_compare($current_version, '1.4.2', '<'))
		{
			$generator_settings = self::_pre_one_four_two_generator($current_version, $generator_settings);
		}

		if
		(
			isset($generator_settings['item_spacing'])
			&&
			$generator_settings['item_spacing'] === 'preserve'
		)
		{
			unset($generator_settings['item_spacing']);
		}

		if (!isset($generator_settings['echoed']))
		{
			$generator_settings['echoed'] = '';
		}
		
		return $generator_settings;
	}

	/**
	 * Clean up generator settings for plugin versions earlier than 1.4.2.
	 * 
	 * @since 3.0.2 Improved condition.
	 * @since 3.0.0
	 * 
	 * @access private static
	 * @param  string $current_version    Current plugin version.
	 * @param  array  $generator_settings Loaded generator settings.
	 * @return array                      Modified generator settings.
	 */
	private static function _pre_one_four_two_generator($current_version, $generator_settings)
	{
		$dropdown_fields = array('fallback_cb', 'walker', 'items_wrap');

		foreach ($generator_settings as $name => $value)
		{
			if
			(
				in_array($name, $dropdown_fields)
				&&
				$value === 'included'
			)
			{
				$generator_settings[$name] = Noakes_Menu_Manager_Constants::CODE_TRUE;
			}
		}
		
		return $generator_settings;
	}
}
