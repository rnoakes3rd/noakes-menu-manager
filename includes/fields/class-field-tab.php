<?php
/*!
 * Tab field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Tab Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the tab field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Tab extends Noakes_Menu_Manager_Field
{
	/**
	 * Generate the output for the tab field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return string Generated tab field.
	 */
	public function output()
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
		
		return $output;
	}
}
