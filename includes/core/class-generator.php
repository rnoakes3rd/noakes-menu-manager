<?php
/*!
 * Generator functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Generator
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the generator functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager_Generator extends Noakes_Menu_Manager_Wrapper
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
		parent::__construct();

		$this->load_option();
		
		add_action('admin_menu', array($this, 'admin_menu'));
		
		add_filter('plugin_action_links_' . plugin_basename($this->base->plugin), array($this, 'plugin_action_links'), 9);
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
			 * Generator page title.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'page_title':
			
				return __('Generator', 'noakes-menu-manager');
				
			/**
			 * Method for selecting the nav menu to generate code for.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'choose_menu_by':
			
				return '';
				
			/**
			 * Theme location to be used.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'theme_location':
			
				return '';
				
			/**
			 * Nav menu to be used.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'menu':
			
				return '';
				
			/**
			 * CSS class to use for the ul element which forms the menu.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'menu_class':
			
				return '';
				
			/**
			 * The ID that is applied to the ul element which forms the menu.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'menu_id':
			
				return '';
				
			/**
			 * Whether to wrap the ul, and what to wrap it with.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'container':
			
				return 'div';
				
			/**
			 * Class that is applied to the container.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'container_class':
			
				return '';
				
			/**
			 * The ID that is applied to the container.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'container_id':
			
				return '';
				
			/**
			 * The aria-label attribute that is applied to the container when it's a nav element.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'container_aria_label':
			
				return '';
				
			/**
			 * If the menu doesn't exist, a callback function will fire.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'fallback_cb':
			
				return '';
				
			/**
			 * Tag wrapped around the link markup.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'before_after_link':
			
				return '';
				
			/**
			 * Tag wrapped around the link text.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'before_after_text':
			
				return '';
				
			/**
			 * Whether to echo the menu or return it.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'echoed':
			
				return Noakes_Menu_Manager_Constants::CODE_TRUE;
				
			/**
			 * How many levels of the hierarchy are to be included.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'depth':
			
				return '';
				
			/**
			 * Instance of a custom walker class.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'walker':
			
				return '';
				
			/**
			 * How the list items should be wrapped. Uses printf() format with numbered placeholders.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'items_wrap':
			
				return '';
				
			/**
			 *  Whether to preserve whitespace within the menu's HTML.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'item_spacing':
			
				return '';
		}

		return parent::_default($name);
	}
	
	/**
	 * Load the generator option.
	 *
	 * @since 3.0.3 Added option unslashing.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array $settings Settings array to load, or null if the settings should be loaded from the database.
	 * @return void
	 */
	public function load_option($settings = null)
	{
		if (empty($settings))
		{
			$settings = wp_unslash(get_option(Noakes_Menu_Manager_Constants::OPTION_GENERATOR));
		}
		
		if (empty($settings))
		{
			$this->_value_collection = $this;
		}
		else
		{
			$this->_set_properties($settings);
		}
	}

	/**
	 * Add the generator menu item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu()
	{
		$generator_page = add_submenu_page('tools.php', Noakes_Menu_Manager_Output::page_title($this->page_title), $this->base->cache->plugin_data['Name'], 'manage_options', Noakes_Menu_Manager_Constants::OPTION_GENERATOR, array($this, 'generator_page'));

		if ($generator_page)
		{
			Noakes_Menu_Manager_Output::add_tab('tools.php', Noakes_Menu_Manager_Constants::OPTION_GENERATOR, $this->page_title);
			
			add_action('load-' . $generator_page, array($this, 'load_generator_page'));
		}
	}

	/**
	 * Output the generator page.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function generator_page()
	{
		Noakes_Menu_Manager_Output::admin_form_page($this->page_title, Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS, Noakes_Menu_Manager_Constants::OPTION_GENERATOR);
	}

	/**
	 * Load generator page functionality.
	 * 
	 * @since 3.2.2 Removed PHP_INT_MAX reference.
	 * @since 3.2.0 Changed hook priority and added data structure validation.
	 * @since 3.0.2 Improved conditions and changed screen setup hook name.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function load_generator_page()
	{
		/**
		 * Setup the screen for Noakes Development Tools.
		 *
		 * @since 3.0.0
		 *
		 * @param  array $suffix Page suffix to use when resetting the screen.
		 * @return void
		 */
		do_action('ndt_screen_setup');
		
		add_action('admin_enqueue_scripts', array('Noakes_Menu_Manager_Global', 'admin_enqueue_scripts'), 9999999);
		add_action('admin_footer', array('Noakes_Menu_Manager_Global', 'admin_footer_templates'));
		
		add_screen_option
		(
			'layout_columns',

			array
			(
				'default' => 2,
				'max' => 2
			)
		);

		Noakes_Menu_Manager_Help::output('generator');
		
		$this->prepare_meta_boxes();

		Noakes_Menu_Manager_Meta_Box::side_meta_boxes();
		Noakes_Menu_Manager_Meta_Box::finalize_meta_boxes();
	}
	
	/**
	 * Prepare the generator form meta boxes.
	 * 
	 * @since 3.2.0
	 * 
	 * @access public
	 * @return void
	 */
	public function prepare_meta_boxes()
	{
		$has_container = (!empty($this->container));
		
		if
		(
			!empty($this->theme_location)
			||
			!empty($this->menu)
		)
		{
			$args = $this->base->nav_menus->wp_nav_menu_defaults;
			$choose_menu_by_menu = ($this->choose_menu_by === Noakes_Menu_Manager_Constants::CODE_MENU);
			
			if (!isset($this->_properties['echoed']))
			{
				$args['echo'] = Noakes_Menu_Manager_Constants::CODE_FALSE;
			}
			
			foreach ($this->_properties as $name => $value)
			{
				if ($name === Noakes_Menu_Manager_Constants::CODE_MENU)
				{
					if ($choose_menu_by_menu)
					{
						$args[$name] = $value;
					}
				}
				else if ($name === 'theme_location')
				{
					if (!$choose_menu_by_menu)
					{
						$args[$name] = $value;
					}
				}
				else if ($name === 'container_class')
				{
					if ($has_container)
					{
						$args[$name] = $value;
					}
				}
				else if ($name === 'container_id')
				{
					if ($has_container)
					{
						$args[$name] = $value;
					}
				}
				else if ($name === 'container_aria_label')
				{
					if ($this->container === Noakes_Menu_Manager_Constants::CODE_NAV)
					{
						$args[$name] = $value;
					}
				}
				else if ($name === 'fallback_cb')
				{
					if ($value === Noakes_Menu_Manager_Constants::CODE_TRUE)
					{
						$args[$name] = 'wp_page_menu';
					}
					else
					{
						$args[$name] = $value;
					}
				}
				else if ($name === 'before_after_link')
				{
					if (!empty($value))
					{
						$tag = esc_attr($value);
						$args['before'] = '<' . $tag . '>';
						$args['after'] = '</' . $tag . '>';
					}
				}
				else if ($name === 'before_after_text')
				{
					if (!empty($value))
					{
						$tag = esc_attr($value);
						$args['link_before'] = '<' . $tag . '>';
						$args['link_after'] = '</' . $tag . '>';
					}
				}
				else if ($name === 'echoed')
				{
					$args['echo'] = $value;
				}
				else if ($name === 'walker')
				{
					if ($value === Noakes_Menu_Manager_Constants::CODE_TRUE)
					{
						$args[$name] = 'new Walker_Nav_Menu()';
					}
				}
				else if ($name === 'items_wrap')
				{
					if ($value === Noakes_Menu_Manager_Constants::CODE_TRUE)
					{
						$args[$name] = '<ul id="%1$s" class="%2$s">%3$s</ul>';
					}
				}
				else if
				(
					$name !== 'page_title'
					&&
					$name !== 'choose_menu_by'
				)
				{
					$args[$name] = $value;
				}
			}
			
			$args = array_diff_assoc($args, $this->base->nav_menus->wp_nav_menu_defaults);
			
			if (!empty($args))
			{
				$theme_code = 'wp_nav_menu([wpcs]array[apnl](' . PHP_EOL;
				$shortcode = '[' . Noakes_Menu_Manager_Constants::COMPONENT_ID;
				$first_line = true;
				
				foreach ($args as $name => $value)
				{
					if
					(
						$name !== 'echo'
						&&
						$name !== 'walker'
					)
					{
						$shortcode .= ' ' . esc_attr($name) . '="' . esc_html(str_replace('"', Noakes_Menu_Manager_Constants::CODE_QUOTE, $value)) . '"';
					}
					
					if (!$first_line)
					{
						$theme_code .= ',' . PHP_EOL;
					}
					
					$value =
					(
						$name === 'walker'
						||
						is_numeric($value)
						||
						$value === Noakes_Menu_Manager_Constants::CODE_TRUE
						||
						$value === Noakes_Menu_Manager_Constants::CODE_FALSE
					)
					? $value
					: "'" . esc_attr($value) . "'";
					
					$theme_code .= "\t'" . esc_attr($name) . "' => " . esc_attr($value);
					$first_line = false;
				}
				
				$theme_code .= PHP_EOL . ')[wpcs]);';
				$shortcode .= ']';
				
				$wp_core = array
				(
					'[apnl]' => '',
					'[wpcs]' => ' '
				);

				$compressed = array
				(
					PHP_EOL => '',
					"\t" => '',
					"', '" => "','",
					' => ' => '=>',
					'[apnl]' => '',
					'[wpcs]' => ''
				);

				$author_preference = array
				(
					'[apnl]' => PHP_EOL,
					'[wpcs]' => ''
				);

				new Noakes_Menu_Manager_Meta_Box(array
				(
					'context' => 'normal',
					'id' => 'code_output',
					'title' => __('Code Output', 'noakes-menu-manager'),

					'fields' => array
					(
						new Noakes_Menu_Manager_Field_Tabs(array
						(
							'description' => __('Generated wp_nav_menu code based on the selected options.', 'noakes-menu-manager'),
							'input_padding' => true,
							'label' => __('Theme Code', 'noakes-menu-manager'),

							'tabs' => array
							(
								new Noakes_Menu_Manager_Field_Tab(array
								(
									'label' => __('WP Core', 'noakes-menu-manager'),

									'fields' => array
									(
										new Noakes_Menu_Manager_Field_Code(array
										(
											'code' => str_replace(array_keys($wp_core), array_values($wp_core), $theme_code)
										))
									)
								)),

								new Noakes_Menu_Manager_Field_Tab(array
								(
									'label' => __('Compressed', 'noakes-menu-manager'),

									'fields' => array
									(
										new Noakes_Menu_Manager_Field_Code(array
										(
											'code' => str_replace(array_keys($compressed), array_values($compressed), $theme_code)
										))
									)
								)),

								new Noakes_Menu_Manager_Field_Tab(array
								(
									'label' => __('Author Preference', 'noakes-menu-manager'),

									'fields' => array
									(
										new Noakes_Menu_Manager_Field_Code(array
										(
											'code' => str_replace(array_keys($author_preference), array_values($author_preference), $theme_code)
										))
									)
								))
							)
						)),

						new Noakes_Menu_Manager_Field_Code(array
						(
							'code' => $shortcode,
							'description' => __('Generated shortcode based on the selected options.', 'noakes-menu-manager'),
							'label' => __('Shortcode', 'noakes-menu-manager')
						)),

						new Noakes_Menu_Manager_Field_AJAX_Button(array
						(
							'action' => Noakes_Menu_Manager_Constants::HOOK_RESET_GENERATOR,
							'button_label' => __('Reset Generator', 'noakes-menu-manager')
						))
					)
				));
			}
		}
		
		$theme_location_label = __('Theme Location', 'noakes-menu-manager');
		$nav_menu_label = __('Nav Menu', 'noakes-menu-manager');
		
		$choose_by_menu_field = new Noakes_Menu_Manager_Field_Select(array
		(
			'description' => __('Method for selecting the nav menu to generate code for.', 'noakes-menu-manager'),
			'label' => __('Choose Menu By', 'noakes-menu-manager'),
			'name' => 'choose_menu_by',
			'radio_buttons' => true,

			'options' => array
			(
				'' => $theme_location_label,
				Noakes_Menu_Manager_Constants::CODE_MENU => $nav_menu_label
			)
		));
		
		$menus = wp_get_nav_menus();
		$nav_menus = array('' => __('Select a nav menu...', 'noakes-menu-manager'));

		foreach ($menus as $menu)
		{
			$nav_menus[$menu->slug] = $menu->name;
		}
		
		$container_field = new Noakes_Menu_Manager_Field_Select(array
		(
			'description' => __('Whether to wrap the ul, and what to wrap it with.', 'noakes-menu-manager'),
			'label' => __('Container', 'noakes-menu-manager'),
			'name' => 'container',
			'options' => $this->base->cache->container_options
		));
		
		$fallback_cb_label = __('Fallback Callback', 'noakes-menu-manager');
		$exclude_arg_label = __('Exclude argument from output', 'noakes-menu-manager');
		$include_arg_label = __('Include argument in output with default value', 'noakes-menu-manager');
		
		new Noakes_Menu_Manager_Meta_Box(array
		(
			'context' => 'normal',
			'id' => 'generator',
			'option_name' => Noakes_Menu_Manager_Constants::OPTION_GENERATOR,
			'title' => __('Generator', 'noakes-menu-manager'),
			'value_collection' => $this->_get_value_collection(),
			
			'fields' => array
			(
				$choose_by_menu_field,
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'classes' => 'required',
					'description' => __('Theme location to be used.', 'noakes-menu-manager'),
					'label' => $theme_location_label,
					'name' => 'theme_location',
					
					'conditions' => array
					(
						array
						(
							'compare' => '!=',
							'field' => $choose_by_menu_field,
							'value' => Noakes_Menu_Manager_Constants::CODE_MENU
						)
					),

					'options' => array_merge
					(
						array
						(
							'' => __('Select a theme location...', 'noakes-menu-manager')
						),
						
						get_registered_nav_menus()
					),
					
					'wrapper_classes' => ($this->choose_menu_by === Noakes_Menu_Manager_Constants::CODE_MENU)
					? 'nmm-hidden'
					: ''
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'classes' => 'required',
					'description' => __('Nav menu to be used.', 'noakes-menu-manager'),
					'label' => $nav_menu_label,
					'name' => 'menu',
					'options' => $nav_menus,
					
					'conditions' => array
					(
						array
						(
							'field' => $choose_by_menu_field,
							'value' => Noakes_Menu_Manager_Constants::CODE_MENU
						)
					),

					'wrapper_classes' => ($this->choose_menu_by === Noakes_Menu_Manager_Constants::CODE_MENU)
					? ''
					: 'nmm-hidden'
				)),
				
				new Noakes_Menu_Manager_Field_Text(array
				(
					'description' => __('CSS class to use for the ul element which forms the menu.', 'noakes-menu-manager'),
					'label' => __('Menu Class(es)', 'noakes-menu-manager'),
					'name' => 'menu_class',
					'sanitization' => Noakes_Menu_Manager_Sanitization::CLASSES,
					
					'attributes' => array
					(
						'placeholder' => __('Default \'menu\'.', 'noakes-menu-manager')
					)
				)),
				
				new Noakes_Menu_Manager_Field_Text(array
				(
					'description' => __('The ID that is applied to the ul element which forms the menu.', 'noakes-menu-manager'),
					'label' => __('Menu ID', 'noakes-menu-manager'),
					'name' => 'menu_id',
					
					'attributes' => array
					(
						'placeholder' => __('Default is the menu slug, incremented.', 'noakes-menu-manager')
					),
				)),
				
				$container_field,
				
				new Noakes_Menu_Manager_Field_Text(array
				(
					'description' => __('Class that is applied to the container.', 'noakes-menu-manager'),
					'label' => __('Container Class(es)', 'noakes-menu-manager'),
					'name' => 'container_class',
					'sanitization' => Noakes_Menu_Manager_Sanitization::CLASSES,
					
					'attributes' => array
					(
						'placeholder' => __('Default \'menu-{menu slug}-container\'.', 'noakes-menu-manager')
					),
					
					'conditions' => array
					(
						array
						(
							'compare' => '!=',
							'field' => $container_field,
							'value' => ''
						)
					),

					'wrapper_classes' => ($has_container)
					? ''
					: 'nmm-hidden'
				)),
				
				new Noakes_Menu_Manager_Field_Text(array
				(
					'description' => __('The ID that is applied to the container.', 'noakes-menu-manager'),
					'label' => __('Container ID', 'noakes-menu-manager'),
					'name' => 'container_id',
					
					'conditions' => array
					(
						array
						(
							'compare' => '!=',
							'field' => $container_field,
							'value' => ''
						)
					),

					'wrapper_classes' => ($has_container)
					? ''
					: 'nmm-hidden'
				)),
				
				new Noakes_Menu_Manager_Field_Text(array
				(
					'description' => __('The aria-label attribute that is applied to the container when it\'s a nav element.', 'noakes-menu-manager'),
					'label' => __('Container ARIA Label', 'noakes-menu-manager'),
					'name' => 'container_aria_label',
					
					'conditions' => array
					(
						array
						(
							'compare' => '=',
							'field' => $container_field,
							'value' => Noakes_Menu_Manager_Constants::CODE_NAV
						)
					),

					'wrapper_classes' => ($this->container === Noakes_Menu_Manager_Constants::CODE_NAV)
					? ''
					: 'nmm-hidden'
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('If the menu doesn\'t exist, a callback function will fire.', 'noakes-menu-manager'),
					'label' => $fallback_cb_label,
					'name' => 'fallback_cb',
					
					'options' => array
					(
						'' => $exclude_arg_label,
						Noakes_Menu_Manager_Constants::CODE_TRUE => $include_arg_label,

						Noakes_Menu_Manager_Constants::CODE_FALSE => sprintf
						(
							__('Explicitly disable %s', 'noakes-menu-manager'),
							$fallback_cb_label
						)
					)
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('Tag wrapped around the link markup.', 'noakes-menu-manager'),
					'label' => __('Before/After Link', 'noakes-menu-manager'),
					'name' => 'before_after_link',
					'options' => $this->base->cache->before_after_options
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('Tag wrapped around the link text.', 'noakes-menu-manager'),
					'label' => __('Before/After Text', 'noakes-menu-manager'),
					'name' => 'before_after_text',
					'options' => $this->base->cache->before_after_options
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'checkbox_value' => Noakes_Menu_Manager_Constants::CODE_TRUE,
					'field_label' => __('Whether to echo the menu or return it.', 'noakes-menu-manager'),
					'label' => __('Echo', 'noakes-menu-manager'),
					'name' => 'echoed'
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('How many levels of the hierarchy are to be included.', 'noakes-menu-manager'),
					'label' => __('Depth', 'noakes-menu-manager'),
					'name' => 'depth',
					'options' => $this->base->cache->depth_options
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('Instance of a custom walker class.', 'noakes-menu-manager'),
					'label' => __('Walker', 'noakes-menu-manager'),
					'name' => 'walker',
					
					'options' => array
					(
						'' => $exclude_arg_label,
						Noakes_Menu_Manager_Constants::CODE_TRUE => $include_arg_label
					)
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('How the list items should be wrapped. Uses printf() format with numbered placeholders.', 'noakes-menu-manager'),
					'label' => __('Items Wrap', 'noakes-menu-manager'),
					'name' => 'items_wrap',
					
					'options' => array
					(
						'' => $exclude_arg_label,
						Noakes_Menu_Manager_Constants::CODE_TRUE => $include_arg_label
					)
				)),
				
				new Noakes_Menu_Manager_Field_Select(array
				(
					'description' => __('Whether to preserve whitespace within the menu\'s HTML.', 'noakes-menu-manager'),
					'label' => __('Item Spacing', 'noakes-menu-manager'),
					'name' => 'item_spacing',
					'options' => $this->base->cache->item_spacing_options
				)),
				
				new Noakes_Menu_Manager_Field_Submit(array
				(
					'button_label' => __('Generate Code', 'noakes-menu-manager')
				))
			)
		));
	}

	/**
	 * Add the generator to the plugin action links.
	 *
	 * @since 3.2.0 Added non-breaking space before dashicon.
	 * @since 3.0.3 Added Dashicon to link.
	 * @since 3.0.2 Removed escape from admin URL.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array $links Existing action links.
	 * @return array        Modified action links.
	 */
	public function plugin_action_links($links)
	{
		array_unshift($links, '<a class="dashicons-before dashicons-admin-generic" href="' . get_admin_url(null, 'tools.php?page=' . Noakes_Menu_Manager_Constants::OPTION_GENERATOR) . '">&nbsp;' . $this->page_title . '</a>');

		return $links;
	}
}
