<?php
/*
Plugin Name:  Purge Transients
Description:  Purge old transients
Version:      0.2
Author:       Seebz
*/



if ( ! function_exists('purge_transients') ) {
	function purge_transients($older_than = '7 days', $safemode = true) {
		global $wpdb;

		$older_than_time = strtotime('-' . $older_than);
		if ($older_than_time > time() || $older_than_time < 1) {
			return false;
		}

		$transients = $wpdb->get_col(
			$wpdb->prepare( "
					SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name 
					FROM {$wpdb->options} 
					WHERE option_name LIKE '\_transient\_timeout\__%%'
						AND option_value < %s
			", $older_than_time)
		);
		if ($safemode) {
			foreach($transients as $transient) {
				get_transient($transient);
			}
		} else {
			$option_names = array();
			foreach($transients as $transient) {
				$option_names[] = '_transient_' . $transient;
				$option_names[] = '_transient_timeout_' . $transient;
			}
			if ($options_names) {
				$options_names = array_map(array($wpdb, 'escape'), $options_names);
				$options_names = "'". implode("','", $options_names) ."'";
				
				$result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name IN ({$option_names})" );
				if (!$result) {
					return false;
				}
			}
		}

		return $transients;
	}
}



function purge_transients_activation () {
	if (!wp_next_scheduled('purge_transients_cron')) {		
		wp_schedule_event( time(), 'daily', 'purge_transients_cron');
	}
}
register_activation_hook(__FILE__, 'purge_transients_activation');



function purge_transients_deactivation () {
	if (wp_next_scheduled('purge_transients_cron')) {
		wp_clear_scheduled_hook('purge_transients_cron');
	}
}
register_deactivation_hook(__FILE__, 'purge_transients_deactivation');



function do_purge_transients_cron () {
	purge_transients();
}
add_action('purge_transients_cron', 'do_purge_transients_cron');



?>
