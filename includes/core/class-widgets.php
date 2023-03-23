<?php
/*!
 * Widgets functionality.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Widgets
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement the widgets functionality.
 *
 * @since 3.0.0
 *
 * @uses Noakes_Menu_Manager_Wrapper
 */
final class Noakes_Menu_Manager_Widgets extends Noakes_Menu_Manager_Wrapper
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
		
		add_action('load-widgets.php', array($this, 'load_widgets'));
		add_action('widgets_init', array($this, 'widgets_init'));
	}

	/**
	 * Load widgets page functionality.
	 * 
	 * @since 3.2.2 Removed PHP_INT_MAX reference.
	 * @since 3.2.0 Changed hook priority.
	 * @since 3.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function load_widgets()
	{
		add_action('admin_enqueue_scripts', array('Noakes_Menu_Manager_Global', 'admin_enqueue_scripts'), 9999999);
		
		Noakes_Menu_Manager_Help::output('widgets');
	}

	/**
	 * Register the nav menu sidebar widget.
	 * 
	 * @since 3.0.0
	 * 
	 * @access public
	 * @return void
	 */
	public function widgets_init()
	{
		register_widget('Noakes_Menu_Widget');
	}
}