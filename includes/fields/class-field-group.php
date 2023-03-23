<?php
/*!
 * Group field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Group Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the group field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Group extends Noakes_Menu_Manager_Field
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
	 * Generate the output for the group field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the group field should be echoed.
	 * @return string        Generated group field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		foreach ($this->fields as $field)
		{
			if (Noakes_Menu_Manager_Utilities::is_field($field))
			{
				$field->is_template = $this->is_template;
				$field->option_name = $this->option_name;
				$field->value_collection = $this->value_collection;

				$output .= $field->output();
			}
		}
		
		return parent::_output
		(
			(empty($output))
			? ''
			: '<div class="nmm-group">'
				. $output
				. '<div class="nmm-clear"></div>'
			. '</div>',
			
			'group',
			$echo
		);
	}
	
	/**
	 * Validate the data in the child fields.
	 *
	 * @since 3.2.0
	 *
	 * @access public
	 * @param  array $raw_data Raw data to be validated.
	 * @return array           Validated data.
	 */
	public function validate_child_data($raw_data)
	{
		$valid_data = array();
		
		foreach ($this->fields as $field)
		{
			$valid_data = array_merge_recursive($valid_data, $field->validate_data($raw_data));
		}
		
		return $valid_data;
	}
}
