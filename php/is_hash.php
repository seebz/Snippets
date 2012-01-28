<?php


/**
 * Détermine si une variable est un tableau associatif
 * @param mixed $var
 * @return boolean 
 */
function is_hash($var) {
	return ( is_array($var) && array_keys($var) !== range(0, count($var)-1) );
}


?>