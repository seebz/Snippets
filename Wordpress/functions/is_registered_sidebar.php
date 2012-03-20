<?php


/**
 * Whether a sidebar is registered
 *
 * @param mixed $index Sidebar name, id or number to check.
 * @return bool true if the sidebar is in registered, false otherwise.
 */
if ( ! function_exists('is_registered_sidebar') ) {
	function is_registered_sidebar($index = 1) {
		global $wp_registered_sidebars;

		if ( is_int($index) ) {
			$index = "sidebar-$index";
		} else {
			$index = sanitize_title($index);
			foreach ( (array) $wp_registered_sidebars as $key => $value ) {
				if ( sanitize_title($value['name']) == $index ) {
					$index = $key;
					break;
				}
			}
		}
		return isset( $wp_registered_sidebars[$index] );
	}
}
