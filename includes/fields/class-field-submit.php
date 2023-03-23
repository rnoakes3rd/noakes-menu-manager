<?php
/*!
 * Submit field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Submit Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the submit field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Submit extends Noakes_Menu_Manager_Field
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
			 * Additional actions added next to the submit button.
			 *
			 * @since 3.1.0
			 *
			 * @var string
			 */
			case 'additional_actions':
			
				return '';
				
			/**
			 * Label for the submit button.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'button_label':
			
				return __('Submit', 'noakes-menu-manager');
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the submit field.
	 *
	 * @since 3.1.0 Added additional actions functionality.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the submit field should be echoed.
	 * @return string        Generated submit field if $echo is false.
	 */
	public function output($echo = false)
	{
		return parent::_output
		(
			'<div class="nmm-field-actions">'
				. '<button class="button button-large button-primary nmm-button' . $this->_field_classes(false) . '"' . $this->input_attributes . ' disabled="disabled" type="submit"><span>' . $this->button_label . '</span></button>'
				. $this->additional_actions
			. '</div>',
			
			'submit',
			$echo
		);
	}
}
