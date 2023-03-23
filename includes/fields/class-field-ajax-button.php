<?php
/*!
 * AJAX button field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage AJAX Button Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the AJAX button field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_AJAX_Button extends Noakes_Menu_Manager_Field
{
	/**
	 * Get a default value based on the provided name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 * @param  string $name Name of the value to return.
	 * @return mixed        Default value if it exists, otherwise an empty string.
	 */
	protected function _default($name)
	{
		switch ($name)
		{
			/**
			 * Action applied to AJAX buttons.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'action':
			
			/**
			 * Confirmation message for AJAX buttons.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'confirmation':
			
				return '';
				
			/**
			 * Label for the AJAX button.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'button_label':
			
				return __('Send', 'noakes-menu-manager');
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the AJAX button field.
	 *
	 * @since 3.0.3 Improved AJAX button.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the AJAX button field should be echoed.
	 * @return string        Generated AJAX button field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		if (!empty($this->action))
		{
			$output = '<div class="nmm-field-actions' . $this->_field_classes(false) . '">'
				. self::generate_button($this->button_label, $this->action, $this->value, $this->confirmation)
			. '</div>';
		}
		
		return parent::_output($output, 'ajax-button', $echo);
	}
	
	/**
	 * Generate an AJAX button.
	 *
	 * @since 3.1.0 Added large class to the button element.
	 * @since 3.0.3
	 *
	 * @access public static
	 * @param  string $button_label Label for the AJAX button.
	 * @param  string $action       Action to send with the AJAX request.
	 * @param  string $value        Optional value to pass with the AJAX request.
	 * @param  string $confirmation Optional confirmation message to display when the button is clicked.
	 * @return string               Generated AJAX button.
	 */
	public static function generate_button($button_label, $action, $value = '', $confirmation = '')
	{
		if (!empty($value))
		{
			$value = ' data-nmm-ajax-value="' . esc_attr($value) . '"';
		}

		if (!empty($confirmation))
		{
			$confirmation = ' data-nmm-ajax-confirmation="' . esc_attr($confirmation) . '"';
		}

		return '<button class="button button-large nmm-button nmm-ajax-button" data-nmm-ajax-action="' . esc_attr($action) . '"' . $confirmation . ' data-nmm-ajax-nonce="' . esc_attr(wp_create_nonce($action)) . '"' . $value . ' disabled="disabled" type="button"><span>' . $button_label . '</span></button>';
	}
}
