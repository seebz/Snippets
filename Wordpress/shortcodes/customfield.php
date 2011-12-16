<?php


/**
 * CustomField Shortcode
 * 
 * Usage: [customfield key="name"]
 *        [customfield key="name" post_id="42" single="false"]
 */
add_shortcode('customfield', 'shortcode_customfield');
function shortcode_customfield($atts) {
	extract(shortcode_atts(array(
		'post_id' => '',
		'key'     => '',
		'single'  => true,
	), $atts));
	if (!$post_id) {
		global $post;
		$post_id = $post->ID;
	}
	if ($single !== true && $single === 'false') {
		$single = false;
	}
	return get_post_meta($post_id, $key, $single);
}
