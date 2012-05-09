<?php
/*
 Plugin Name: Debug Bar in BP
 Description: Adds the <em>Debug Bar button</em> to the <em>Buddypress Adminbar</em>.
 Version: 0.2
 Author: Seebz
 Author URI: http://seebz.net/
 */


class Debug_Bar_In_Bp {
	public function __construct() {
		add_action( 'plugins_loaded', array( &$this, 'plugins_loaded') );

	}

	public function plugins_loaded() {
		global $debug_bar;

		if ( is_admin() || ! is_super_admin() || ! defined('BP_VERSION')
			|| ! isset($debug_bar) || $debug_bar->is_wp_login() )
			return;

		add_action( 'bp_adminbar_menus',  array( &$this, 'admin_bar_menu' ), 1000 );
		add_action( 'wp_footer',          array( &$debug_bar, 'render' ) );
		add_action( 'wp_head',            array( &$debug_bar, 'ensure_ajaxurl' ), 1 );
		add_filter( 'body_class',         array( &$debug_bar, 'body_class' ) );

		$debug_bar->requirements();
		add_action( 'wp_enqueue_scripts', array( &$debug_bar, 'enqueue' ) );
		$debug_bar->init_panels();
	}

	public function admin_bar_menu() {
		?>
		<li id="wp-admin-bar-debug-bar" style="padding:0; background-image:none;"><a href="#" onclick="return false;">Debug Bar</a></li>
		<?php
	}
}

$GLOBALS['debug_bar_in_bp'] = new Debug_Bar_In_Bp();
