<?php


/**
 * Purge old transients
 */
if ( ! function_exists('purge_transients') ) {
	function purge_transients($older_than = '1 week', $safemode = true) {
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
		} else {
			foreach($transients as $transient) {
				get_transient($transient);
			}
		}

		return $transients;
	}
}
