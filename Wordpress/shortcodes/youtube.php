<?php


/**
 * Youtube Shortcode
 * 
 * Usage: [youtube id="oHg5SJYRHA0"]
 *        [youtube id="oHg5SJYRHA0" width="480" height="360"]
 *        [youtube id="oHg5SJYRHA0" width="480" height="360" align="right"]
 */
add_shortcode('youtube', 'shortcode_youtube');
function shortcode_youtube($atts) {
	extract(shortcode_atts(array(
		'id'     => '',
		'width'  => '420',
		'height' => '315',
		'align' => '',
	), $atts));
	$align_class = ($align ? 'align' . $align : '');
	return sprintf('<iframe class="youtube %s" src="http://www.youtube.com/embed/%s" width="%d" height="%d" frameborder="0" allowfullscreen></iframe>',
		$align_class,
		$id,
		$width,
		$height
	);
}
