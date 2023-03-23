<?php
/*!
 * Tabs field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Tabs Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the tabs field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Tabs extends Noakes_Menu_Manager_Field
{
	/**
	 * Constructor function.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array $properties Properties for the extension field.
	 * @return void
	 */
	public function __construct($properties)
	{
		$this->_array_only[] = 'tabs';
		
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
			 * True if the input layer should be padded.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'input_padding':
			
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
			 * Tabs for the tabs field.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'tabs':
			
				return array();
		}

		return parent::_default($name);
	}
	
	/**
	 * Add one or more tabs to the tabs field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  Noakes_Menu_Manager_Field_Tab $tabs Tab field object to add to the field.
	 * @return void
	 */
	public function add_tabs($tabs)
	{
		$tabs = Noakes_Menu_Manager_Utilities::check_array($tabs);
		
		foreach ($tabs as $tab)
		{
			$this->push('tabs', $tab);
		}
	}
	
	/**
	 * Generate the output for the tabs field.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the tabs field should be echoed.
	 * @return string        Generated tabs field if $echo is false.
	 */
	public function output($echo = false)
	{
		if ($this->input_padding)
		{
			$this->push('wrapper_classes', 'nmm-input-padding');
		}

		$buttons = $content = '';
		
		foreach ($this->tabs as $tab)
		{
			if
			(
				is_a($tab, 'Noakes_Menu_Manager_Field_Tab')
				&&
				!empty($tab->label)
			)
			{
				$tab->is_template = $this->is_template;
				$tab->option_name = $this->option_name;
				$tab->value_collection = $this->value_collection;

				$tab_content = $tab->output();

				if (!empty($tab_content))
				{
					$active_class = (empty($content))
					? ' nmm-tab-active'
					: '';

					$buttons .= '<a class="nmm-tab-link' . $active_class . '">' . $tab->label . '</a>';

					$content .= '<div class="nmm-tab' . $active_class . '">'
						. $tab_content
					. '</div>';
				}
			}
		}
		
		return parent::_output
		(
			(empty($buttons))
			? ''
			: '<div class="nmm-tabs">'
				. '<div class="nmm-tab-buttons nmm-secondary-tab-wrapper">' . $buttons . '</div>'
				. '<div class="nmm-tab-content">' . $content . '</div>'
			. '</div>',
			
			'tabs',
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
		
		foreach ($this->tabs as $tab)
		{
			foreach ($tab->fields as $field)
			{
				$valid_data = array_merge_recursive($valid_data, $field->validate_data($raw_data));
			}
		}
		
		return $valid_data;
	}
}
