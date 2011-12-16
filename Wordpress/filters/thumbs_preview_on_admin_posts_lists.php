<?php


/**
 * Thumbs preview on admin posts lists
 */
class Thumbs_Preview_On_Admin_Posts_Lists {
	
	public $column_id    = 'post-thumbnail';
	public $column_title = 'Thumbnail';
	public $column_style = 'width:10%;';
	public $thumb_size   = array(60, 60);
	
	public function __construct() {
		add_action('admin_init', array(&$this, 'action_init'), 999);
	}
	public function Thumbs_Preview_On_Admin_Posts_Lists() {
		$this->__construct();
	}
	
	public function action_init() {
		foreach(get_post_types() as $post_type) {
			if (post_type_supports($post_type, 'thumbnail')) {
				add_filter("manage_{$post_type}_posts_columns", array(&$this, 'filter_manage_posts_columns'));
				add_filter("manage_{$post_type}_posts_custom_column", array(&$this, 'filter_manage_posts_custom_column'), 10, 2);
			}
		}
	}
	
	public function action_admin_footer() {
		printf("\n<!-- %s -->\n", __CLASS__);
		printf('<style type="text/css">th#%s{ %s }</style>', $this->column_id, $this->column_style);
		printf("\n<!-- /%s -->\n", __CLASS__);
	}
	
	public function filter_manage_posts_columns($columns) {
		add_action('admin_footer', array(&$this, 'action_admin_footer'));
		$out = array_slice($columns, 0, 1, true);
		$out[ $this->column_id ] = __($this->column_title);
		$out += $columns;
		return $out;
	}
	
	public function filter_manage_posts_custom_column($column_name, $id) {
		if ($column_name == $this->column_id) {
			$post_thumbnail_id = get_post_thumbnail_id( $id );
			if ( $post_thumbnail_id ) {
				$post_thumbnail =  wp_get_attachment_image($post_thumbnail_id, $this->thumb_size);
				printf('<a href="%s">%s</a>',
					get_admin_url() .'media.php?action=edit&attachment_id='. $post_thumbnail_id,
					$post_thumbnail
				);
			} else {
				print('<span class="no-post-thumbnail"></span>');
			}
		}
	}
	
}
new Thumbs_Preview_On_Admin_Posts_Lists();
