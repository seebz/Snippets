<?php


/**
 * Replace all occurrences of the search string with the replacement string in serialized string.
 * @param string $search The value being searched for.
 * @param string $replace The replacement value that replaces found search values.
 * @param string $str The serialized string being searched and replaced on.
 * @return string A serialized string with the replaced values. 
 */
function serialized_str_replace($search, $replace, $str) {
	$split = preg_split('`s:([0-9]+):`', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
	
	$search = '`' . preg_quote($search, '`') . '`';
	
	$out = array();
	while (count($split) > 1) {
		$str_end = array_pop($split);
		$str_len = array_pop($split);
		
		$pattern = '`^"(.{' . $str_len . '})";(.*)$`sm';
		preg_match($pattern, $str_end, $m);
		if (count($m)) {
			$str = preg_replace($search, $replace, $m[1]);
			$str_len = strlen($str);
			$str_end = sprintf('"%s";%s', $str, $m[2]);
		}
		
		$out[] = sprintf('s:%d:%s', $str_len, $str_end);
	}
	$out[] = $split[0];
	
	return implode('', array_reverse($out));
}


/**
 * Case-insensitive version of serialized_str_replace().
 */
function serialized_str_ireplace($search, $replace, $str) {
	$split = preg_split('`s:([0-9]+):`', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
	
	$search = '`' . preg_quote($search, '`') . '`i';
	
	$out = array();
	while (count($split) > 1) {
		$str_end = array_pop($split);
		$str_len = array_pop($split);
		
		$pattern = '`^"(.{' . $str_len . '})";(.*)$`sm';
		preg_match($pattern, $str_end, $m);
		if (count($m)) {
			$str = preg_replace($search, $replace, $m[1]);
			$str_len = strlen($str);
			$str_end = sprintf('"%s";%s', $str, $m[2]);
		}
		
		$out[] = sprintf('s:%d:%s', $str_len, $str_end);
	}
	$out[] = $split[0];
	
	return implode('', array_reverse($out));
}


?>