<?php


if (function_exists('breadcrumb')) return;



function breadcrumb($args = array())
{
	// Args
	$defaults = apply_filters('breadcrumb_defaults', array(
		'container'        => 'div', 
		'container_class'  => 'breadcrumb-container', 
		'container_id'     => '',
		'breadcrumb_class' => 'breadcrumb',
		'breadcrumb_id'    => 'breadcrumb',
		'before'           => '',
		'after'            => '',
		'link_before'      => '',
		'link_after'       => '',
		'items_wrap'       => '<nav id="%1$s" class="%2$s">%3$s</nav>',
		'separator'        => ' » ',
		'echo'             => true,
	));
	$args = wp_parse_args($args, $defaults);
	$args = apply_filters('breadcrumb_args', $args);
	$args = (object) $args;


	// Current item
	if (is_tax() || is_category() || is_tag())
		$current= new Breadcrumb_Item_Taxonomy();

	elseif (is_archive() || is_home() || is_front_page())
		$current = new Breadcrumb_Item_Archive();

	elseif (is_singular())
		$current = new Breadcrumb_Item_Singular();

	elseif (is_search())
		$current = new Breadcrumb_Item_Search();

	elseif (is_404())
		$current = new Breadcrumb_Item_404();

	else
		$current = null;

	$current = apply_filters('breadcrumb_current_item', $current, $args);
	if ( ! $current) return;


	// Items
	$breadcrumb_items = array_merge(
			$current->parents(),
			array($current),
			$current->children()
		);

	$breadcrumb_items = apply_filters('breadcrumb_objects', $breadcrumb_items, $args);


	// Pre-output
	$breadcrumb = $items = '';

	$show_container = false;
	if ( $args->container ) {
		$allowed_tags = apply_filters( 'breadcrumb_container_allowedtags', array( 'div', 'nav' ) );
		if ( in_array( $args->container, $allowed_tags ) ) {
			$show_container = true;
			$class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : '';
			$id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
			$breadcrumb .= '<'. $args->container . $id . $class . '>';
		}
	}

	$items .= breadcrumb_items( $breadcrumb_items, $args );
	unset($breadcrumb_items);

	$wrap_id    = $args->breadcrumb_id    ? $args->breadcrumb_id    : '';
	$wrap_class = $args->breadcrumb_class ? $args->breadcrumb_class : '';

	$items = apply_filters( 'breadcrumb_items', $items, $args );

	// Output
	$breadcrumb .= sprintf( $args->items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $items );
	unset( $items );

	if ( $show_container )
		$breadcrumb .= '</' . $args->container . '>';


	$breadcrumb = apply_filters( 'breadcrumb', $breadcrumb, $args );

	if ( $args->echo )
		echo $breadcrumb;
	else
		return $breadcrumb;
}



function breadcrumb_items($items, $args)
{
	$first_item = reset($items);
	$last_item  = end($items);

	$output = array();
	foreach ($items as $item)
	{
		$item_classes = array();
		if ($item == $first_item)
			$item_classes[] = 'first-item';
		if ($item == $last_item)
			$item_classes[] = 'last-item';
		$classes = apply_filters('breadcrumb_item_classes', $item_classes, $item, $args, $items);

		$attributes = ' href="' . esc_attr( $item->url()) . '"';
		if ( ! empty($item_classes))
			$attributes .= ' class="' . implode(' ', $item_classes) . '"';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . $item->title() . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output[] = apply_filters('breadcrumb_item_output', $item_output, $item, $args, $items);
	}
	$output = implode($args->separator, $output);

	return $output;
}



abstract class Breadcrumb_Item
{

	public $item   = null;
	public $params = array();


	public function __construct($item = null, $params = array())
	{
		$this->set_item($item);
		$this->set_params($params);
	}

	public function set_item($item = null)
	{
		$this->item = $item;
	}

	public function set_params($params = array())
	{
		$this->params = $params;
	}


	public function title()
	{
		return apply_filters('breadcrumb_item_title', '', $this);
	}

	public function url()
	{
		return apply_filters('breadcrumb_item_url', '', $this);
	}


	public function parent_item()
	{
		return apply_filters('breadcrumb_item_parent', null, $this);
	}

	public function child_item()
	{
		return apply_filters('breadcrumb_item_child', null, $this);
	}

	public function parents() {
		$parents = array();

		$item = $this;
		while($item = $item->parent_item())
			$parents[] = $item;

		return apply_filters('breadcrumb_item_parents', array_reverse($parents), $this);
	}

	public function children() {
		$children = array();

		$item = $this;
		while($item = $item->child_item())
			$children[] = $item;

		return apply_filters('breadcrumb_item_children', $children, $this);
	}

}



class Breadcrumb_Item_Taxonomy extends Breadcrumb_Item
{

	public function set_item($item = null)
	{
		if (is_null($item))
			$item = get_queried_object();

		$this->item   = $item;
	}

	public function set_params($params = array())
	{
		$params += array(
			'paged' => null,
		);

		$this->params = $params;
	}


	public function title()
	{
		// Paged
		if ($this->params['paged'])
			$title = apply_filters('breadcrumb_item_title_paged', sprintf(__('Page %d'), $this->params['paged']), $this->params['paged'], $this);

		elseif ( is_category() )
			$title = apply_filters( 'single_cat_title', $this->item->name );
		elseif ( is_tag() )
			$title = apply_filters( 'single_tag_title', $this->item->name );
		else
			$title = apply_filters( 'single_term_title', $this->item->name );

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$url = get_term_link($this->item);

		// Paged
		if ($this->params['paged'])
			$url .= 'page/' . $this->params['paged'] . '/';

		return apply_filters('breadcrumb_item_url', $url, $this);
	}

	public function parent_item()
	{
		// Paged
		if ($this->params['paged'])
			$parent = new self($this->item, array('paged' => null) + $this->params);

		// Parent term
		elseif (is_taxonomy_hierarchical($this->item->taxonomy) && $this->item->parent)
			$parent = new self( get_term($this->item->parent, $this->item->taxonomy) );

		// Archive
		else
			$parent = new Breadcrumb_Item_Archive(get_post_type());

		return apply_filters('breadcrumb_item_parent', $parent, $this);
	}

	public function child_item()
	{
		// Paged
		if ( ! $this->params['paged'] && get_query_var('paged'))
			$child = new self($this->item,array('paged' => get_query_var('paged')) + $this->params);

		else
			$child = null;

		return apply_filters('breadcrumb_item_child', $child, $this);
	}

}



class Breadcrumb_Item_Archive extends Breadcrumb_Item
{

	public function set_item($item = null)
	{
		if (is_null($item))
			$item = get_post_type();
		if (is_string($item))
			$item = get_post_type_object($item);

		$this->item   = $item;
	}

	public function set_params($params = array())
	{
		$params += array(
			'year'  => null,
			'month' => null,
			'day'   => null,
			'paged' => null,
		);

		if ($params['month'])
			$params['month'] = zeroise($params['month'], 2);
		if ($params['day'])
			$params['day'] = zeroise($params['day'], 2);

		$this->params = $params;
	}


	public function title()
	{
		// Params
		if ($this->params['paged'])
			$title = apply_filters('breadcrumb_item_title_paged', sprintf(__('Page %d'), $this->params['paged']), $this->params['paged'], $this);
		elseif ($this->params['day'])
			$title = $this->params['day'];
		elseif ($this->params['month'])
			$title = $this->_month_i18n($this->params['month']);
		elseif ($this->params['year'])
			$title = $this->params['year'];

		elseif ($this->_is_front_page())
			$title = apply_filters('breadcrumb_front_page_title', __('Home'));
		elseif ($this->_is_home() && get_option('page_for_posts'))
			$title = get_the_title(get_option('page_for_posts'));
		else
			$title = apply_filters('post_type_archive_title', $this->item->labels->name );

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		if ($this->_is_front_page())
			$url = home_url('/');
		elseif ($this->_is_home())
			$url = get_permalink( get_option('page_for_posts') );
		else
			$url = get_post_type_archive_link($this->item->name);

		// Params
		if ($this->params['year'])
			$url .= $this->params['year'] . '/';

		if ($this->params['month'])
			$url .= $this->params['month'] . '/';

		if ($this->params['day'])
			$url .= $this->params['day'] . '/';

		if ($this->params['paged'])
			$url .= 'page/' . $this->params['paged'] . '/';

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function parent_item()
	{
		// Params
		if ($this->params['paged'])
			$parent = new self($this->item, array('paged' => null) + $this->params);

		elseif ($this->params['day'])
			$parent = new self($this->item, array('day' => null) + $this->params);

		elseif ($this->params['month'])
			$parent = new self($this->item, array('month' => null) + $this->params);

		elseif ($this->params['year'])
			$parent = new self($this->item, array('year' => null) + $this->params);

		// Default
		elseif ( ! $this->_is_front_page())
			$parent = new self('page');
		else
			$parent = null;

		return apply_filters('breadcrumb_item_parent', $parent, $this);
	}

	public function child_item()
	{
		// Params
		if ( ! $this->params['year'] && get_query_var('year'))
			$child = new self($this->item, array('year' => get_query_var('year')) + $this->params);

		elseif ( ! $this->params['month'] && get_query_var('monthnum'))
			$child = new self($this->item, array('month' => get_query_var('monthnum')) + $this->params);

		elseif ( ! $this->params['day'] && get_query_var('day'))
			$child = new self($this->item, array('day' => get_query_var('day')) + $this->params);

		elseif ( ! $this->params['paged'] && get_query_var('paged'))
			$child = new self($this->item, array('paged' => get_query_var('paged')) + $this->params);

		else
			$child = null;

		return apply_filters('breadcrumb_item_child', $child, $this);
	}


	protected function _is_home()
	{
		return ($this->item->name == 'post');
	}

	protected function _is_front_page()
	{
		return ($this->item->name == 'page' || ($this->item->name == 'post' && ! get_option('page_for_posts')));
	}

	protected function _month_i18n($num)
	{
		global $wp_locale;
		return $wp_locale->get_month($num);
	}
}



class Breadcrumb_Item_Singular extends Breadcrumb_Item
{

	public function set_item($item = null)
	{
		if (is_null($item))
			$item = get_the_ID();
		if (is_numeric($item))
			$item = get_post($item);

		$this->item   = $item;
	}

	public function set_params($params = array())
	{
		$params += array(
			'paged' => null,
		);

		$this->params = $params;
	}


	public function title()
	{
		// Paged
		if ($this->params['paged'])
			$title = apply_filters('breadcrumb_item_title_paged', sprintf(__('Page %d'), $this->params['paged']), $this->params['paged'], $this);

		else
			$title = $this->item->post_title;

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$url = get_permalink($this->item->ID);

		// Paged
		if ($this->params['paged'])
			$url .= 'page/' . $this->params['paged'] . '/';

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function parent_item()
	{
		// Paged
		if ($this->params['paged'])
			$parent = new self($this->item, array('paged' => null) + $this->params);

		// Parent post
		elseif ( $this->item->post_parent )
			$post_parent = new self($this->item->post_parent);

		// Archive
		else
			$post_parent = new Breadcrumb_Item_Archive($this->item->post_type);

		return apply_filters('breadcrumb_item_parent', $post_parent, $this);
	}

	public function child_item()
	{
		// Paged
		if ( ! $this->params['paged'] && get_query_var('paged'))
		{
			$params = array('paged' => get_query_var('paged')) + $this->params;
			$child = new self($this->item, $params);
		}

		else
			$child = null;

		return apply_filters('breadcrumb_item_child', $child, $this);
	}

}



class Breadcrumb_Item_Search extends Breadcrumb_Item
{

	public function set_params($params = array())
	{
		$params += array(
			'paged'     => null,
		);

		if ( ! isset($params['s']))
			$params['s'] = get_search_query();

		if ( ! isset($params['post_type']))
			$params['post_type'] = get_query_var('post_type');
		if ($params['post_type'] == 'any')
			$params['post_type'] = 'page';

		$this->params = $params;
	}


	public function title()
	{
		// Paged
		if ($this->params['paged'])
			$title = apply_filters('breadcrumb_item_title_paged', sprintf(__('Page %d'), $this->params['paged']), $this->params['paged'], $this);

		else
			$title = apply_filters('breadcrumb_item_search_title', sprintf(__('Search Results %1$s %2$s'), ':', $this->params['s']), $this);

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$url = get_search_link();

		// Paged
		if ($this->params['paged'])
			$url .= 'page/' . $this->params['paged'] . '/';

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function parent_item()
	{
		// Params
		if ($this->params['paged'])
			$parent = new self($this->item, array('paged' => null) + $this->params);

		// Archive
		else
			$post_parent = new Breadcrumb_Item_Archive($this->params['post_type']);

		return apply_filters('breadcrumb_item_parent', $post_parent, $this);
	}

	public function child_item()
	{
		// Paged
		if ( ! $this->params['paged'] && get_query_var('paged'))
		{
			$params = array('paged' => get_query_var('paged')) + $this->params;
			$child = new self($this->item, $params);
		}

		else
			$child = null;

		return apply_filters('breadcrumb_item_child', $child, $this);
	}

}



class Breadcrumb_Item_404 extends Breadcrumb_Item
{

	public function title()
	{
		$title = apply_filters('breadcrumb_item_404_title', __('Page not found'));

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$url = $_SERVER['REQUEST_URI'];

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function parent_item()
	{
		$post_parent = new Breadcrumb_Item_Archive('page');

		return apply_filters('breadcrumb_item_parent', $post_parent, $this);
	}

}


