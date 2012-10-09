<?php



if ( ! function_exists('apache_request_headers')) :
	/**
	 * Fetch all HTTP request headers
	 * @link http://php.net/manual/en/function.apache-request-headers.php
	 * @return array An associative array of all the HTTP headers in the current request, or FALSE on failure.
	 */
	function apache_request_headers()
	{
		foreach($_SERVER as $k => $v)
		{
			if (substr($k, 0, 5) == 'HTTP_')
			{
				$k = str_replace('_', ' ', substr($k, 5));
				$k = ucwords(strtolower($k));
				$k = str_replace(' ', '-', $k);
				$out[$k] = $v;
			}
		}

		if (isset($_SERVER['CONTENT_TYPE']))
			$out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
		if (isset($_SERVER['CONTENT_LENGTH']))
			$out['Content-Length'] = $_SERVER['CONTENT_LENGTH'];

		if (isset($out['Remote-Ip']))
			unset($out['Remote-Ip']);

		return $out;
	}
endif;


?>