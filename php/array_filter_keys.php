<?php


/**
 * Filtre les éléments d'un tableau en utilisant les clés grâce à une fonction utilisateur
 * @param  array    $array    Le tableau à évaluer.
 * @param  callable $callback La fonction utilisateur à utiliser.
 * @return array    Le tableau filtré.
 */
function array_filter_keys(array $array, callable $callback = null)
{
	$keys = array_keys($array);
	$keys = $callback 
		? array_filter($keys, $callback)
		: array_filter($keys);

	$out = array();
	foreach ($keys as $key) {
		$out[$key] = $array[$key];
	}
	return $out;
}

