<?php


/**
 * Snap Shortcode
 * 
 * Usage: [snap url="http://www.example.com/"]
 *        [snap url="http://www.example.com/" width="120" height="80" align="right"]
 * (source: http://www.geekeries.fr/snippet/creer-automatiquement-miniatures-sites-wordpress/)
 */
add_shortcode('snap', 'shortcode_snap');
function shortcode_snap($atts) {
	extract(shortcode_atts(array(
		'url'    => '',
		'width'  => '400',
		'height' => '300',
		'align'  => 'left',
	), $atts));
	$align_class = 'align' . $align;
	return sprintf('<img class="snap %s" src="http://s.wordpress.com/mshots/v1/%s?w=%d&amp;h=%d" width="%3$d" height="%4$d" alt="" />',
		$align_class,
		urlencode($url),
		$width,
		$height
	);
}
