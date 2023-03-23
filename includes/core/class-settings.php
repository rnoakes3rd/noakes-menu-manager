<?php
/*!
 * Settings functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Settings
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the settings functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager_Settings extends Noakes_Menu_Manager_Wrapper
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
		add_action('admin_menu', array($this, 'admin_menu_tabs'), 11);
		
		add_filter('plugin_action_links_' . plugin_basename($this->base->plugin), array($this, 'plugin_action_links'));
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
			 * Settings page title.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'page_title':
			
				return __('Settings', 'noakes-menu-manager');
				
			/**
			 * True if the generator tool should be disabled.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'disable_generator':
				
			/**
			 * True if the NMM Menu widget should be available for sidebars.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'enable_widget':
				
			/**
			 * True if default DOM IDs should be excluded from nav menu items.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'exclude_default_ids':
			
			/**
			 * True if an ID field should be added to nav menu items.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'enable_id':
			
			/**
			 * True if a Link ID field should be added to nav menu items.
			 *
			 * @since 3.1.0
			 *
			 * @var boolean
			 */
			case 'enable_link_id':
			
			/**
			 * True if a Link Class(es) field should be added to nav menu items.
			 *
			 * @since 3.1.0
			 *
			 * @var boolean
			 */
			case 'enable_link_classes':
			
			/**
			 * True if a query string field should be added to nav menu items.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'enable_query_string':
			
			/**
			 * True if an hash field should be added to nav menu items.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'enable_hash';
			
			/**
			 * True if plugin settings should be deleted when the plugin is uninstalled.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case Noakes_Menu_Manager_Constants::SETTING_DELETE_SETTINGS:
			
			/**
			 * True if plugin settings should be deleted when the plugin is uninstalled.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case Noakes_Menu_Manager_Constants::SETTING_DELETE_SETTINGS . Noakes_Menu_Manager_Constants::SETTING_UNCONFIRMED:
			
			/**
			 * True if plugin user meta should be deleted when the plugin is uninstalled.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case Noakes_Menu_Manager_Constants::SETTING_DELETE_USER_META:
			
			/**
			 * True if plugin user meta should be deleted when the plugin is uninstalled.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case Noakes_Menu_Manager_Constants::SETTING_DELETE_USER_META . Noakes_Menu_Manager_Constants::SETTING_UNCONFIRMED:
			
				return false;
				
			/**
			 * Unused nav menus that should be disabled.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'disable':
				
			/**
			 * Nav menus that should be registered for the site.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'menus':
			
				return array();
				
			/**
			 * CSS class applied to all active nav menu items.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'active_class':
			
				return '';
		}

		return parent::_default($name);
	}

	/**
	 * Load the settings option.
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
			$settings = wp_unslash(get_option(Noakes_Menu_Manager_Constants::OPTION_SETTINGS));
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
	 * Add the settings menu item.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu()
	{
		$settings_page = add_options_page(Noakes_Menu_Manager_Output::page_title($this->page_title), $this->base->cache->plugin_data['Name'], 'manage_options', Noakes_Menu_Manager_Constants::OPTION_SETTINGS, array($this, 'settings_page'));

		if ($settings_page)
		{
			Noakes_Menu_Manager_Output::add_tab('options-general.php', Noakes_Menu_Manager_Constants::OPTION_SETTINGS, $this->page_title);
			
			add_action('load-' . $settings_page, array($this, 'load_settings_page'));
		}
	}

	/**
	 * Add additional nav tabs.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu_tabs()
	{
		if ($this->enable_widget)
		{
			Noakes_Menu_Manager_Output::add_tab('widgets.php', '', __('Widgets', 'noakes-menu-manager'));
		}
		
		Noakes_Menu_Manager_Output::add_tab('nav-menus.php', '', __('Nav Menus', 'noakes-menu-manager'));
	}

	/**
	 * Output the settings page.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function settings_page()
	{
		Noakes_Menu_Manager_Output::admin_form_page($this->page_title, Noakes_Menu_Manager_Constants::HOOK_SAVE_SETTINGS, Noakes_Menu_Manager_Constants::OPTION_SETTINGS);
	}

	/**
	 * Load settings page functionality.
	 * 
	 * @since 3.2.2 Removed PHP_INT_MAX reference.
	 * @since 3.2.0 Changed hook priority and added data structure validation.
	 * @since 3.1.0 Changed uninstall setting labels and added link ID and class(es) functionality.
	 * @since 3.0.2 Changed screen setup hook name.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function load_settings_page()
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

		Noakes_Menu_Manager_Help::output('settings');
		
		$this->prepare_meta_boxes();

		Noakes_Menu_Manager_Meta_Box::side_meta_boxes();
		Noakes_Menu_Manager_Meta_Box::finalize_meta_boxes();
	}
	
	/**
	 * Prepare the settings form meta boxes.
	 * 
	 * @since 3.2.0
	 * 
	 * @access public
	 * @return void
	 */
	public function prepare_meta_boxes()
	{
		$plugin_name = $this->base->cache->plugin_data['Name'];
		$value_collection = $this->_get_value_collection();
		
		$save_all_settings = array
		(
			'button_label' => __('Save All Settings', 'noakes-menu-manager')
		);
		
		new Noakes_Menu_Manager_Meta_Box(array
		(
			'context' => 'normal',
			'id' => 'general_settings',
			'option_name' => Noakes_Menu_Manager_Constants::OPTION_SETTINGS,
			'title' => __('General Settings', 'noakes-menu-manager'),
			'value_collection' => $value_collection,
			
			'fields' => array
			(
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Disable the nav menu generator tool.', 'noakes-menu-manager'),
					'label' => __('Disable Generator', 'noakes-menu-manager'),
					'name' => 'disable_generator'
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Make the \'(Nav Menu Manager) Menu\' widget available for sidebars.', 'noakes-menu-manager'),
					'label' => __('Enable Widget', 'noakes-menu-manager'),
					'name' => 'enable_widget'
				)),
				
				new Noakes_Menu_Manager_Field_Submit($save_all_settings)
			)
		));
		
		new Noakes_Menu_Manager_Meta_Box(array
		(
			'context' => 'normal',
			'id' => 'site_menus',
			'option_name' => Noakes_Menu_Manager_Constants::OPTION_SETTINGS,
			'title' => __('Site Menus', 'noakes-menu-manager'),
			'value_collection' => $value_collection,
			
			'fields' => array
			(
				new Noakes_Menu_Manager_Field_Nav_Menus(array
				(
					'description' => __('Nav menus added by the theme or another plugin. Use the checkboxes to disable nav menus that aren\'t in use.', 'noakes-menu-manager'),
					'label' => __('Existing Nav Menus', 'noakes-menu-manager'),
					'name' => 'disable'
				)),
				
				new Noakes_Menu_Manager_Field_Repeatable(array
				(
					'add_label' => __('Add Nav Menu', 'noakes-menu-manager'),
					'description' => __('Nav menus to register for the site.', 'noakes-menu-manager'),
					'input_padding' => true,
					'is_simple' => true,
					'label' => __('Nav Menus', 'noakes-menu-manager'),
					'name' => 'menus',
					
					'fields' => array
					(
						new Noakes_Menu_Manager_Field_Group(array
						(
							'fields' => array
							(
								new Noakes_Menu_Manager_Field_Text(array
								(
									'classes' => 'noatice-tooltip required',
									'name' => 'location',
									'sanitization' => Noakes_Menu_Manager_Sanitization::SLUG,
									'wrapper_classes' => 'nmm-col-sm-6 nmm-col-xs-12',

									'attributes' => array
									(
										'placeholder' => __('Location', 'noakes-menu-manager'),
										'title' => __('Menu location identifier, like a slug.', 'noakes-menu-manager')
									)
								)),

								new Noakes_Menu_Manager_Field_Text(array
								(
									'classes' => 'noatice-tooltip required',
									'name' => 'description',
									'wrapper_classes' => 'nmm-col-sm-6 nmm-col-xs-12',

									'attributes' => array
									(
										'placeholder' => __('Description', 'noakes-menu-manager'),
										'title' => __('Menu description that is displayed in the dashboard.', 'noakes-menu-manager')
									)
								))
							)
						))
					)
				)),
				
				new Noakes_Menu_Manager_Field_Submit($save_all_settings)
			)
		));
		
		new Noakes_Menu_Manager_Meta_Box(array
		(
			'context' => 'normal',
			'id' => 'menu_settings',
			'option_name' => Noakes_Menu_Manager_Constants::OPTION_SETTINGS,
			'title' => __('Menu Settings', 'noakes-menu-manager'),
			'value_collection' => $value_collection,
			
			'fields' => array
			(
				new Noakes_Menu_Manager_Field_Text(array
				(
					'description' => __('If entered, this CSS class(es) will be added to all active nav menu items.', 'noakes-menu-manager'),
					'label' => __('Active Class', 'noakes-menu-manager'),
					'name' => 'active_class',
					'sanitization' => Noakes_Menu_Manager_Sanitization::CLASSES
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Remove default nav menu item IDs.', 'noakes-menu-manager'),
					'label' => __('Exclude Default IDs', 'noakes-menu-manager'),
					'name' => 'exclude_default_ids'
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Add an ID field to nav menu items.', 'noakes-menu-manager'),
					'label' => __('Enable ID', 'noakes-menu-manager'),
					'name' => 'enable_id'
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Add a Link ID field to nav menu items.', 'noakes-menu-manager'),
					'label' => __('Enable Link ID', 'noakes-menu-manager'),
					'name' => 'enable_link_id'
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Add a Link Class(es) field to nav menu items.', 'noakes-menu-manager'),
					'label' => __('Enable Link Class(es)', 'noakes-menu-manager'),
					'name' => 'enable_link_classes'
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Add a query string field to nav menu items.', 'noakes-menu-manager'),
					'label' => __('Enable Query String', 'noakes-menu-manager'),
					'name' => 'enable_query_string'
				)),
				
				new Noakes_Menu_Manager_Field_Checkbox(array
				(
					'field_label' => __('Add a hash field to nav menu items.', 'noakes-menu-manager'),
					'label' => __('Enable Hash', 'noakes-menu-manager'),
					'name' => 'enable_hash'
				)),
				
				new Noakes_Menu_Manager_Field_Submit($save_all_settings)
			)
		));
		
		$uninstall_settings_box = new Noakes_Menu_Manager_Meta_Box(array
		(
			'context' => 'normal',
			'id' => 'uninstall_settings',
			'option_name' => Noakes_Menu_Manager_Constants::OPTION_SETTINGS,
			'title' => __('Uninstall Settings', 'noakes-menu-manager'),
			'value_collection' => $value_collection
		));
		
		if (!empty($this->menus))
		{
			$theme = wp_get_theme();
			$index = -1;
			$text_domain = $theme->get('TextDomain');
			
			$text_domain = (empty($text_domain))
			? 'noakes-menu-manager'
			: esc_attr($text_domain);
			
			$code = '/* START: ' . esc_html($this->base->cache->plugin_data['Name']) . ' Fail-safe Code */' . PHP_EOL
			. 'if[apnl][wpcs]([apnl][apt][wpcs]![wpcs]class_exists([wpcs]\'Noakes_Menu_Manager\'[wpcs])[apnl][apt][wpcs]&&[apnl][apt][wpcs]![wpcs]function_exists([wpcs]\'nmm_fail_safe_code\'[wpcs])[apnl][wpcs])[apnl][wpcs]{' . PHP_EOL
			. "\t" . 'add_action([wpcs]\'after_setup_theme\', \'nmm_fail_safe_code\'[wpcs]);[apnl]' . PHP_EOL
			. "\t" . 'function nmm_fail_safe_code()[apnl][apt][wpcs]{' . PHP_EOL
			. "\t\t" . 'register_nav_menus([wpcs]array[apnl][apt][apt](' . PHP_EOL
			. "\t\t\t";

			foreach ($this->menus as $menu)
			{
				if
				(
					isset($menu['location'])
					&&
					isset($menu['description'])
				)
				{
					if (++$index > 0)
					{
						$code .= ',' . PHP_EOL
						. "\t\t\t";
					}

					$code .= '\'' . esc_attr($menu['location']) . '\' => __([wpcs]\'' . esc_attr($menu['description']) . '\', \'' . $text_domain . '\'[wpcs])';
				}
			}
			
			$code .= PHP_EOL
			. "\t\t" . ')[wpcs]);' . PHP_EOL
			. "\t" . '}' . PHP_EOL
			. '}' . PHP_EOL
			. '/* END: ' . esc_html($this->base->cache->plugin_data['Name']) . ' Fail-safe Code */';
			
			$wp_core = array
			(
				'[apnl]' => '',
				'[apt]' => '',
				'[wpcs]' => ' '
			);
			
			$compressed = array
			(
				'/* ' => '/*',
				' */' => '*/',
				PHP_EOL => '',
				"\t" => '',
				"', '" => "','",
				' => ' => '=>',
				'[apnl]' => '',
				'[apt]' => '',
				'[wpcs]' => ''
			);
			
			$author_preference = array
			(
				'[apnl]' => PHP_EOL,
				'[apt]' => "\t",
				'[wpcs]' => ''
			);
			
			$uninstall_settings_box->add_fields(new Noakes_Menu_Manager_Field_Tabs(array
			(
				'description' => __('Add this code to the theme functions.php to prevent site menus from disappearing if the plugin is disabled or uninstalled.', 'noakes-menu-manager'),
				'input_padding' => true,
				'label' => __('Fail-safe Code', 'noakes-menu-manager'),
				
				'tabs' => array
				(
					new Noakes_Menu_Manager_Field_Tab(array
					(
						'label' => __('WP Core', 'noakes-menu-manager'),
						
						'fields' => array
						(
							new Noakes_Menu_Manager_Field_Code(array
							(
								'code' => str_replace(array_keys($wp_core), array_values($wp_core), $code)
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
								'code' => str_replace(array_keys($compressed), array_values($compressed), $code)
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
								'code' => str_replace(array_keys($author_preference), array_values($author_preference), $code)
							))
						)
					))
				)
			)));
		}
		
		$uninstall_settings_box = Noakes_Menu_Manager_Field_Checkbox::add_confirmation
		(
			$uninstall_settings_box,
			
			sprintf
			(
				_x('Delete settings for %1$s when the plugin is uninstalled.', 'Plugin Name', 'noakes-menu-manager'),
				$plugin_name
			),
			
			__('Delete Settings', 'noakes-menu-manager'),
			Noakes_Menu_Manager_Constants::SETTING_DELETE_SETTINGS,
			$this->{Noakes_Menu_Manager_Constants::SETTING_DELETE_SETTINGS}
		);
		
		$uninstall_settings_box = Noakes_Menu_Manager_Field_Checkbox::add_confirmation
		(
			$uninstall_settings_box,
			
			sprintf
			(
				_x('Delete post meta for %1$s when the plugin is uninstalled.', 'Plugin Name', 'noakes-menu-manager'),
				$plugin_name
			),
			
			__('Delete Post Meta', 'noakes-menu-manager'),
			Noakes_Menu_Manager_Constants::SETTING_DELETE_POST_META,
			$this->{Noakes_Menu_Manager_Constants::SETTING_DELETE_POST_META}
		);
		
		$uninstall_settings_box = Noakes_Menu_Manager_Field_Checkbox::add_confirmation
		(
			$uninstall_settings_box,
			
			sprintf
			(
				_x('Delete user meta for %1$s when the plugin is uninstalled.', 'Plugin Name', 'noakes-menu-manager'),
				$plugin_name
			),
			
			__('Delete User Meta', 'noakes-menu-manager'),
			Noakes_Menu_Manager_Constants::SETTING_DELETE_USER_META,
			$this->{Noakes_Menu_Manager_Constants::SETTING_DELETE_USER_META}
		);
		
		$uninstall_settings_box->add_fields(new Noakes_Menu_Manager_Field_Submit($save_all_settings));
	}

	/**
	 * Add settings to the plugin action links.
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
		array_unshift($links, '<a class="dashicons-before dashicons-admin-tools" href="' . get_admin_url(null, 'options-general.php?page=' . Noakes_Menu_Manager_Constants::OPTION_SETTINGS) . '">&nbsp;' . $this->page_title . '</a>');

		return $links;
	}
}
