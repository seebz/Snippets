<?php


/**
 * Youtube Shortcode
 * 
 * Usage: [youtube id="oHg5SJYRHA0"]
 *        [youtube id="oHg5SJYRHA0" width="480" height="360"]
 */
add_shortcode('youtube', 'shortcode_youtube');
function shortcode_youtube($atts) {
	extract(shortcode_atts(array(
		'id'     => '',
		'width'  => '420',
		'height' => '315',
	), $atts));
	return sprintf('<iframe src="http://www.youtube.com/embed/%s" width="%d" height="%d" frameborder="0" allowfullscreen></iframe>',
		$id,
		$width,
		$height
	);
}
