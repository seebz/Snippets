<?php


/**
 * Retrieve or display list of post type as a dropdown (select list).
 */
if ( !function_exists('wp_dropdown_post_types') ) {
	function wp_dropdown_post_types($args = '') {
		$defaults = array(
			'selected' => 0, 'echo' => 1,
			'name' => 'post_type', 'id' => '',
			'show_option_none' => '', 'show_option_no_change' => '',
			'option_none_value' => ''
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$post_types = get_post_types(array('public'=>true, 'show_ui'=>true), 'objects');

		$output = '';
		// Back-compat with old system where both id and name were based on $name argument
		if ( empty($id) )
			$id = $name;

		if ( ! empty($post_types) ) {
			$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
			if ( $show_option_no_change )
				$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
			if ( $show_option_none )
				$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
			foreach($post_types as $type_name => $post_type) {
				$output .= sprintf("\t".'<option value="%s" %s>%s</option>'."\n",
					esc_attr($type_name),
					selected($r['selected'], $type_name, false),
					$post_type->label
				);
			}
			$output .= "</select>\n";
		}

		$output = apply_filters('wp_dropdown_post_types', $output);

		if ( $echo )
			echo $output;

		return $output;
	}
}
