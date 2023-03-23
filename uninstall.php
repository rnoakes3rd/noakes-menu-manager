<?php
/*!
 * Functionality for plugin uninstallation.
 * 
 * @since 3.1.0 Added link ID and class(es) functionality and minor MySQL query cleanup.
 * @since 3.0.3 Added option unslashing.
 * @since 3.0.2 Improved condition and changed faux uninstall definition.
 * @since 3.0.0
 * 
 * @package Nav Menu Manager
 */

if
(
	!defined('WP_UNINSTALL_PLUGIN')
	&&
	!defined('NDT_FAUX_UNINSTALL_PLUGIN')
)
{
	exit;
}

global $wpdb;

require_once(dirname(__FILE__) . '/includes/static/class-constants.php');

$settings = wp_unslash(get_option(Noakes_Menu_Manager_Constants::OPTION_SETTINGS));
$deleted = 0;

if
(
	isset($settings[Noakes_Menu_Manager_Constants::SETTING_DELETE_SETTINGS])
	&&
	$settings[Noakes_Menu_Manager_Constants::SETTING_DELETE_SETTINGS]
)
{
	delete_option(Noakes_Menu_Manager_Constants::OPTION_GENERATOR);
	delete_option(Noakes_Menu_Manager_Constants::OPTION_SETTINGS);
	
	$deleted++;
}

if
(
	isset($settings[Noakes_Menu_Manager_Constants::SETTING_DELETE_POST_META])
	&&
	$settings[Noakes_Menu_Manager_Constants::SETTING_DELETE_POST_META]
)
{
	delete_metadata('post', '', Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_HASH, '', true);
	delete_metadata('post', '', Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_LINK_CLASSES, '', true);
	delete_metadata('post', '', Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_LINK_ID, '', true);
	delete_metadata('post', '', Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_ID, '', true);
	delete_metadata('post', '', Noakes_Menu_Manager_Constants::POST_META_PREFIX . Noakes_Menu_Manager_Constants::POST_META_QUERY_STRING, '', true);
	
	$deleted++;
}

if
(
	isset($settings[Noakes_Menu_Manager_Constants::SETTING_DELETE_USER_META])
	&&
	$settings[Noakes_Menu_Manager_Constants::SETTING_DELETE_USER_META]
)
{
	$wpdb->query($wpdb->prepare
	(
		"DELETE FROM 
			$wpdb->usermeta 
		WHERE 
			meta_key LIKE %s;\n",
			
		'%' . $wpdb->esc_like(Noakes_Menu_Manager_Constants::TOKEN) . '%'
	));
	
	$deleted++;
}

if ($deleted === 3)
{
	delete_option(Noakes_Menu_Manager_Constants::OPTION_VERSION);
}
