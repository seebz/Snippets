<?php


/**
 * More Menu Item CSS classes
 * 
 * Add "first-menu-item" and "last-menu-item" CSS classes to menu items
 */
class MoreCssClassesForMenuItems {
	protected $_menus_items = array();
	
	public function __construct() {
		add_filter('nav_menu_css_class', array(&$this, 'store_orders'), 10, 3);
		add_filter('wp_nav_menu_items', array(&$this, 'add_classes'), 10, 2);
	}
	
	public function store_orders($cls, $item, $menu_args) {
		$m = serialize($menu_args);
		if ( !isset($this->_menus_items[ $m ]) || !isset($this->_menus_items[ $m ][ $item->menu_item_parent ]) 
			|| !is_array($this->_menus_items[ $m ][ $item->menu_item_parent ]) ) {
			$this->_menus_items[ $m ][ $item->menu_item_parent ] = array();
		}
		$this->_menus_items[ $m ][ $item->menu_item_parent ][] = $item->ID;
		return $cls;
	}
	
	public function add_classes($html, $menu_args) {
		$m = serialize($menu_args);
		if (isset($this->_menus_items[ $m ]) && is_array($this->_menus_items[ $m ])) {
			foreach($this->_menus_items[ $m ] as $level) {
				$last = end($level);
				$html = preg_replace(
						'`class="([^"]*menu-item-'. $last .'([^"0-9][^"]*)*")`U',
						'class="last-menu-item $1',
						$html
					);
				$first = reset($level);
				$html = preg_replace(
						'`class="([^"]*menu-item-'. $first .'([^"0-9][^"]*)*")`U',
						'class="first-menu-item $1',
						$html
					);
			}
			$this->_menus_items[ $m ] = array();
		}
		return $html;
	}
}
new MoreCssClassesForMenuItems();
