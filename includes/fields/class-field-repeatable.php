<?php
/*!
 * Repeatable field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Repeatable Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the repeatable field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Repeatable extends Noakes_Menu_Manager_Field
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
			 * Additional actions added to the bottom of the repeatable field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'additional_actions':
			
				return '';
				
			/**
			 * Button label for adding items to the repeatable field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'add_label':
			
				return __('Add Item', 'noakes-menu-manager');
			
			/**
			 * True if the input layer should be padded.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'input_padding':
			
			/**
			 * True if the repeatable items cannot be sorted.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'is_locked':
			
			/**
			 * True if the repeatable field is simple and should be more compact.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'is_simple':
			
				return false;
				
			/**
			 * True if the field is tall and the description should be displayed below the label.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'is_tall':
			
				return true;
				
			/**
			 * Sanitization name to use for the field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'sanitization':
			
				return Noakes_Menu_Manager_Sanitization::REPEATABLE;
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the repeatable field.
	 *
	 * @since 3.1.0 Removed primary class and added large class to the add button element.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the repeatable field should be echoed.
	 * @return string        Generated repeatable field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		
		if
		(
			!empty($this->fields)
			&&
			!empty($this->name)
		)
		{
			if ($this->input_padding)
			{
				$this->push('wrapper_classes', 'nmm-input-padding');
			}
			
			$classes = array('nmm-repeatable');
			$values = $this->value;
			$option_name = $this->id . '[__i__]';
			
			$template = new Noakes_Menu_Manager_Field_Group(array
			(
				'fields' => array_merge
				(
					$this->fields,
					
					array
					(
						new Noakes_Menu_Manager_Field_Hidden(array
						(
							'name' => 'order-index',
							'sanitization' => '',
							'wrapper_classes' => 'nmm-repeatable-order-index'
						))
					)
				)
			));
			
			if ($this->is_locked)
			{
				$classes[] = 'nmm-repeatable-locked';
			}
			
			if ($this->is_simple)
			{
				$classes[] = 'nmm-repeatable-simple';
			}

			$output = '<div class="' . esc_attr(implode(' ', $classes)) . '">';
			
			if (is_array($values))
			{
				foreach ($values as $i => $value_collection)
				{
					$item = clone $template;
					$item->option_name = str_replace('__i__', $i, $option_name);
					$item->value_collection = $value_collection;
					$item->wrapper_classes = array('nmm-repeatable-item');

					$output .= $item->output();
				}
			}

			$template->is_template = true;
			$template->option_name = $option_name;
			$template->wrapper_classes = array('nmm-repeatable-template');

			$actions = new Noakes_Menu_Manager_Field_HTML(array
			(
				'wrapper_classes' => array('nmm-repeatable-actions'),

				'content' => '<div class="nmm-field-actions">'
					. '<button class="button button-large nmm-button nmm-repeatable-add" type="button"><span>' . $this->add_label . '</span></button>'
					. $this->additional_actions
				. '</div>'
			));

			$output .= $template->output()
			. $actions->output()
			. '</div>';
		}
		
		return parent::_output($output, 'repeatable', $echo);
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
		
		foreach ($raw_data as $index => $raw_repeatable_data)
		{
			if (is_numeric($index))
			{
				foreach ($this->fields as $field)
				{
					$valid_data = array_merge_recursive
					(
						$valid_data,

						array
						(
							$index => $field->validate_data($raw_repeatable_data)
						)
					);
				}
			}
		}
		
		return $valid_data;
	}
}
