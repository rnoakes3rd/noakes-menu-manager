<?php
/**
 * Nav menu sidebar widget.
 * 
 * @since 3.0.0
 * 
 * @package    Nav Menu Manager
 * @subpackage Menu Widget
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement nav menu widget functionality.
 *
 * @since 3.0.0
 *
 * @uses WP_Widget
 */
class Noakes_Menu_Widget extends WP_Widget
{
	/**
	 * Constructor function.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct
		(
			Noakes_Menu_Manager_Constants::COMPONENT_ID,
			__('(Nav Menu Manager) Menu', 'noakes-menu-manager'),

			array
			(
				'classname' => 'widget_' . Noakes_Menu_Manager_Constants::COMPONENT_ID . ' widget_nav_menu',
				'customize_selective_refresh' => true,
				'description' => __('Add a nav menu to the sidebar.', 'noakes-menu-manager')
			)
		);
	}

	/**
	 * Output the widget.
	 * 
	 * @since 3.0.2 Improved condition.
	 * @since 3.0.1 Added missing nav menu widget args.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  array $args     Sidebar settings to apply to the widget.
	 * @param  array $instance Settings for the current widget.
	 * @return void
	 */
	public function widget($args, $instance)
	{
		$assigned = get_nav_menu_locations();
		
		$nav_menu =
		(
			empty($instance['theme_location'])
			||
			!isset($assigned[$instance['theme_location']])
		)
		? ''
		: wp_get_nav_menu_object($assigned[$instance['theme_location']]);
		
		$nav_menu =
		(
			!empty($nav_menu)
			||
			empty($instance['nav_menu'])
		)
		? $nav_menu
		: wp_get_nav_menu_object($instance['nav_menu']);
		
		if (empty($nav_menu))
		{
			return;
		}
		
		echo $args['before_widget'];
		
		$title = (empty($instance['title']))
		? ''
		: apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		if (!empty($title))
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$nav_menu_args = array
		(
			'menu' => $nav_menu,
			'fallback_cb' => false
		);
		
		$arg_names = array('menu_class', 'menu_id', 'container', 'container_class', 'container_id', 'container_aria_label', 'depth', 'item_spacing');

		foreach ($arg_names as $arg_name)
		{
			if
			(
				!empty($instance[$arg_name])
				||
				$arg_name === 'container'
			)
			{
				$nav_menu_args[$arg_name] = $instance[$arg_name];
			}
		}

		if (!empty($instance['before_after_link']))
		{
			$tag = esc_attr($instance['before_after_link']);
			$nav_menu_args['link_before'] = '<' . $tag . '>';
			$nav_menu_args['link_after'] = '</' . $tag . '>';
		}

		if (!empty($instance['before_after_text']))
		{
			$tag = esc_attr($instance['before_after_text']);
			$nav_menu_args['before'] = '<' . $tag . '>';
			$nav_menu_args['after'] = '</' . $tag . '>';
		}

		wp_nav_menu(apply_filters('widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance));

		echo $args['after_widget'];
	}

	/**
	 * Update the widget settings.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  array $new_instance New settings for the current widget.
	 * @param  array $old_instance Old settings for the current widget.
	 * @return array               Sanitized settings for the current widget.
	 */
	public function update($new_instance, $old_instance)
	{
		$classes_fields = array('menu_class', 'container_class');
		$number_fields = array('nav_menu', 'depth');
		
		$sanitize = array
		(
			Noakes_Menu_Manager_Sanitization::CLASSES => array(),
			Noakes_Menu_Manager_Sanitization::NUMBER => array(),
			Noakes_Menu_Manager_Sanitization::TEXT => array()
		);
		
		foreach ($new_instance as $name => $value)
		{
			if (in_array($name, $classes_fields))
			{
				$sanitize[Noakes_Menu_Manager_Sanitization::CLASSES][$name] = $value;
			}
			else if (in_array($name, $number_fields))
			{
				$sanitize[Noakes_Menu_Manager_Sanitization::NUMBER][$name] = $value;
			}
			else
			{
				$sanitize[Noakes_Menu_Manager_Sanitization::TEXT][$name] = $value;
			}
		}
		
		return Noakes_Menu_Manager_Sanitization::sanitize($sanitize);
	}

	/**
	 * Output the widget form.
	 * 
	 * @since 3.0.2 Removed escape from admin URL.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  array $instance Settings for the current widget.
	 * @return void
	 */
	public function form($instance)
	{
		global $wp_customize;
		
		$menus = wp_get_nav_menus();
		$has_menus = (!empty($menus));
		$nav_menu_options = array('' => __('&mdash; Disabled &mdash;', 'noakes-menu-manager'));
		$nmm = Noakes_Menu_Manager();
		
		$no_menus_message_class = ($has_menus)
		? ' nmm-hidden'
		: '';
		
		$form_controls_class = ($has_menus)
		? ''
		: ' nmm-hidden';

		echo '<div class="' . Noakes_Menu_Manager_Constants::COMPONENT_ID . '-wrapper">'
			. '<p class="nav-menu-widget-no-menus-message' . $no_menus_message_class . '">'
				. __('No nav menus have been created yet.', 'noakes-menu-manager') . '<br />'
				. sprintf
				(
					'<a href="%1$s">%2$s</a>',

					($wp_customize instanceof WP_Customize_Manager)
					? "javascript:wp.customize.panel('nav_menus').focus();"
					: admin_url('nav-menus.php'),

					__('Create a menu &raquo;', 'noakes-menu-manager')
				)
				. '</p>'
			. '<div class="nav-menu-widget-form-controls' . $form_controls_class . '">';
		
		$this->_field_text($instance, __('Title:', 'noakes-menu-manager'), 'title');
		$this->_field_select($instance, __('Theme Location:', 'noakes-menu-manager'), 'theme_location', array_merge($nav_menu_options, get_registered_nav_menus()));
		
		foreach ($menus as $menu)
		{
			$nav_menu_options[$menu->term_id] = $menu->name;
		}
		
		$this->_field_select($instance, __('or Nav Menu:', 'noakes-menu-manager'), 'nav_menu', $nav_menu_options);
		$this->_field_text($instance, __('Menu Class(es):', 'noakes-menu-manager'), 'menu_class');
		$this->_field_text($instance, __('Menu ID:', 'noakes-menu-manager'), 'menu_id');
		$this->_field_select($instance, __('Container:', 'noakes-menu-manager'), 'container', $nmm->cache->container_options, 'div');
		$this->_field_text($instance, __('Container Class(es):', 'noakes-menu-manager'), 'container_class');
		$this->_field_text($instance, __('Container ID:', 'noakes-menu-manager'), 'container_id');
		$this->_field_text($instance, __('Container ARIA Label:', 'noakes-menu-manager'), 'container_aria_label');
		$this->_field_select($instance, __('Before/After Link:', 'noakes-menu-manager'), 'before_after_link', $nmm->cache->before_after_options);
		$this->_field_select($instance, __('Before/After Text:', 'noakes-menu-manager'), 'before_after_text', $nmm->cache->before_after_options);
		$this->_field_select($instance, __('Depth:', 'noakes-menu-manager'), 'depth', $nmm->cache->depth_options);
		$this->_field_select($instance, __('Item Spacing:', 'noakes-menu-manager'), 'item_spacing', $nmm->cache->item_spacing_options);
		
		echo '</div>'
		. '</div>';
	}

	/**
	 * Output a widget select field.
	 * 
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  array  $instance      Settings for the current widget.
	 * @param  string $label         Label displayed with the select field.
	 * @param  string $field_name    Name of the select field.
	 * @param  array  $options       Options for the select field.
	 * @param  mixed  $default_value Optional default value for the select field.
	 * @return void
	 */
	private function _field_select($instance, $label, $field_name, $options, $default_value = '')
	{
		$value = (isset($instance[$field_name]))
		? $instance[$field_name]
		: $default_value;
		
		$id = $this->get_field_id($field_name);

		echo '<p>'
			. '<label for="' . $id . '">' . $label . '</label> '
			. '<select id="' . $id . '" name="' . $this->get_field_name($field_name) . '">';

		foreach ($options as $option_value => $option_label)
		{
			echo '<option ' . selected($option_value, $value, false) . ' value="' . esc_attr($option_value) . '">' . esc_html($option_label) . '</option>';
		}

		echo '</select>'
		. '</p>';
	}

	/**
	 * Output a widget text field.
	 * 
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  array  $instance      Settings for the current widget.
	 * @param  string $label         Label displayed with the text field.
	 * @param  string $field_name    Name of the text field.
	 * @param  mixed  $default_value Default value for the text field.
	 * @return void
	 */
	private function _field_text($instance, $label, $field_name, $default_value = '')
	{
		$value = (isset($instance[$field_name]))
		? $instance[$field_name]
		: $default_value;
		
		$id = $this->get_field_id($field_name);

		echo '<p>'
			. '<label for="' . $id . '">' . $label . '</label>'
			. '<input class="widefat" id="' . $id . '" name="' . $this->get_field_name($field_name) . '" type="text" value="' . esc_attr($value) . '" />'
		. '</p>';
	}
}
