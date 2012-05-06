<?php


/**
 * PDFdoc Shortcode
 * 
 * Usage: [pdfdoc url="http://www.example.com/document.pdf"]
 *        [pdfdoc url="http://www.example.com/document.pdf" width="480" height="360"]
 */
add_shortcode('pdfdoc', 'shortcode_pdfdoc');
function shortcode_pdfdoc($atts) {
	extract(shortcode_atts(array(
		'url'    => '',
		'width'  => '600',
		'height' => '700',
	), $atts));
	return sprintf('<iframe class="pdfdoc" src="http://docs.google.com/viewer?url=%s&embedded=true" width="%s" height="%s" style="border:none;"></iframe>',
		urlencode($url),
		$width,
		$height
	);
}
