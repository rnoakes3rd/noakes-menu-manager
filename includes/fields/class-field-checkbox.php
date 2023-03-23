<?php
/*!
 * Checkbox field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Checkbox Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the checkbox field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Checkbox extends Noakes_Menu_Manager_Field
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
			 * Custom value for a checkbox field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'checkbox_value':
			
			/**
			 * Label displayed next to the checkbox.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'field_label':
			
				return '';
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the checkbox field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the checkbox field should be echoed.
	 * @return string        Generated checkbox field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		if (!empty($this->id))
		{
			if ($this->checkbox_value === '')
			{
				$this->checkbox_value = '1';
			}

			if (!empty($this->value))
			{
				$this->value = $this->checkbox_value;
			}
			
			$output = '<label class="nmm-description">'
				. '<input ' . checked($this->checkbox_value, $this->value, false) . ' ' . $this->input_attributes . ' type="checkbox" value="' . esc_attr($this->checkbox_value) . '"' . $this->_field_classes() . ' />'
				. $this->field_label
			. '</label>';
		}
		
		return parent::_output($output, 'checkbox', $echo);
	}
	
	/**
	 * Add confirmation fields to a meta box.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  Noakes_Menu_Manager_Meta_Box $meta_box    Meta box object to add the confirmation fields to.
	 * @param  string                       $field_label Label displayed next to the confirmation checkboxes.
	 * @param  string                       $label       Primary label for the checkbox fields.
	 * @param  string                       $name        Name of the main confirmation field.
	 * @param  string                       $value       Value for the main confirmation field.
	 * @return Noakes_Menu_Manager_Meta_Box              Meta box with the added confirmation fields.
	 */
	public static function add_confirmation($meta_box, $field_label, $label, $name, $value)
	{
		$confirmed = (!empty($value));

		$unconfirmed = new Noakes_Menu_Manager_Field_Checkbox(array
		(
			'field_label' => $field_label,
			'label' => $label,
			'name' => $name . Noakes_Menu_Manager_Constants::SETTING_UNCONFIRMED,
			'sanitization' => Noakes_Menu_Manager_Sanitization::EXCLUDE,

			'wrapper_classes' => ($confirmed)
			? 'nmm-hidden'
			: ''
		));
		
		$meta_box->add_fields(array
		(
			$unconfirmed,
			
			new Noakes_Menu_Manager_Field_Checkbox(array
			(
				'field_label' => $field_label,
				'name' => $name,
				'sanitization' => Noakes_Menu_Manager_Sanitization::CONFIRMATION,

				'conditions' => ($confirmed)
				? array()
				: array
				(
					array
					(
						'field' => $unconfirmed,
						'value' => '1'
					)
				),

				'label' => ($confirmed)
				? $label
				: sprintf
				(
					_x('Confirm %1$s', 'Label', 'noakes-menu-manager'),
					$label
				),

				'wrapper_classes' => ($confirmed)
				? ''
				: 'nmm-confirmation nmm-hidden'
			))
		));
		
		return $meta_box;
	}
}
