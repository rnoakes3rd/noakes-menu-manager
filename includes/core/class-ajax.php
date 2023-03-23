<?php
/*!
 * AJAX functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage AJAX
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the AJAX functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager_AJAX extends Noakes_Menu_Manager_Wrapper
{
	/**
	 * Constructor function.
	 *
	 * @since 3.0.2 Changed AJAX check.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		if ($this->base->cache->doing_ajax)
		{
			add_action('wp_ajax_' . Noakes_Menu_Manager_Constants::HOOK_RESET_GENERATOR, array($this, 'reset_generator'));
			add_action('wp_ajax_' . Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS, array($this, 'save_settings'));
		}
		else
		{
			$query_arg = '';
			
			if (isset($_GET[Noakes_Menu_Manager_Constants::HOOK_RESET_GENERATOR]))
			{
				$query_arg = Noakes_Menu_Manager_Constants::HOOK_RESET_GENERATOR;
				
				Noakes_Menu_Manager_Noatice::add_success(__('Generator reset successfully.', 'noakes-menu-manager'));
			}
			else if (isset($_GET[Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS]))
			{
				$query_arg = Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS;
				
				Noakes_Menu_Manager_Noatice::add_success(__('Settings saved successfully.', 'noakes-menu-manager'));
			}
			
			if (!empty($query_arg))
			{
				$this->base->cache->push('remove_query_args', $query_arg);
			}
		}
	}
	
	/**
	 * Reset the generator settings.
	 *
	 * @since 3.2.2 Improved query argument.
	 * @since 3.1.0 Improved structure.
	 * @since 3.0.3 Added capability check and additional data validation.
	 * @since 3.0.2 Removed escape from response URL.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function reset_generator()
	{
		if ($this->_invalid_submission(Noakes_Menu_Manager_Constants::HOOK_RESET_GENERATOR))
		{
			$this->_send_error(__('You are not authorized to reset the generator.', 'noakes-menu-manager'), 403);
		}
		else if ($this->_invalid_redirect())
		{
			$this->_send_error(__('Generator could not be reset.', 'noakes-menu-manager'));
		}
		
		update_option(Noakes_Menu_Manager_Constants::OPTION_GENERATOR, array());
		
		wp_send_json_success(array
		(
			'url' => add_query_arg
			(
				array
				(
					'page' => sanitize_key($_POST['option-name']),
					Noakes_Menu_Manager_Constants::HOOK_RESET_GENERATOR => 1
				),
				
				admin_url(sanitize_text_field($_POST['admin-page']))
			)
		));
	}
	
	/**
	 * Save the plugin settings.
	 *
	 * @since 3.2.2 Improved query argument.
	 * @since 3.2.0 Added data structure validation.
	 * @since 3.1.0 Improved structure.
	 * @since 3.0.3 Added capability check and additional data validation.
	 * @since 3.0.2 Removed escape from response URL.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function save_settings()
	{
		if ($this->_invalid_submission(Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS))
		{
			$this->_send_error(__('You are not authorized to save settings.', 'noakes-menu-manager'), 403);
		}
		else if ($this->_invalid_redirect())
		{
			$this->_send_error(__('Settings could not be saved.', 'noakes-menu-manager'));
		}
		
		$option_name = sanitize_key($_POST['option-name']);
		
		if ($option_name === Noakes_Menu_Manager_Constants::OPTION_GENERATOR)
		{
			$this->base->generator->prepare_meta_boxes();
		}
		else
		{
			$this->base->settings->prepare_meta_boxes();
		}
		
		update_option($option_name, Noakes_Menu_Manager_Sanitization::sanitize
		(
			/**
			 * Validate the data for the current form.
			 *
			 * @since 3.2.0
			 *
			 * @param array $valid_data Validated data.
			 */
			apply_filters(Noakes_Menu_Manager_Constants::HOOK_VALIDATE_DATA, array())
		));
		
		wp_send_json_success(array
		(
			'url' => add_query_arg
			(
				array
				(
					'page' => $option_name,
					Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS => 1
				),
				
				admin_url(sanitize_text_field($_POST['admin-page']))
			)
		));
	}
	
	/**
	 * Check for invalid redirect data.
	 *
	 * @since 3.1.0
	 *
	 * @access private
	 * @return boolean True if the required redirect data is missing.
	 */
	private function _invalid_redirect()
	{
		return
		(
			!isset($_POST['admin-page'])
			||
			empty($_POST['admin-page'])
			||
			!isset($_POST['option-name'])
			||
			empty($_POST['option-name'])
		);
	}
	
	/**
	 * Check for an invalid submission.
	 *
	 * @since 3.1.0
	 *
	 * @access private
	 * @param  string $action     AJAX action to verify the nonce for.
	 * @param  string $capability User capability required to complete the submission.
	 * @return boolean            True if the submission is invalid.
	 */
	private function _invalid_submission($action, $capability = 'manage_options')
	{
		return
		(
			!check_ajax_referer($action, false, false)
			||
			(
				!empty($capability)
				&&
				!current_user_can($capability)
			)
		);
	}
	
	/**
	 * Send a general error message.
	 * 
	 * @since 3.1.0 Added status code argument.
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  string  $message     Message displayed in the error noatice.
	 * @param  integer $status_code HTTP status code to send with the error.
	 * @return void
	 */
	private function _send_error($message, $status_code = null)
	{
		wp_send_json_error
		(
			array
			(
				'noatice' => Noakes_Menu_Manager_Noatice::generate_error($message)
			),
			
			$status_code
		);
	}
}
