<?php


function whois_domain($domain) {
	if (!is_string($domain) || !preg_match('`^[a-z]{1,}([-\.][a-z]+)+$`i', $domain)) {
		return false; // not a valid domain ?
	}
	
	function query_server($domain, $server = 'whois.iana.org') {
		$response = '';
		if ($fsk = fsockopen($server, 43, $errno, $errstr, 2)) {
			fputs($fsk, $domain . "\r\n"); // CRLF as per RFC3912
			while (!feof($fsk)) {
				$response .= fgets($fsk, 1024);
			}
			@fclose($fsk);
		}
		if (preg_match('`^[\s]*whois[\s]*(?:server[\s]*)?:[\s]*([-a-z\.]+)[\s]*$`mi', $response, $m)) {
			$server = $m[1];
			return $server;
		} else {
			return $response;
		}
	}
	
	$response_or_next_server = 'whois.iana.org';
	
	$i = 5; // max 5 queries for avoid loop
	while(preg_match('`^[-a-z\.]+$`', $response_or_next_server)) {
		if ( !($i--) ) { 
			return null; 
		}
		$response_or_next_server = query_server($domain, $response_or_next_server);
	}
	
	return $response_or_next_server;
}
