<?php
/*!
 * Select field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Select Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the select field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Select extends Noakes_Menu_Manager_Field
{
	/**
	 * Constructor function.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array $properties Properties for the select field.
	 * @return void
	 */
	public function __construct($properties)
	{
		$this->_array_only[] = 'options';
		
		parent::__construct($properties);
	}
	
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
			 * Options displayed in the select field.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'options':
			
				return array();
				
			/**
			 * True if the options should be displayed as radio buttons.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'radio_buttons':
			
				return false;
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the select field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the select field should be echoed.
	 * @return string        Generated select field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		if
		(
			!empty($this->id)
			&&
			!empty($this->options)
		)
		{
			if ($this->radio_buttons)
			{
				$output = $this->_radio_buttons();
			}
			else
			{
				$output = $this->_select_box();
			}
		}
		
		return parent::_output($output, 'select', $echo);
	}
	
	/**
	 * Generate the radio buttons output.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 * @return string Generated radio buttons output.
	 */
	private function _radio_buttons()
	{
		$output = '<div class="nmm-field-actions">';
		$id = 'nmm-' . $this->id;
		$index = -1;
		$classes = $this->_field_classes();
		
		foreach ($this->options as $value => $label)
		{
			$output .= '<label><input ' . checked($this->value, $value, false) . ' ' . $classes . str_replace($id, $id . '-' . ++$index, $this->input_attributes) . ' type="radio" value="' . esc_attr($value) . '" />' . $label . '</label>';
		}
		
		$output .= '<div class="nmm-clear"></div>'
		. '</div>';
		
		return $output;
	}
	
	/**
	 * Generate the select box output.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 * @return string Generated select box output.
	 */
	private function _select_box()
	{
		$output = '<select' . $this->input_attributes . $this->_field_classes() . '>';

		foreach ($this->options as $value => $label)
		{
			if (is_array($label))
			{
				$output .= '<optgroup label="' . esc_attr($value) . '">';

				foreach ($label as $group_value => $group_label)
				{
					$output .= '<option ' . selected($this->value, $group_value, false) . ' value="' . esc_attr($group_value) . '">' . $group_label . '</option>';
				}

				$output .= '</optgroup>';
			}
			else
			{
				$output .= '<option ' . selected($this->value, $value, false) . ' value="' . esc_attr($value) . '">' . $label . '</option>';
			}
		}

		$output .= '</select>';
		
		return $output;
	}
}
