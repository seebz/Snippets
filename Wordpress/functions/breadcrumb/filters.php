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


