<?php


/**
 * Bloginfo Shortcode
 * 
 * Usage: [bloginfo key="name"]
 * (source: http://css-tricks.com/snippets/wordpress/bloginfo-shortcode/)
 */
add_shortcode('bloginfo', 'shortcode_bloginfo');
function shortcode_bloginfo($atts) {
	extract(shortcode_atts(array(
		'key' => '',
	), $atts));
	return get_bloginfo($key);
}
