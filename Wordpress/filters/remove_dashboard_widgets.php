<?php


/**
 * Remove Dashboard `Incoming Links` Widget
 */
add_action('admin_init', create_function('', 'remove_meta_box("dashboard_incoming_links", "dashboard", "normal");'));


/**
 * Remove Dashboard `WP Plugins` Widget
 */
add_action('admin_init', create_function('', 'remove_meta_box("dashboard_plugins", "dashboard", "normal");'));


/**
 * Remove Dashboard `Primary feed (Dev Blog)` Widget
 */
add_action('admin_init', create_function('', 'remove_meta_box("dashboard_primary", "dashboard", "normal");'));



/**
 * Remove Dashboard `Secondary Feed (Planet)` Widget
 */
add_action('admin_init', create_function('', 'remove_meta_box("dashboard_secondary", "dashboard", "normal");'));

