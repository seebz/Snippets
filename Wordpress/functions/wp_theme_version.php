<?php


/**
 * Retourne la version du thème extraite depuis l'en-tête de la feuille de style
 */
function wp_theme_version()
{
	if (function_exists('wp_get_theme'))
		$current_theme = wp_get_theme();
	elseif (function_exists('get_theme_data'))
		$current_theme = get_theme_data( get_stylesheet_directory_uri() .'/style.css' );
	else
		return null;

	return ($current_theme && isset($current_theme['Version'])) ? $current_theme['Version'] : null;
}


/**
 * Exemple d'utilisations, versionner la feuille de style
 * (forcer la mise à jour coté client après une modification; implique que la feuille de style soit appellée via la fonction `get_stylesheet_uri()`)

function mytheme_stylesheet_uri($uri)
{
	return $uri . '?ver=' . wp_theme_version();
}
add_filter( 'stylesheet_uri', 'mytheme_stylesheet_uri', 99 );

 */
