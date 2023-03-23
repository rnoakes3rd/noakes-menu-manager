<?php
/*!
 * Cached function calls and flags.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Cache
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the cache functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager_Cache extends Noakes_Menu_Manager_Wrapper
{
	/**
	 * Constructor function.
	 *
	 * @since 3.0.2 Changed remove query args hook name.
	 * @since 3.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		add_filter('ndt_remove_query_args', array($this, 'remove_query_args'));
	}
	
	/**
	 * Get a default cached item based on the provided name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 * @param  string $name Name of the cached item to return.
	 * @return mixed        Default cached item if it exists, otherwise an empty string.
	 */
	protected function _default($name)
	{
		switch ($name)
		{
			/**
			 * Current admin page being used.
			 *
			 * @since 3.1.0
			 *
			 * @var string
			 */
			case 'admin_page':

				return basename($_SERVER['SCRIPT_NAME']);
				
			/**
			 * Path to the plugin assets folder.
			 *
			 * @since 3.0.0
			 *
			 * @var string
			 */
			case 'assets_url':

				$folder = 'debug';
				
				if
				(
					!$this->script_debug
					||
					!file_exists(plugin_dir_path($this->base->plugin) . 'assets/' . $folder . '/')
				)
				{
					$folder = 'release';
				}

				return plugins_url('/assets/' . $folder . '/', $this->base->plugin);

			/**
			 * Before/after options for nav menus.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'before_after_options':

				return array
				(
					'' => __('None', 'noakes-menu-manager'),
					'em' => __('EM Tag', 'noakes-menu-manager'),
					'span' => __('SPAN Tag', 'noakes-menu-manager'),
					'strong' => __('STRONG Tag', 'noakes-menu-manager')
				);
				
			/**
			 * Container options for nav menus.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'container_options':

				return array
				(
					'' => __('None', 'noakes-menu-manager'),
					'div' => __('DIV Tag', 'noakes-menu-manager'),
					Noakes_Menu_Manager_Constants::CODE_NAV => __('NAV Tag', 'noakes-menu-manager')
				);
				
			/**
			 * True if AJAX is currently being processed.
			 *
			 * @since 3.0.3 Changed to built-in function.
			 * @since 3.0.2
			 *
			 * @var boolean
			 */
			case 'doing_ajax':
			
				return wp_doing_ajax();

			/**
			 * Depth options for nav menus.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'depth_options':

				return array
				(
					'' => __('No Limit', 'noakes-menu-manager'),
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
					7 => 7,
					8 => 8,
					9 => 9
				);

			/**
			 * Validation rules for the current form.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'form_validation':

				return array();

			/**
			 * Item spacing options for nav menus.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'item_spacing_options':

				return array
				(
					'' => __('Preserve', 'noakes-menu-manager'),
					'discard' => __('Discard', 'noakes-menu-manager')
				);

			/**
			 * Asset file names pulled from the manifest JSON.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'manifest':

				return Noakes_Menu_Manager_Utilities::load_json('assets/manifest.json');
				
			/**
			 * Default values for the wp_nav_menu code.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'nav_menu_defaults':
			
				return array
				(
					'menu' => '',
					'menu_class' => '',
					'menu_id' => '',
					'container' => 'div',
					'container_class' => '',
					'container_id' => '',
					'fallback_cb' => '',
					'before' => '',
					'after' => '',
					'link_before' => '',
					'link_after' => '',
					'echo' => 'true',
					'depth' => 0,
					'walker' => '',
					'theme_location' => '',
					'items_wrap' => '',
					'item_spacing' => ''
				);

			/**
			 * Current option name being used.
			 *
			 * @since 3.1.0
			 *
			 * @var string
			 */
			case 'option_name':

				return
				(
					isset($_GET['page'])
					&&
					!empty($_GET['page'])
				)
				? sanitize_key($_GET['page'])
				: '';

			/**
			 * General details about the plugin.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'plugin_data':

				return Noakes_Menu_Manager_Plugins::get_data(plugin_basename($this->base->plugin));
				
			/**
			 * Nav menus registered outside of the Nav Menu Manager.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'registered_nav_menus':
			
				return array();
				
			/**
			 * Query args to remove from the current URL.
			 *
			 * @since 3.0.0
			 *
			 * @var array
			 */
			case 'remove_query_args':

				return array();

			/**
			 * Object for the current screen.
			 *
			 * @since 3.0.3 Simplified variable.
			 * @since 3.0.0
			 *
			 * @var WP_Screen
			 */
			case 'screen':

				return get_current_screen();
				
			/**
			 * True if script debugging is enabled.
			 *
			 * @since 3.0.0
			 *
			 * @var boolean
			 */
			case 'script_debug':
			
				return
				(
					defined('SCRIPT_DEBUG')
					&&
					SCRIPT_DEBUG
				);
		}

		return parent::_default($name);
	}
	
	/**
	 * Filter the query args that should be removed from a URL.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array $query_args Current query args that should be removed from a URL.
	 * @return array             Modified query args that should be removed from a URL.
	 */
	public function remove_query_args($query_args)
	{
		return array_merge($query_args, $this->remove_query_args);
	}

	/**
	 * Obtain a path to an asset.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  string $path      Path to the asset folder.
	 * @param  string $file_name File name for the asset.
	 * @return string            Full path to the requested asset.
	 */
	public function asset_path($path, $file_name)
	{
		$manifest = $this->manifest;

		if (isset($manifest[$file_name]))
		{
			$file_name = $manifest[$file_name];
		}

		return trailingslashit($this->assets_url . $path) . $file_name;
	}
	
	/**
	 * Get the filtered query args that should be removed from a URL.
	 *
	 * @since 3.2.2 Added additional query args for the filter.
	 * @since 3.0.2 Changed remove query args hook name.
	 * @since 3.0.0
	 *
	 * @access public
	 * @param  array $query_args Additional query args that should be removed from a URL.
	 * @return array             Filtered query args that should be removed from a URL.
	 */
	public function get_remove_query_args($query_args = array())
	{
		/**
		 * Filters the Noakes Development Tools query args that should be removed from the URL.
		 *
		 * @since 3.0.0
		 *
		 * @param  array $query_args Query args that should be removed.
		 * @return array             Modified query args that should be removed.
		 */
		return apply_filters('ndt_remove_query_args', Noakes_Menu_Manager_Utilities::check_array($query_args));
	}
}

