<?php


/**
 * Converti une chaine ou un tableau en UTF-8 si nécessaire.
 * @param mixed $data
 * @return mixed 
 */
function to_utf8($data = null) {
	if ( is_string($data) ) {
		if ($data !== mb_convert_encoding( mb_convert_encoding($data, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') ) {
			$data = mb_convert_encoding($data, 'UTF-8');
		}
	} elseif ( is_array($data) ) {
		$data = array_map(__FUNCTION__, $data);
	}
	return $data;
}


?>