<?php


/**
 * Same that register_post_type() with archives per date
 *
 * http://codex.wordpress.org/Function_Reference/register_post_type
 */
function register_post_type_with_dates( $post_type, $args = array() )
{
	$inst = new Custom_Post_Type_With_Dates($post_type, $args);
	return $inst->args;
}


/**
 * Internal usage
 */
class Custom_Post_Type_With_Dates
{
	public $post_type;
	public $args;

	public function __construct( $post_type, $args = array() ) {
		$this->post_type = $post_type;
		$this->args = $args;
		$this->args = $this->_register_post_type();

		add_action('rewrite_rules_array', array(&$this, 'rewrite_rules_array'), 100);
		add_action('wp_loaded', array(&$this, 'wp_loaded'));
		add_filter('date_template', array(&$this, 'date_template'));
	}

	protected function _register_post_type() {
		return register_post_type($this->post_type, $this->args);
	}

	public function rewrite_rules_array( $rules ) {
		if ( ! empty($this->args->has_archive) && is_string($this->args->has_archive)) {
			$slug = $this->args->has_archive;
		} else {
			$slug = $this->post_type;
		}
		$new_rules = $this->_get_new_rules();

		return ($new_rules + $rules);
	}

	public function wp_loaded() {
		$test_key = current(array_keys($this->_get_new_rules()));
		$rules = (array) get_option('rewrite_rules');
		if ( ! array_key_exists($test_key, $rules)) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
	}

	public function date_template( $template ) {
		$templates = array();
		if (get_query_var('post_type') == $this->post_type) {
			$templates[] = "date-{$this->post_type}.php"; 
			$templates[] = "archive-{$this->post_type}.php"; 
		}
		$templates[] = $template;

		return locate_template( $templates );
	}

	protected function _get_new_rules() {
		$post_type = $this->post_type;
		if (isset($this->args->has_archive) && is_string($this->args->has_archive)) {
			$slug = $this->args->has_archive;
		} else {
			$slug = $post_type;
		}

		return array(
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/([0-9]{1,2})/?$" => 'index.php?year=$matches[1]&monthnum=$matches[2]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$" => 'index.php?year=$matches[1]&feed=$matches[2]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$" => 'index.php?year=$matches[1]&feed=$matches[2]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/page/?([0-9]{1,})/?$" => 'index.php?year=$matches[1]&paged=$matches[2]' . '&post_type=' .  $post_type,
			"{$slug}/([0-9]{4})/?$" => 'index.php?year=$matches[1]' . '&post_type=' .  $post_type,
		);
	}
}
