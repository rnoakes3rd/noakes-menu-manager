<?php
/*!
 * Functionality for plugin help.
 *
 * @since 3.0.0
 *
 * @package    Nav Menu Manager
 * @subpackage Help
 */

if (!defined('ABSPATH'))
{
	exit;
}

/**
 * Class used to implement plugin help functionality.
 *
 * @since 3.0.0
 */
final class Noakes_Menu_Manager_Help
{
	/**
	 * Output the help tabs.
	 *
	 * @since 3.2.0 Removed 'noreferrer' from links.
	 * @since 3.1.0 Help tab ID change.
	 * @since 3.0.2 Enabled help tabs and added AJAX check.
	 * @since 3.0.0
	 *
	 * @access public static
	 * @param  string  $kb_path     Path to the knowledge base article associated with this help tab.
	 * @param  boolean $plugin_page True if the help tab is being added to a plugin-specific page.
	 * @return void
	 */
	public static function output($kb_path, $plugin_page = true)
	{
		$nmm = Noakes_Menu_Manager();
		
		if
		(
			!empty($kb_path)
			&&
			!$nmm->cache->doing_ajax
		)
		{
			$id = 'nmm-' . $nmm->cache->option_name;

			if ($plugin_page === true)
			{
				$nmm->cache->screen->set_help_sidebar('<p><strong>' . __('Plugin developed by', 'noakes-menu-manager') . '</strong><br />'
				. '<a href="https://robertnoakes.com/" rel="noopener" target="_blank">Robert Noakes</a></p>'
				. '<hr />'
				. '<p><a class="button" href="' . Noakes_Menu_Manager_Constants::URL_SUPPORT . '" rel="noopener" target="_blank">' . __('Plugin Support', 'noakes-menu-manager') . '</a></p>'
				. '<p><a class="button" href="' . Noakes_Menu_Manager_Constants::URL_REVIEW . '" rel="noopener" target="_blank">' . __('Review Plugin', 'noakes-menu-manager') . '</a></p>'
				. '<p><a class="button" href="' . Noakes_Menu_Manager_Constants::URL_TRANSLATE . '" rel="noopener" target="_blank">' . __('Translate Plugin', 'noakes-menu-manager') . '</a></p>'
				. '<p><a class="button" href="' . Noakes_Menu_Manager_Constants::URL_DONATE . '" rel="noopener" target="_blank">' . __('Plugin Donation', 'noakes-menu-manager') . '</a></p>');
			}
			else if ($plugin_page !== false)
			{
				$id .= $plugin_page;
			}

			$url = Noakes_Menu_Manager_Constants::URL_KB . $kb_path . '/';

			$nmm->cache->screen->add_help_tab(array
			(
				'id' => $id,
				'priority' => 20,
				'title' => $nmm->cache->plugin_data['Name'],

				'content' => '<h3>' . __('For more information about this page, view the knowledge base article at:', 'noakes-menu-manager') . '<br />'
				. '<a href="' . esc_url($url) . '" rel="noopener" target="_blank">' . $url . '</a></h3>'
			));
		}
	}
}
