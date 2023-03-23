<?php
/*!
 * Code field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Code Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the code field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Code extends Noakes_Menu_Manager_Field
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
			 * Code added to the field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'code':
			
				return '';
				
			/**
			 * True if a copy to clipboard button should be included.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'include_copy':
			
				return true;
			
			/**
			 * True if the field is tall and the description should be displayed below the label.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'is_tall':
			
				return true;
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the code field.
	 *
	 * @since 3.1.0 Added large class to button element.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the code field should be echoed.
	 * @return string        Generated code field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = (empty($this->code))
		? ''
		: '<pre class="nmm-code' . $this->_field_classes(false) . '">'
			. $this->code
		. '</pre>';
		
		if ($this->include_copy)
		{
			$output .= '<div class="nmm-field-actions">'
				. '<button class="button button-large nmm-button nmm-copy-to-clipboard" type="button">' . __('Copy to Clipboard', 'noakes-menu-manager') . '</button>'
			. '</div>';
		}
		
		return parent::_output($output, 'code', $echo);
	}
}
