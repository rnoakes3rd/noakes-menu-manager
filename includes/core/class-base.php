<?php
/*!
 * Base plugin functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Base
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the base plugin functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager extends Noakes_Menu_Manager_Wrapper
{
	/**
	 * Main instance of Noakes_Menu_Manager.
	 *
	 * @since 3.0.0
	 *
	 * @access private static
	 * @var    Noakes_Menu_Manager
	 */
	private static $_instance = null;

	/**
	 * Returns the main instance of Noakes_Menu_Manager.
	 *
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  string          $file Main plugin file.
	 * @return Noakes_Menu_Manager       Main Noakes_Menu_Manager instance. 
	 */
	public static function _get_instance($file)
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self($file);
		}

		return self::$_instance;
	}

	/**
	 * Base name for the plugin.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    string
	 */
	public $plugin;

	/**
	 * Global cache object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    Noakes_Menu_Manager_Cache
	 */
	public $cache;

	/**
	 * Global settings object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    Noakes_Menu_Manager_Settings
	 */
	public $settings;

	/**
	 * Global nav menus object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    Noakes_Menu_Manager_Nav_Menus
	 */
	public $nav_menus;

	/**
	 * Global generator object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    Noakes_Menu_Manager_Generator
	 */
	public $generator;

	/**
	 * Global widgets object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    Noakes_Menu_Manager_Widgets
	 */
	public $widgets;

	/**
	 * Global AJAX object.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @var    Noakes_Menu_Manager_AJAX
	 */
	public $ajax;

	/**
	 * Constructor function.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  string $file Main plugin file.
	 * @return void
	 */
	public function __construct($file)
	{
		if
		(
			!empty($file)
			&&
			file_exists($file)
		)
		{
			$this->plugin = $file;

			add_action('plugins_loaded', array($this, 'plugins_loaded'));
		}
	}

	/**
	 * Load the plugin functionality.
	 *
	 * @since 3.2.2 Removed PHP_INT_MAX reference.
	 * @since 3.2.0 Changed hook priority.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function plugins_loaded()
	{
		$this->cache = new Noakes_Menu_Manager_Cache();
		$this->settings = new Noakes_Menu_Manager_Settings();
		$this->nav_menus = new Noakes_Menu_Manager_Nav_Menus();
		
		if (!$this->settings->disable_generator)
		{
			$this->generator = new Noakes_Menu_Manager_Generator();
		}
		
		if ($this->settings->enable_widget)
		{
			$this->widgets = new Noakes_Menu_Manager_Widgets();
		}
		
		$this->ajax = new Noakes_Menu_Manager_AJAX();
		
		add_action('admin_init', array('Noakes_Menu_Manager_Setup', 'check_version'), 0);
		add_action('init', array($this, 'init'));
		add_action('init', array($this, 'init_nav_menus'), 9999999);
		
		add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
		
		add_shortcode(Noakes_Menu_Manager_Constants::COMPONENT_ID, array($this, 'shortcode'));
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function init()
	{
		load_plugin_textdomain('noakes-menu-manager', false, dirname(plugin_basename($this->plugin)) . '/languages/');
	}

	/**
	 * Initialize the nav menus.
	 *
	 * @since 3.2.6 Security cleanup.
	 * @since 3.2.0 Improved data validation.
	 * @since 3.0.2 Improved conditions.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function init_nav_menus()
	{
		$this->cache->registered_nav_menus = get_registered_nav_menus();
		
		//phpcs:disable WordPress.Security.NonceVerification.Recommended
		if
		(
			count($this->settings->disable) > 0
			&&
			(
				!isset($_GET['page'])
				||
				$_GET['page'] !== Noakes_Menu_Manager_Constants::OPTION_SETTINGS
			)
		)
		{
			foreach ($this->settings->disable as $location => $value)
			{
				if ($value === '1')
				{
					unregister_nav_menu($location);
				}
			}
		}
		//phpcs:enable

		if (count($this->settings->menus) > 0)
		{
			foreach ($this->settings->menus as $menu)
			{
				if
				(
					isset($menu['location'])
					&&
					isset($menu['description'])
				)
				{
					register_nav_menu($menu['location'], $menu['description']);
				}
			}
		}
	}

	/**
	 * Add links to the plugin page.
	 *
	 * @since 3.2.0 Removed 'noreferrer' from links and added non-breaking space before dashicons.
	 * @since 3.0.2 Added Dashicons to links.
	 * @since 3.0.2 Improved condition.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array  $links Default links for the plugin.
	 * @param  string $file  Main plugin file name.
	 * @return array         Modified links for the plugin.
	 */
	public function plugin_row_meta($links, $file)
	{
		return ($file === plugin_basename($this->plugin))
		? array_merge
		(
			$links,

			array
			(
				'<a class="dashicons-before dashicons-sos" href="' . Noakes_Menu_Manager_Constants::URL_SUPPORT . '" rel="noopener" target="_blank">&nbsp;' . __('Support', 'noakes-menu-manager') . '</a>',
				'<a class="dashicons-before dashicons-star-filled" href="' . Noakes_Menu_Manager_Constants::URL_REVIEW . '" rel="noopener" target="_blank">&nbsp;' . __('Review', 'noakes-menu-manager') . '</a>',
				'<a class="dashicons-before dashicons-translation" href="' . Noakes_Menu_Manager_Constants::URL_TRANSLATE . '" rel="noopener" target="_blank">&nbsp;' . __('Translate', 'noakes-menu-manager') . '</a>',
				'<a class="dashicons-before dashicons-coffee" href="' . Noakes_Menu_Manager_Constants::URL_DONATE . '" rel="noopener" target="_blank">&nbsp;' . __('Donate', 'noakes-menu-manager') . '</a>'
			)
		)
		: $links;
	}

	/**
	 * Nav menu shortcode.
	 * 
	 * @since 3.2.6 Security cleanup.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @param  array  $atts    Settings for the nav menu output.
	 * @param  string $content Optional content displayed above the menu.
	 * @return string          Generated wp_nav_menu output.
	 */
	public function shortcode($atts = array(), $content = '')
	{
		if (isset($atts['walker']))
		{
			unset($atts['walker']);
		}
		
		foreach ($atts as $name => $value)
		{
			$atts[$name] = htmlspecialchars_decode(str_replace(Noakes_Menu_Manager_Constants::CODE_QUOTE, '"', $value));
		}
		
		$atts['echo'] = false;

		return wp_kses_post
		(
			wpautop(do_shortcode($content))
			. wp_nav_menu($atts)
		);
	}
}
