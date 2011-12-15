<?php


/**
 * Same that get_category_parents() for pages
 * 
 * http://codex.wordpress.org/Function_Reference/get_category_parents
 */
if ( !function_exists('get_page_parents') ) {
	function get_page_parents($id, $link = false, $separator = '/', $nicename = false, $visited = array()) {
		$chain = '';
		$parent = &get_page( $id );
		if ( is_wp_error( $parent ) )
			return $parent;
		
		if ( $nicename )
			$name = $parent->post_slug;
		else
			$name = apply_filters('the_title', $parent->post_title);
		
		if ( $parent->post_parent && ( $parent->post_parent != $parent->ID ) && !in_array( $parent->post_parent, $visited ) ) {
			$visited[] = $parent->post_parent;
			$chain .= get_page_parents( $parent->post_parent, $link, $separator, $nicename, $visited );
		}
		
		if ( $link )
			$chain .= '<a href="' . get_page_link($id) . '">' . $name . '</a>' . $separator;
		else
			$chain .= $name.$separator;
		return $chain;
	}
}
