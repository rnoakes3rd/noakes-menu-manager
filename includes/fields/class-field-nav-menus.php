<?php
/*!
 * Nav menus field functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Nav Menus Field
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the nav menus field object.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Field
 */
final class Noakes_Menu_Manager_Field_Nav_Menus extends Noakes_Menu_Manager_Field
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
				
			/**
			 * Sanitization name to use for the field.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'sanitization':
			
				return Noakes_Menu_Manager_Sanitization::COLLECTION;
		}

		return parent::_default($name);
	}
	
	/**
	 * Generate the output for the nav menus field.
	 *
	 * @since 3.2.0 Removed 'noreferrer' from link.
	 * @since 3.0.2 Removed escape from admin URL.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  boolean $echo True if the nav menus field should be echoed.
	 * @return string        Generated nav menus field if $echo is false.
	 */
	public function output($echo = false)
	{
		$output = '';
		$registered_nav_menus = $this->base->cache->registered_nav_menus;
		
		if (count($registered_nav_menus) > 0)
		{
			$assigned = get_nav_menu_locations();
			$rows = '';
			$select_label = __('Select %1$s', 'noakes-menu-manager');
			$select_all = ' checked="checked"';
			$value = $this->value;
			
			foreach ($registered_nav_menus as $location => $description)
			{
				$location_attr = esc_attr($location);
				$replace = '[' . $this->name . '][' . Noakes_Menu_Manager_Sanitization::TEXT . '][' . $location_attr . ']';
				$search = '[' . $this->name . ']';
				
				$checked = (isset($value[$location_attr]))
				? ' checked="checked"'
				: '';
				
				$menu = (isset($assigned[$location]))
				? wp_get_nav_menu_object($assigned[$location])
				: '';
				
				$assigned_to = (empty($menu))
				? __('None', 'noakes-menu-manager')
				: '<a href="' . admin_url('nav-menus.php?action=edit&menu=' . $menu->term_id) . '" rel="noopener" target="_blank">' . $menu->name . '</a>';
				
				if (empty($checked))
				{
					$select_all = '';
				}

				$rows .= '<tr>'
					. '<th class="check-column" scope="row">'
						. '<label class="screen-reader-text"' . str_replace($search, $replace, $this->label_attribute) . '>'
							. sprintf
							(
								$select_label,
								$location
							)
						. '</label>'
						. '<input' . str_replace($search, $replace, $this->input_attributes) . ' type="checkbox" value="1"' . $checked . ' />'
					. '</th>'
					. '<td>' . $location . '</td>'
					. '<td>' . $description . '</td>'
					. '<td>' . $assigned_to . '</td>'
				. '</tr>';
			}
			
			$header_row = '<tr>'
				. '<td class="check-column">'
					. '<label class="screen-reader-text" for="cb-select-all-1">'
						. sprintf
						(
							$select_label,
							__('All', 'noakes-menu-manager')
						)
					. '</label>'
					. '<input' . $select_all . ' id="cb-select-all-1" type="checkbox" />'
				. '</td>'
				. '<th>' . __('Location', 'noakes-menu-manager') . '</th>'
				. '<th>' . __('Description', 'noakes-menu-manager') . '</th>'
				. '<th>' . __('Assigned To', 'noakes-menu-manager') . '</th>'
			. '</tr>';

			$output = '<table class="widefat striped' . $this->_field_classes(false) . '">'
				. '<thead>'
					. $header_row
				. '</thead>'
				. '<tbody>'
					. $rows
				. '</tbody>'
				. '<tfoot>'
					. str_replace('cb-select-all-1', 'cb-select-all-2', $header_row)
				. '</tfoot>'
			. '</table>';
		}
		
		return parent::_output($output, 'nav-menus', $echo);
	}
}
