<?php
/*!
 * Global plugin hooks.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Global
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement global hooks.
 *
 * @since 3.0.0
 */
final class Noakes_Menu_Manager_Global
{
	/**
	 * Version for the jQuery Validate plugin.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	const JQUERY_VALIDATE_VERSION = '1.19.3';
	
	/**
	 * Enqueue plugin assets.
	 *
	 * @since 3.1.0 Added AJAX script options.
	 * @since 3.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function admin_enqueue_scripts()
	{
		wp_deregister_script('jquery-validation');
		
		wp_dequeue_script('jquery-validation');
		
		$nmm = Noakes_Menu_Manager();
		$full_vendor_path = plugins_url('/assets/vendor/', $nmm->plugin);
		
		$asset_suffix = ($nmm->cache->script_debug)
		? ''
		: '.min';
		
		wp_enqueue_script('jquery-validation', $full_vendor_path . 'jquery-validation/jquery.validate' . $asset_suffix . '.js', array(), self::JQUERY_VALIDATE_VERSION, true);
		
		$home_url = home_url();
		$vendor_path = str_replace($home_url, '', $full_vendor_path);
		$locale = get_locale();
		$locale_split = explode('_', $locale);
		
		$jquery_validation_path = $vendor_path . 'jquery-validation/localization/';
		$jquery_validation_messages_file = $jquery_validation_path . 'messages_' . $locale . '.min.js';
		$jquery_validation_messages_file_simple = $jquery_validation_path . 'messages_' . $locale_split[0] . '.min.js';

		if (file_exists(ABSPATH . $jquery_validation_messages_file))
		{
			wp_enqueue_script('jquery-validation-localization-messages', $home_url . $jquery_validation_messages_file, array(), self::JQUERY_VALIDATE_VERSION, true);
		}
		else if (file_exists(ABSPATH . $jquery_validation_messages_file_simple))
		{
			wp_enqueue_script('jquery-validation-localization-messages', $home_url . $jquery_validation_messages_file_simple, array(), self::JQUERY_VALIDATE_VERSION, true);
		}

		$jquery_validation_methods_file = $jquery_validation_path . 'methods_' . $locale . '.min.js';
		$jquery_validation_methods_file_simple = $jquery_validation_path . 'methods_' . $locale_split[0] . '.min.js';

		if (file_exists(ABSPATH . $jquery_validation_methods_file))
		{
			wp_enqueue_script('jquery-validation-localization-methods', $home_url . $jquery_validation_methods_file, array(), self::JQUERY_VALIDATE_VERSION, true);
		}
		else if (file_exists(ABSPATH . $jquery_validation_methods_file_simple))
		{
			wp_enqueue_script('jquery-validation-localization-methods', $home_url . $jquery_validation_methods_file_simple, array(), self::JQUERY_VALIDATE_VERSION, true);
		}
		
		wp_enqueue_style('noatice', $nmm->cache->asset_path('styles', 'noatice.css'), array(), Noakes_Menu_Manager_Constants::VERSION);
		wp_enqueue_style('nmm-style', $nmm->cache->asset_path('styles', 'style.css'), array('dashicons', 'noatice'), Noakes_Menu_Manager_Constants::VERSION);
		
		wp_enqueue_script('noatice', $nmm->cache->asset_path('scripts', 'noatice.js'), array(), Noakes_Menu_Manager_Constants::VERSION, true);
		wp_enqueue_script('nmm-script', $nmm->cache->asset_path('scripts', 'script.js'), array('jquery-ui-sortable', 'jquery-validation', 'noatice', 'postbox', 'wp-util'), Noakes_Menu_Manager_Constants::VERSION, true);
		
		wp_localize_script
		(
			'nmm-script',
			'nmm_script_options',

			array
			(
				'admin_page' => $nmm->cache->admin_page,
				'code_nav' => Noakes_Menu_Manager_Constants::CODE_NAV,
				'component_id' => Noakes_Menu_Manager_Constants::COMPONENT_ID,
				'noatices' => Noakes_Menu_Manager_Noatice::output(),
				'option_name' => $nmm->cache->option_name,
				'token' => Noakes_Menu_Manager_Constants::TOKEN,
				'validation' => $nmm->cache->form_validation,

				'strings' => array
				(
					'save_alert' => __('The changes you made will be lost if you navigate away from this page.', 'noakes-menu-manager'),
					'validation_error' => __('Please correct the validation error(s) and try again.', 'noakes-menu-manager')
				),
				
				'urls' => array
				(
					'ajax' => admin_url('admin-ajax.php'),
					'current' => remove_query_arg($nmm->cache->get_remove_query_args())
				)
			)
		);
	}

	/**
	 * Include the HTML templates in the admin footer.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @return void
	 */
	public static function admin_footer_templates()
	{
		ob_start();

		$templates_path = dirname(__FILE__) . '/../templates/';

		require($templates_path . 'repeatable-buttons.php');

		echo Noakes_Menu_Manager_Utilities::clean_code(ob_get_clean());
	}
}
