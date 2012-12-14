<?php



add_filter('breadcrumb_current_item', 'breadcrumb_bp_current_item');
function breadcrumb_bp_current_item($item)
{
	$params = array(
		'bp_component'         => bp_current_component(),
		'bp_item'              => bp_current_item(),
		'bp_action'            => bp_current_action(),
		'bp_action_variables'  => bp_action_variables(),
	);

	switch(bp_current_component())
	{
		case false:
			return $item;
		break;

		case 'groups':
			return new Breadcrumb_BP_Component_Group($item, $params);
		break;

		default:
			return new Breadcrumb_BP_Component($item, $params);
	}
}



add_filter('breadcrumb_item_title', 'breadcrumb_bp_item_title', 10, 2);
function breadcrumb_bp_item_title($title, $item)
{
	if ($item instanceof Breadcrumb_BP_Item)
		$title = preg_replace( '|<span>[0-9]+</span>|', '', $title );

	return $title;
}




/**
 * Common
 */

abstract class Breadcrumb_BP_Item extends Breadcrumb_Item
{

	protected function _bp_displayed_user()
	{
		global $bp;
		return ( ! empty($bp->displayed_user) && isset($bp->displayed_user->id)) ? $bp->displayed_user : null;
	}

}



class Breadcrumb_BP_Component extends Breadcrumb_BP_Item
{

	public function title()
	{
		if ( ! $this->_bp_displayed_user())
			$title = $this->item->title();

		elseif (($nav = $this->_bp_component_nav()) &&  ! $this->_bp_is_default_component_and_action())
			$title = $nav['name'];

		else
			$title = '';

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		if ( ! $this->_bp_displayed_user())
			$url = $this->item->url();

		elseif (($nav = $this->_bp_component_nav()) &&  ! $this->_bp_is_default_component_and_action())
			$url = $nav['link'];

		else
			$url = '';

		return apply_filters('breadcrumb_item_title', $url, $this);
	}


	public function child_item()
	{
		$child = null;

		if ($this->params['bp_action'])
			$child = new Breadcrumb_BP_Component_Action($this->item, array('component' => $this) + $this->params);

		return apply_filters('breadcrumb_item_child', $child, $this);
	}

	public function parent_item()
	{
		if ($this->_bp_displayed_user())
			$parent = new Breadcrumb_BP_Displayed_User($this->item, $this->params);
		else
			$parent = $this->item->parent_item();

		return apply_filters('breadcrumb_item_parent', $parent, $this);
	}


	protected function _bp_component_nav()
	{
		global $bp;
		$component = $this->params['bp_component'];

		foreach((array) $bp->bp_nav as $nav)
		{
			if ($nav['slug'] == $component)
				return $nav;
		}

		return array();
	}

	protected function _bp_component_options_nav()
	{
		global $bp;
		$component = $this->params['bp_component'];

		return (isset($bp->bp_options_nav[$component]) ? $bp->bp_options_nav[$component] : array());
	}


	protected function _bp_default_component()
	{
		global $bp;
		return $bp->default_component;
	}

	protected function _bp_is_default_component()
	{
		return ($this->_bp_default_component() == $this->params['bp_component']);
	}


	protected function _bp_component_action_nav()
	{
		global $bp;
		$action = $this->params['bp_action']; //bp_current_action();

		foreach($this->_bp_component_options_nav() as $option_nav)
		{
			if ($option_nav['slug'] == $action)
				return $option_nav;
		}
		return array();
	}

	protected function _bp_default_component_action()
	{
		$component_nav = $this->_bp_component_nav();
		return ($component_nav ? $component_nav['default_subnav_slug'] : null);
	}

	protected function _bp_is_default_component_action()
	{
		return ($this->params['bp_action'] == $this->_bp_default_component_action());
	}


	protected function _bp_is_default_component_and_action()
	{
		return ($this->_bp_is_default_component() && $this->_bp_is_default_component_action());
	}

}



class Breadcrumb_BP_Component_Action extends Breadcrumb_BP_Component
{

	public function title()
	{
		if (($nav = $this->_bp_component_action_nav()) &&  ! $this->_bp_is_default_component_action())
			$title = $nav['name'];

		else
			$title = '';

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		if (($nav = $this->_bp_component_action_nav()) &&  ! $this->_bp_is_default_component_action())
			$url = $nav['link'];

		else
			$url = '';

		return apply_filters('breadcrumb_item_title', $url, $this);
	}


	public function child_item()
	{
		return apply_filters('breadcrumb_item_child', null, $this);
	}

}



/**
 * Displayed_user
 */

class Breadcrumb_BP_Displayed_User extends Breadcrumb_BP_Component
{

	public function title()
	{
		$displayed_user = $this->_bp_displayed_user();
		$title = bp_core_get_user_displayname($displayed_user->id);

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$displayed_user = $this->_bp_displayed_user();
		$url = bp_core_get_user_domain($displayed_user->id);

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function parent_item()
	{
		return apply_filters('breadcrumb_item_parent', $this->item, $this);
	}

}



/** 
 * Groups
 */

class Breadcrumb_BP_Component_Group extends Breadcrumb_BP_Component
{

	public function child_item()
	{
		$child = null;

		if ($this->params['bp_item'])
			$child = new Breadcrumb_BP_Component_Group_Item($this->item, array('group' => $this) + $this->params);

		return apply_filters('breadcrumb_item_child', $child, $this);
	}

}



class Breadcrumb_BP_Component_Group_Item extends Breadcrumb_BP_Item
{

	public function title()
	{
		$group = $this->_get_group();
		$title = $group->name;

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$group = $this->_get_group();
		$url   = bp_get_group_permalink($group);

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function child_item()
	{
		$child = null;

		//if ($this->params['bp_action'] && $this->params['bp_action'] != $this->_default_action())
		if ($this->params['bp_action'])
			$child = new Breadcrumb_BP_Component_Group_Action($this->item, array('group_item' => $this) + $this->params);

		return apply_filters('breadcrumb_item_child', $child, $this);
	}


	protected function _get_group()
	{
		$group_id = groups_get_id($this->params['bp_item']);
		$group = groups_get_group(compact('group_id'));

		return $group;
	}

	protected function _default_action()
	{
		global $bp;
		return $bp->groups->default_extension;
	}

}



class Breadcrumb_BP_Component_Group_Action extends Breadcrumb_BP_Component_Action
{

	public function child_item()
	{
		$child = null;

		// Forum topic
		if ($this->params['bp_action'] == 'forum' && $this->params['bp_action_variables']
			&&  $this->params['bp_action_variables'][0] == 'topic'
		)
			$child = new Breadcrumb_BP_Component_Group_Forum_Topic($this->item, array('group_action' => $this) + $this->params);

		// Admin Tab (TODO)

		return apply_filters('breadcrumb_item_child', $child, $this);
	}


	protected function _bp_component_options_nav()
	{
		global $bp;
		$component = $this->params['bp_item'];

		return (isset($bp->bp_options_nav[$component]) ? $bp->bp_options_nav[$component] : array());
	}

	protected function _bp_default_component_action()
	{
		global $bp;
		return $bp->groups->default_extension;
	}

}



class Breadcrumb_BP_Component_Group_Forum_Topic extends Breadcrumb_BP_Item
{

	public function title()
	{
		// Topic_page (= paged)
		if (isset($this->params['topic_page']))
			$title = apply_filters('breadcrumb_item_title_paged', sprintf(__('Page %d'), $this->params['topic_page']), $this->params['topic_page'], $this);

		else
		{
			$topic_id = $this->_get_topic_id();
			$title    = get_topic_title($topic_id);
		}

		return apply_filters('breadcrumb_item_title', $title, $this);
	}

	public function url()
	{
		$url = untrailingslashit( $this->params['group_item']->url() )
			. '/forum/'
			. $this->params['bp_action_variables'][0] . '/'
			. $this->params['bp_action_variables'][1] . '/';

		// Topic_page (= paged)
		if (isset($this->params['topic_page']))
		{
			$url .= '?topic_page=' . $this->params['topic_page'];
			if (isset($this->params['num']))
				$url .= '&num=' . $this->params['num'];
		}

		return apply_filters('breadcrumb_item_url', $url, $this);
	}


	public function child_item()
	{
		$child = null;

		// Topic_page (= paged)
		if ( ! isset($this->params['topic_page']) && isset($_GET['topic_page']) && $_GET['topic_page'] > 1)
			$child = new self($this->item, $this->params + $_GET);

		return apply_filters('breadcrumb_item_child', $child, $this);
	}


	protected function _get_topic_id()
	{
		return bp_forums_get_topic_id_from_slug( $this->params['bp_action_variables'][1] );
	}

	protected function _get_topic()
	{
		return get_topic_title($this->_get_topic_id());
	}

}


