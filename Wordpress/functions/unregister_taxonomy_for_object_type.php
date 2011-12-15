<?php


/**
 * Opposite of register_taxonomy_for_object_type()
 * 
 * http://codex.wordpress.org/Function_Reference/register_taxonomy_for_object_type
 */
if ( !function_exists('unregister_taxonomy_for_object_type') ) {
	function unregister_taxonomy_for_object_type( $taxonomy, $object_type) {
		global $wp_taxonomies;

		if ( !isset($wp_taxonomies[$taxonomy]) )
			return false;

		if ( ! get_post_type_object($object_type) )
			return false;

		$key = array_search($object_type, $wp_taxonomies[$taxonomy]->object_type);
		if ($key !== false) {
			unset( $wp_taxonomies[$taxonomy]->object_type[ $key ] );
			return true;
		}

		return false;
	}
}
