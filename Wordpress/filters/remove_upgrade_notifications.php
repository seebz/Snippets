<?php


/**
 * Remove `Core` upgrade notifications
 */
add_filter('pre_site_transient_update_core', '__return_false');
wp_clear_scheduled_hook('wp_version_check');


/**
 * Remove `Plugins` upgrade notifications
 */
add_filter('pre_site_transient_update_plugins', '__return_false');
wp_clear_scheduled_hook('wp_update_plugins');
remove_action('load-update-core.php', 'wp_update_plugins');


/**
 * Remove `Themes` upgrade notifications
 */
add_filter('pre_site_transient_update_themes', '__return_false');
wp_clear_scheduled_hook('wp_update_themes');
remove_action('load-update-core.php', 'wp_update_themes');


/**
 * Remove Dashboard menu upgrade entry
 */
add_action('admin_init', 'remove_dashboard_menu_upgrade_entry');
function remove_dashboard_menu_upgrade_entry() {
	global $submenu;
	if (count($submenu['index.php']) === 2 && isset($submenu['index.php'][10])) {
		$submenu['index.php'] = array();
	} elseif (isset($submenu['index.php'][10])) {
		unset($submenu['index.php'][10]);
	}
}

