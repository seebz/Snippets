<?php


/**
 * Détermine si une variable est un tableau associatif
 * @param mixed $var La variable à évaluer. 
 * @return boolean Retourne TRUE si var est un tableau associatif, FALSE sinon. 
 */
function is_hash($var) {
	return ( is_array($var) && array_keys($var) !== range(0, count($var)-1) );
}


?>