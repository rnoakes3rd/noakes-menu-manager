<?php
/*!
 * Text field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Text Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the text field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
class Noakes_Menu_Manager_Field_Text extends Noakes_Menu_Manager_Field
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
			 * Input type attribute.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'input_type':
			
				return 'text';
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the text field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the text field should be echoed.
	 * @return string        Generated text field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		if (!empty($this->id))
		{
			$max_width_open = $max_width_close = '';

			if
			(
				isset($this->attributes['maxlength'])
				&&
				is_numeric($this->attributes['maxlength'])
			)
			{
				$max_width_open = '<div style="max-width: ' . ($this->attributes['maxlength'] * 14 + 32) . 'px;">';
				$max_width_close = '</div>';
			}

			$output = $max_width_open
				. '<input' . $this->_field_classes() . $this->input_attributes . ' type="' . $this->input_type . '" value="' . esc_attr($this->value) . '" />'
			. $max_width_close;
		}
		
		return parent::_output($output, $this->input_type, $echo);
	}
}
