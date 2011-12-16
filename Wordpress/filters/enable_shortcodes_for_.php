<?php


/**
 * Enable Shortcodes for excerpts
 */
function __wp_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		//$text = strip_shortcodes( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}
$wp_filter['get_the_excerpt'][10]['wp_trim_excerpt']['function'] = '__wp_trim_excerpt';
add_filter('get_the_excerpt', 'do_shortcode');


/**
 * Enable Shortcodes for titles
 */
add_filter('single_post_title', 'do_shortcode');
add_filter('the_title', 'do_shortcode');
add_filter('wp_title', 'do_shortcode');
