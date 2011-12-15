<?php


/**
 * More Widget CSS classes
 * 
 * Add "first-widget", "last-widget" and more CSS classes to widgets
 * 
 * (source: http://wordpress.org/support/topic/how-to-first-and-last-css-classes-for-sidebar-widgets?replies=9)
 */
add_filter('dynamic_sidebar_params','more_css_classes_for_widgets');
function more_css_classes_for_widgets($params) {
	global $my_widget_num;
	$this_id = $params[0]['id'];
	$arr_registered_widgets = wp_get_sidebars_widgets();

	if (!$my_widget_num) {
		$my_widget_num = array();
	}

	if (!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) {
		return $params;
	}

	if (isset($my_widget_num[$this_id])) {
		$my_widget_num[$this_id] ++;
	} else {
		$my_widget_num[$this_id] = 1;
	}

	$class = 'class="widget-' . $my_widget_num[$this_id] . ' ';

	if ($my_widget_num[$this_id] == 1) {
		$class .= 'first-widget ';
	} elseif ($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) {
		$class .= 'last-widget ';
	}

	$params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']);

	return $params;
}
