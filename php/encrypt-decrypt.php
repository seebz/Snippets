<?php


function encrypt($str, $salt=null) {
	$salt = md5($salt);
	$out = '';
	for ($i = 0; $i<strlen($str); $i++) {
		$kc = substr($salt, ($i%strlen($salt)) - 1, 1);
		$out .= chr(ord($str{$i})+ord($kc));
	}
	$out = base64_encode($out);
	$out = str_replace(array('=', '/'), array('', '-'), $out);
	return $out;
}


function decrypt($str, $salt=null) {
	$salt = md5($salt);
	$out = '';
	$str = str_replace('-', '/', $str);
	$str = base64_decode($str);
	for ($i = 0; $i<strlen($str); $i++) {
		$kc = substr($salt, ($i%strlen($salt)) - 1, 1);
		$out .= chr(ord($str{$i})-ord($kc));
	}
	return $out;
}


?>