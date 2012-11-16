<?php
/*
Plugin Name: GG Authorship
Description: Implémente le <em>Google Authorship</em>.
Version:     1.0
Author:      Seebz
Author URI:  http://seebz.net
*/


class Google_Authorship
{

	const USER_META = 'google_plus';


	public function __construct()
	{
		add_action('wp_head', array($this, 'print_meta'), 1);
		add_filter('user_contactmethods', array($this, 'add_profile_field'));
	}

	public function add_profile_field($fields)
	{
		$fields[ self::USER_META ] = 'Google+';
		return $fields;
	}

	public function print_meta()
	{
		if (is_singular())
		{
			global $post;

			$author_id = $post->post_author;
			$author_gplus_url = get_user_meta($author_id, self::USER_META, true);
			if ($author_gplus_url)
			{
				printf("\n". '<link rel="author" href="%s"/>' . "\n",
					esc_attr($author_gplus_url));
			}
		}
	}

}


$GLOBALS['google_authorhip'] = new Google_Authorship();


?>