<?php


/**
 * Add `has_thumbnail` CSS class to Post which have thumbnail
 */
add_filter('post_class','has_thumbnail_post_class', 10, 3);
function has_thumbnail_post_class($classes, $class, $post_id) {
	if ( has_post_thumbnail($post_id) ) {
		$classes[] = 'has_thumbnail';
	}
	return $classes;
}
