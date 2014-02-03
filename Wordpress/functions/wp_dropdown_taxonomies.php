<?php


/**
 * Retrieve or display list of taxonomies as a dropdown (select list).
 */
if ( !function_exists('wp_dropdown_taxonomies') ) {
	function wp_dropdown_taxonomies($args = '') {
		$defaults = array(
			'selected' => 0, 'echo' => 1,
			'name' => 'taxonomy', 'id' => '',
			'class' => '',
			'show_option_none' => '', 'show_option_no_change' => '',
			'option_none_value' => ''
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$taxonomies = get_taxonomies(array('public'=>true, 'show_ui'=>true), 'objects');

		$output = '';
		// Back-compat with old system where both id and name were based on $name argument
		if ( empty($id) )
			$id = $name;

		if ( ! empty($taxonomies) ) {
			$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "' class='" . esc_attr( $class ) . "'>\n";
			if ( $show_option_no_change )
				$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
			if ( $show_option_none )
				$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
			foreach($taxonomies as $tax_name => $taxonomy) {
				$output .= sprintf("\t".'<option value="%s" %s>%s</option>'."\n",
					esc_attr($tax_name),
					selected($r['selected'], $tax_name, false),
					$taxonomy->label
				);
			}
			$output .= "</select>\n";
		}

		$output = apply_filters('wp_dropdown_taxonomies', $output);

		if ( $echo )
			echo $output;

		return $output;
	}
}

