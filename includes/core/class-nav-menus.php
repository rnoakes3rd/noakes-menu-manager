<?php
/*!
 * Nav menus functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Nav Menus
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the nav menus functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager_Nav_Menus extends Noakes_Menu_Manager_Wrapper
{
	/**
	 * Default values for the wp_nav_menu code.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public
	 * @var    array
	 */
	public $wp_nav_menu_defaults = array
	(
		'menu' => '',
		'menu_class' => '',
		'menu_id' => '',
		'container' => 'div',
		'container_class' => '',
		'container_id' => '',
		'container_aria_label' => '',
		'fallback_cb' => '',
		'before' => '',
		'after' => '',
		'link_before' => '',
		'link_after' => '',
		'echo' => Noakes_Menu_Manager_Constants::CODE_TRUE,
		'depth' => '',
		'walker' => '',
		'theme_location' => '',
		'items_wrap' => '',
		'item_spacing' => ''
	);
	
	/**
	 * Active nav menu item classes applied by WordPress.
	 * 
	 * @since 3.0.0
	 * 
	 * @access private
	 * @var    array
	 */
	private $_active_classes = array
	(
		'current-menu-item',
		'current-menu-parent',
		'current-menu-ancestor',
		'current_page_item',
		'current_page_parent',
		'current_page_ancestor'
	);

	/**
	 * Constructor function.
	 *
	 * @since 3.1.0 Added link ID and class(es) functionality.
	 * @since 3.0.2 Added load call for nav menus page and improved condition.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$enable_id = $this->base->settings->enable_id;
		
		$modify_links =
		(
			$this->base->settings->enable_link_id
			||
			$this->base->settings->enable_link_classes
			||
			$this->base->settings->enable_query_string
			||
			$this->base->settings->enable_hash
		);

		if ($this->base->settings->active_class !== '')
		{
			add_filter('nav_menu_css_class', array($this, 'nav_menu_css_class'));
		}

		if
		(
			$enable_id
			||
			$this->base->settings->exclude_default_ids
		)
		{
			add_filter('nav_menu_item_id', array($this, 'nav_menu_item_id'), 11, 2);
		}
		
		if 
		(
			$enable_id
			||
			$modify_links
		)
		{
			add_action('load-nav-menus.php', array($this, 'load_nav_menus'));
			add_action('wp_nav_menu_item_custom_fields', array($this, 'wp_nav_menu_item_custom_fields'), 10, 2);
			add_action('wp_update_nav_menu_item', array($this, 'wp_update_nav_menu_item'), 10, 2);
			
			add_filter('manage_nav-menus_columns', array($this, 'manage_nav_menus_columns'), 11);
			add_filter('wp_setup_nav_menu_item', array($this, 'wp_setup_nav_menu_item'));
			
			if ($modify_links)
			{
				add_filter('nav_menu_link_attributes', array($this, 'nav_menu_link_attributes'), 10, 2);
			}
		}
	}

	/**
	 * Add the active class to appropriate menu items.
	 * 
	 * @since 3.0.2 Added filter hook for active classes.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  array $classes Array of nav menu item classes.
	 * @return array          Modified array of nav menu item classes.
	 */
	public function nav_menu_css_class($classes)
	{
		$intersecting = array_intersect
		(
			/**
			 * Filters active classes for nav menus.
			 *
			 * @since 3.0.2
			 *
			 * @param string $active_classes Default active classes.
			 */
			apply_filters(Noakes_Menu_Manager_Constants::HOOK_ACTIVE_CLASSES, $this->_active_classes),
			
			$classes
		);

		if (!empty($intersecting))
		{
			$classes[] = $this->base->settings->active_class;
		}

		return $classes;
	}

	/**
	 * Modify menu item IDs.
	 * 
	 * @since 3.0.2 Improved condition.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  string $menu_id ID applied to the current nav menu item.
	 * @param  object $item    Nav menu item object.
	 * @return string          Filtered menu item ID.
	 */
	public function nav_menu_item_id($menu_id, $item)
	{
		if
		(
			$this->base->settings->enable_id
			&&
			$item->{Noakes_Menu_Manager_Constants::POST_META_ID} !== ''
		)
		{
			return $item->{Noakes_Menu_Manager_Constants::POST_META_ID};
		}
		
		return ($this->base->settings->exclude_default_ids)
		? ''
		: $menu_id;
	}

	/**
	 * Load nav menus page functionality.
	 * 
	 * @since 3.0.2
	 * 
	 * @access public
	 * @return void
	 */
	public function load_nav_menus()
	{
		Noakes_Menu_Manager_Help::output('nav-menus');
	}
	
	/**
	 * Add custom fields to a nav menu item.
	 * 
	 * @since 3.1.0 Added link ID and class(es) functionality.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  string $item_id ID for the current nav menu item.
	 * @param  object $item    Nav menu item object.
	 * @return string          Filtered menu item ID.
	 */
	public function wp_nav_menu_item_custom_fields($item_id, $item)
	{
		if ($this->base->settings->enable_id)
		{
			$this->_custom_field(Noakes_Menu_Manager_Constants::POST_META_ID, $item_id, __('ID', 'noakes-menu-manager'), $item->{Noakes_Menu_Manager_Constants::POST_META_ID});
		}
		
		if ($this->base->settings->enable_link_id)
		{
			$this->_custom_field(Noakes_Menu_Manager_Constants::POST_META_LINK_ID, $item_id, __('Link ID', 'noakes-menu-manager'), $item->{Noakes_Menu_Manager_Constants::POST_META_LINK_ID});
		}
		
		if ($this->base->settings->enable_link_classes)
		{
			$this->_custom_field(Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES, $item_id, __('Link Class(es)', 'noakes-menu-manager'), $item->{Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES});
		}
		
		if ($this->base->settings->enable_query_string)
		{
			$this->_custom_field(Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING, $item_id, __('Query String', 'noakes-menu-manager'), $item->{Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING});
		}
		
		if ($this->base->settings->enable_hash)
		{
			$this->_custom_field(Noakes_Menu_Manager_Constants::POST_META_HASH, $item_id, __('Hash', 'noakes-menu-manager'), $item->{Noakes_Menu_Manager_Constants::POST_META_HASH});
		}
	}

	/**
	 * Update custom field values for a nav menu item.
	 * 
	 * @since 3.1.0 Added link ID and class(es) functionality.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  integer $menu_id         ID of the updated menu.
	 * @param  integer $menu_item_db_id ID of the updated menu item.
	 * @return void
	 */
	public function wp_update_nav_menu_item($menu_id, $menu_item_db_id)
	{
		if
		(
			!isset($_POST['update-nav-menu-nonce'])
			||
			!wp_verify_nonce($_POST['update-nav-menu-nonce'], 'update-nav_menu')
		)
		{
			return;
		}
		
		$id_name = 'menu-item-' . Noakes_Menu_Manager_Constants::POST_META_ID;
		$link_id_name = 'menu-item-' . Noakes_Menu_Manager_Constants::POST_META_LINK_ID;
		$link_classes_name = 'menu-item-' . Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES;
		$query_string_name = 'menu-item-' . Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING;
		$hash_name = 'menu-item-' . Noakes_Menu_Manager_Constants::POST_META_HASH;

		if
		(
			$this->base->settings->enable_id
			&&
			isset($_POST[$id_name])
			&&
			!empty($_POST[$id_name][$menu_item_db_id])
		)
		{
			update_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_ID, Noakes_Menu_Manager_Utilities::remove_redundancies($_POST[$id_name][$menu_item_db_id]));
		}
		else
		{
			delete_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_ID);
		}

		if
		(
			$this->base->settings->enable_link_id
			&&
			isset($_POST[$link_id_name])
			&&
			!empty($_POST[$link_id_name][$menu_item_db_id])
		)
		{
			update_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_LINK_ID, Noakes_Menu_Manager_Utilities::remove_redundancies($_POST[$link_id_name][$menu_item_db_id]));
		}
		else
		{
			delete_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_LINK_ID);
		}

		if
		(
			$this->base->settings->enable_link_classes
			&&
			isset($_POST[$link_classes_name])
			&&
			!empty($_POST[$link_classes_name][$menu_item_db_id])
		)
		{
			update_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES, Noakes_Menu_Manager_Utilities::sanitize_classes($_POST[$link_classes_name][$menu_item_db_id]));
		}
		else
		{
			delete_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES);
		}

		if
		(
			$this->base->settings->enable_query_string
			&&
			isset($_POST[$query_string_name])
			&&
			!empty($_POST[$query_string_name][$menu_item_db_id])
		)
		{
			update_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING, Noakes_Menu_Manager_Utilities::remove_redundancies($_POST[$query_string_name][$menu_item_db_id]));
		}
		else
		{
			delete_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING);
		}

		if
		(
			$this->base->settings->enable_hash
			&&
			isset($_POST[$hash_name])
			&&
			!empty($_POST[$hash_name][$menu_item_db_id])
		)
		{
			update_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_HASH, Noakes_Menu_Manager_Utilities::remove_redundancies($_POST[$hash_name][$menu_item_db_id]));
		}
		else
		{
			delete_post_meta($menu_item_db_id, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_HASH);
		}
	}

	/**
	 * Returns the columns for the nav menus page.
	 * 
	 * @since 3.1.0 Added link ID and class(es) functionality.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  array $columns Existing nav menu columns.
	 * @return array          Updated nav menu columns.
	 */
	public function manage_nav_menus_columns($columns)
	{
		if ($this->base->settings->enable_id)
		{
			$columns[Noakes_Menu_Manager_Constants::POST_META_ID] = __('ID', 'noakes-menu-manager');
		}

		if ($this->base->settings->enable_link_id)
		{
			$columns[Noakes_Menu_Manager_Constants::POST_META_LINK_ID] = __('Link ID', 'noakes-menu-manager');
		}

		if ($this->base->settings->enable_link_classes)
		{
			$columns[Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES] = __('Link Class(es)', 'noakes-menu-manager');
		}

		if ($this->base->settings->enable_query_string)
		{
			$columns[Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING] = __('Query String', 'noakes-menu-manager');
		}

		if ($this->base->settings->enable_hash)
		{
			$columns[Noakes_Menu_Manager_Constants::POST_META_HASH] = __('Hash', 'noakes-menu-manager');
		}

		return $columns;
	}

	/**
	 * Setup a nav menu item.
	 * 
	 * @since 3.1.0 Added link ID and class(es) functionality.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  object $menu_item Nav menu item object.
	 * @return string            Filtered nav menu item object.
	 */
	public function wp_setup_nav_menu_item($menu_item)
	{
		$menu_item->{Noakes_Menu_Manager_Constants::POST_META_ID} = ($this->base->settings->enable_id)
		? get_post_meta($menu_item->ID, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_ID, true)
		: '';
		
		$menu_item->{Noakes_Menu_Manager_Constants::POST_META_LINK_ID} = ($this->base->settings->enable_link_id)
		? get_post_meta($menu_item->ID, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_LINK_ID, true)
		: '';
		
		$menu_item->{Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES} = ($this->base->settings->enable_link_classes)
		? get_post_meta($menu_item->ID, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES, true)
		: '';
		
		$menu_item->{Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING} = ($this->base->settings->enable_query_string)
		? get_post_meta($menu_item->ID, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING, true)
		: '';
		
		$menu_item->{Noakes_Menu_Manager_Constants::POST_META_HASH} = ($this->base->settings->enable_hash)
		? get_post_meta($menu_item->ID, '_menu_item_' . Noakes_Menu_Manager_Constants::POST_META_HASH, true)
		: '';

		return $menu_item;
	}
	
	/**
	 * Modify menu item link attributes.
	 * 
	 * @since 3.1.0 Added link ID and class(es) functionality.
	 * @since 3.0.2 Improved conditions.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  string $atts Existing link attributes.
	 * @param  object $item Nav menu item object.
	 * @return string       Updated link attributes.
	 */
	public function nav_menu_link_attributes($atts, $item)
	{
		if
		(
			$this->base->settings->enable_link_id
			&&
			isset($item->{Noakes_Menu_Manager_Constants::POST_META_LINK_ID})
			&&
			$item->{Noakes_Menu_Manager_Constants::POST_META_LINK_ID} !== ''
		)
		{
			$atts['id'] = $item->{Noakes_Menu_Manager_Constants::POST_META_LINK_ID};
		}
		
		if
		(
			$this->base->settings->enable_link_classes
			&&
			isset($item->{Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES})
			&&
			$item->{Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES} !== ''
		)
		{
			if (!isset($atts['class']))
			{
				$atts['class'] = '';
			}
			else if (!empty($atts['class']))
			{
				$atts['class'] .= ' ';
			}
			
			$atts['class'] .= $item->{Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES};
		}
		
		$href_pieces = explode('#', $atts['href']);

		if
		(
			$this->base->settings->enable_query_string
			&&
			isset($item->{Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING})
			&&
			$item->{Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING} !== ''
		)
		{
			$href_pieces[0] .= (strpos($href_pieces[0], '?') === false)
			? '?'
			: '&';
			
			$href_pieces[0] .= $item->{Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING};
		}

		if
		(
			$this->base->settings->enable_hash
			&&
			isset($item->{Noakes_Menu_Manager_Constants::POST_META_HASH})
			&&
			$item->{Noakes_Menu_Manager_Constants::POST_META_HASH} !== '')
		{
			$href_pieces[1] = $item->{Noakes_Menu_Manager_Constants::POST_META_HASH};
		}

		$atts['href'] = $href_pieces[0];

		if
		(
			isset($href_pieces[1])
			&&
			$href_pieces[1] !== ''
		)
		{
			$atts['href'] .= '#' . $href_pieces[1];
		}

		return $atts;
	}
	
	/**
	 * Output a custom field.
	 * 
	 * @since 3.0.0
	 * 
	 * @access private
	 * @param  string $name    Name for the custom field.
	 * @param  string $item_id ID for the current nav menu item.
	 * @param  string $label   Label displayed above the field.
	 * @return void
	 */
	private function _custom_field($name, $item_id, $label, $value)
	{
		echo '<p class="field-' . $name . ' description description-wide">'
			. '<label for="edit-menu-item-' . $name . '-' . $item_id . '">'
				. $label . '<br />'
				. '<input class="widefat edit-menu-item-' . $name . '" id="edit-menu-item-' . $name . '-' . $item_id . '" name="menu-item-' . $name . '[' . $item_id . ']" type="text" value="' . esc_attr($value) . '" />'
			. '</label>'
		. '</p>';
	}
}
