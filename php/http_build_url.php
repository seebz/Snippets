<?php



/**
 * Constants usable with http_build_url()
 * @link http://php.net/manual/en/http.constants.php#constant.http-url-replace
 */
defined('HTTP_URL_REPLACE')        or define('HTTP_URL_REPLACE',        0);
defined('HTTP_URL_JOIN_PATH')      or define('HTTP_URL_JOIN_PATH',      1);
defined('HTTP_URL_JOIN_QUERY')     or define('HTTP_URL_JOIN_QUERY',     2);
defined('HTTP_URL_STRIP_USER')     or define('HTTP_URL_STRIP_USER',     4);
defined('HTTP_URL_STRIP_PASS')     or define('HTTP_URL_STRIP_PASS',     8);
defined('HTTP_URL_STRIP_AUTH')     or define('HTTP_URL_STRIP_AUTH',     12);
defined('HTTP_URL_STRIP_PORT')     or define('HTTP_URL_STRIP_PORT',     32);
defined('HTTP_URL_STRIP_PATH')     or define('HTTP_URL_STRIP_PATH',     64);
defined('HTTP_URL_STRIP_QUERY')    or define('HTTP_URL_STRIP_QUERY',    128);
defined('HTTP_URL_STRIP_FRAGMENT') or define('HTTP_URL_STRIP_FRAGMENT', 256);
defined('HTTP_URL_STRIP_ALL')      or define('HTTP_URL_STRIP_ALL',      492);


if ( ! function_exists('http_build_str')) :
	/**
	 * Build query string
	 * @link http://php.net/manual/en/function.http-build-str.php
	 * @param array $query associative array of query string parameters
	 * @param string $prefix top level prefix
	 * @param string $arg_separator argument separator to use (by default the INI setting arg_separator.output will be used, or "&" if neither is set
	 * @return string Returns the built query as string on success or FALSE on failure. 
	 */
	function http_build_str(array $query, $prefix = '', $arg_separator = null)
	{
		if (is_null($arg_separator)) $arg_separator = ini_get('arg_separator.output');

		$out = array();
		foreach($query as $k => $v)
		{
			$key = $prefix ? "{$prefix}%5B{$k}%5D" : $k;

			if (is_array($v))
				$out[] = call_user_func(__FUNCTION__, $v, $key, $arg_separator);
			else
				$out[] = $key . '=' . urlencode($v);
		}

		return implode($arg_separator, $out);
	}
endif;


if ( ! function_exists('http_build_url')) :
	/**
	 * Build a URL
	 * @link http://php.net/manual/en/function.http-build-url.php
	 * @param mixed $url (part(s) of) an URL in form of a string or associative array like parse_url() returns
	 * @param mixed $parts same as the first argument
	 * @param integer $flags a bitmask of binary or'ed HTTP_URL constants; HTTP_URL_REPLACE is the default
	 * @param array $new_url if set, it will be filled with the parts of the composed url like parse_url() would return
	 * @return string Returns the new URL as string on success or FALSE on failure.
	 */
	function http_build_url($url = array(), $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = null)
	{
		$defaults = array(
			'scheme' => (empty($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS'])=='off' ? 'http' : 'https'),
			'host'   => $_SERVER['HTTP_HOST'],
			'port'   => '',
			'user'   => '', 'pass' => '',
			'path'   => preg_replace('`^([^\?]*).*$`', '$1', $_SERVER['REQUEST_URI']),
			'query'  => '', 'fragment' => '',
		);
		is_array($url) or $url = parse_url($url);
		is_array($parts) or $parts = parse_url($parts);
		$new_url = $parts + $url + $defaults;

		$flags or $flags = (HTTP_URL_JOIN_PATH); // Default flags ?


		$JOIN_PATH      = (($flags | HTTP_URL_JOIN_PATH) == $flags);
		$JOIN_QUERY     = (($flags | HTTP_URL_JOIN_QUERY) == $flags);
		$STRIP_USER     = (($flags | HTTP_URL_STRIP_USER) == $flags);
		$STRIP_PASS     = (($flags | HTTP_URL_STRIP_PASS) == $flags);
		$STRIP_PATH     = (($flags | HTTP_URL_STRIP_PATH) == $flags);
		$STRIP_QUERY    = (($flags | HTTP_URL_STRIP_QUERY) == $flags);
		$STRIP_FRAGMENT = (($flags | HTTP_URL_STRIP_FRAGMENT) == $flags);


		// User
		if ($STRIP_USER)
			$new_url['user'] = '';

		// Pass
		if ( ! $new_url['user'] || ($new_url['pass'] && $STRIP_PASS))
			$new_url['pass'] = '';

		// Port
		if ($new_url['port'] && ($flags | HTTP_URL_STRIP_PORT) == $flags)
			$new_url['port'] = '';

		// Path
		if ($STRIP_PATH)
			$new_url['path'] = '';
		else
		{
			$d_path = $defaults['path'];
			$u_path = (isset($url['path'])   ? $url['path']   : '');
			$p_path = (isset($parts['path']) ? $parts['path'] : '');

			if ($p_path) $u_path = '';

			$path = $d_path;

			if (isset($url['host']) && ! $p_path)
				$path = '/' . ltrim($u_path, '/');
			elseif (strpos($u_path, '/') === 0)
				$path = $u_path;
			elseif ($u_path)
				$path = pathinfo($path . 'x', PATHINFO_DIRNAME) . '/' . $u_path;

			if (isset($parts['host']))
				$path = '/' . ltrim($p_path, '/');
			elseif (strpos($p_path, '/') === 0)
				$path = $p_path;
			elseif ($p_path)
				$path = pathinfo($path . 'x', PATHINFO_DIRNAME) . '/' . $p_path;

			$path = explode('/', $path);
			$k_stack = array();
			foreach($path as $k => $v)
			{
				if( $v == '..') // /../
				{
					if ($k_stack)
					{
						$k_parent = array_pop($k_stack);
						unset($path[$k_parent]);
					}
					unset($path[$k]);
				}
				elseif ($v == '.') // /./
					unset($path[$k]);
				else
					$k_stack[] = $k;
			}
			$path = implode('/', $path);

			$new_url['path'] = $path;
		}
		$new_url['path'] = '/' . ltrim($new_url['path'], '/');

		// Query
		if ($STRIP_QUERY)
			$new_url['query'] = '';
		else
		{
			$u_query = isset($url['query'])   ? $url['query']   : '';
			$p_query = isset($parts['query']) ? $parts['query'] : '';

			$query = $new_url['query'];

			if (is_array($p_query))
				$query = $u_query;
			elseif ($JOIN_QUERY)
			{
				if ( ! is_array($u_query)) parse_str($u_query, $u_query);
				if ( ! is_array($p_query)) parse_str($p_query, $p_query);

				$u_query = http_build_str($u_query);
				$p_query = http_build_str($p_query);

				$u_query = str_replace(array('[', '%5B'), '{{{', $u_query);
				$u_query = str_replace(array(']', '%5D'), '}}}', $u_query);

				$p_query = str_replace(array('[', '%5B'), '{{{', $p_query);
				$p_query = str_replace(array(']', '%5D'), '}}}', $p_query);

				parse_str($u_query, $u_query);
				parse_str($p_query, $p_query);

				$query = http_build_str(array_merge($u_query, $p_query));
				$query = str_replace(array('{{{', '%7B%7B%7B'), '%5B', $query);
				$query = str_replace(array('}}}', '%7D%7D%7D'), '%5D', $query);

				parse_str($query, $query);
			}

			if (is_array($query))
				$query = http_build_str($query);

			$new_url['query'] = $query;
		}

		// Fragment
		if ($STRIP_FRAGMENT)
			$new_url['fragment'] = '';


		// Scheme
		$out = $new_url['scheme'] . '://';

		// User
		if ($new_url['user'])
			$out .= $new_url['user']
				. ($new_url['pass'] ? ':' . $new_url['pass'] : '')
				. '@';

		// Host
		$out .= $new_url['host'];

		// Port
		if ($new_url['port'])
			$out .= ':' . $new_url['port'];

		// Path
		$out .= $new_url['path'];

		// Query
		if ($new_url['query'])
			$out .= '?' . $new_url['query'];

		// Fragment
		if ($new_url['fragment'])
			$out .= '#' . $new_url['fragment'];


		$new_url = array_filter($new_url);
		return $out;
	}
endif;

