<?php



/**
 * Inject post date to the breadcrumb for singular posts
 */

//add_filter('breadcrumb_item_parent', 'breadcumb_inject_post_date', 10, 2);
function breadcumb_inject_post_date($parent, $item)
{
	if (is_singular('post')
		&& $parent instanceof Breadcrumb_Item_Archive
		&& $item instanceof Breadcrumb_Item_Singular
	)
	{
		$post_time = strtotime($item->item->post_date);
		$parent = new Breadcrumb_Item_Archive($item->item->post_type, array(
				'year'  => date('Y', $post_time),
				'month' => date('m', $post_time),
				'day'   => date('d', $post_time),
			));
	}

	return $parent;
}



/**
 * Inject post category to the breadcrumb for singular posts
 */

//add_filter('breadcrumb_item_parent', 'breadcrumb_inject_post_category', 10, 2);
function breadcrumb_inject_post_category($parent, $item)
{
	if (is_singular('post')
		&& $parent instanceof Breadcrumb_Item_Archive
		&& $item instanceof Breadcrumb_Item_Singular
	)
	{
		$level = 0; $cat = null;

		foreach(wp_get_post_categories($item->item->ID) as $cat_id)
		{
			$cat_parents = get_category_parents($cat_id, false, '[SEPARATOR]');
			$cat_level   = count(explode('[SEPARATOR]', $cat_parents)) - 1;
			if ($cat_level > $level)
			{
				$level = $cat_level;
				$cat   = $cat_id;
			}
		}
		if ($cat)
		{
			$parent = new Breadcrumb_Item_Taxonomy(get_category($cat));
		}
	}

	return $parent;
}



/**
 * Breadcrumb for Foundation CSS framework
 * (http://foundation.zurb.com/)
 */

//add_action('init', 'breadcumb_init_foundation');
function breadcumb_init_foundation()
{
	add_filter('breadcrumb_defaults', 'breadcrumb_foundation_defaults');
	add_filter('breadcrumb_container_allowedtags', 'breadcrumb_foundation_container_allowedtags');
	add_filter('breadcrumb_item_output', 'breadcrumb_foundation_item_output', 10 ,4);
}


function breadcrumb_foundation_defaults($defaults = array())
{
	return array(
		'container'        => 'nav',
		'items_wrap'       => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'breadcrumb_class' => 'breadcrumbs',
		'before'           => '<li>',
		'after'            => '</li>',
		'separator'        => ' ',
	) + $defaults;
}

function breadcrumb_foundation_container_allowedtags($tags)
{
	$tags[] = 'ul';
	return $tags;
}

function breadcrumb_foundation_item_output($item_output, $item, $args, $items)
{
	if ($item == end($items))
		return sprintf('<li class="current">%s</li>', strip_tags($item_output, '<a>'));

	return $item_output;
}


