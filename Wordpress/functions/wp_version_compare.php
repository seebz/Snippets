<?php


/**
 * Compare la version de WordPress avec une chaîne $version passé en paramètre.
 * 
 * Se référer à la doc de la fonction PHP `version_compare` pour les opérateurs
 * possibles et les valeurs de retour.
 * http://php.net/manual/en/function.version-compare.php
 */
function wp_version_compare($version, $operator = null) {
	global $wp_version;
	if (is_null($operator)) {
		return version_compare($wp_version, $version);
	} else {
		return version_compare($wp_version, $version, $operator);
	}
}
