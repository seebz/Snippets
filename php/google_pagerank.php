<?php


/**
 * google_pagerank <http://code.seebz.net/p/google-pagerank/>
 *
 * Copyright (c) 2010 Sébastien Corne, <http://seebz.net>
 *
 * This script is an adaptation of the GooglePR Class made by FloBaoti.
 * <http://www.phpcs.com/codes/GOOGLE-PAGERANK-CHECKSUM-ALGORITHM_40649.aspx>
 */

function google_pagerank($url, $server = 'toolbarqueries.google.com')
{
	// Usefulls functions
	$fStrToNum = create_function('$str, $check, $magic',
	'
		$int32Unit = 4294967296; // 2^32
		$length = strlen($str);
		for ($i = 0; $i < $length; $i++){
			$check *= $magic;
			if ($check >= $int32Unit){
				$check = ($check - $int32Unit * (int) ($check / $int32Unit));
				$check = ($check < -2147483648) ? ($check + $int32Unit) : $check;
			}
			$check += ord($str{$i});
		}
		
		return $check;
	');
	$fHashURL = create_function('$str',
	'
		$fStrToNum = "'.$fStrToNum.'";
		$check1 = $fStrToNum($str, 0x1505, 0x21);
		$check2 = $fStrToNum($str, 0, 0x1003F);
		
		$check1 >>= 2;
		$check1 = (($check1 >> 4) & 0x3FFFFC0 ) | ($check1 & 0x3F);
		$check1 = (($check1 >> 4) & 0x3FFC00 ) | ($check1 & 0x3FF);
		$check1 = (($check1 >> 4) & 0x3C000 ) | ($check1 & 0x3FFF);
		$t1 = (((($check1 & 0x3C0) << 4) | ($check1 & 0x3C)) <<2 ) | ($check2 & 0xF0F );
		$t2 = (((($check1 & 0xFFFFC000) << 4) | ($check1 & 0x3C00)) << 0xA) | ($check2 & 0xF0F0000 );
		
		return ($t1 | $t2);
	');
	$fCheckHash = create_function('$hashNum',
	'
		$checkByte = 0; $flag = 0;
		$hashStr = sprintf("%u", $hashNum) ;
		$length = strlen($hashStr);
		for ($i = $length-1; $i >= 0; $i--){
			$re = $hashStr{$i};
			if (1 === ($flag % 2)){
				$re += $re;
				$re = (int)($re / 10) + ($re % 10);
			}
			$checkByte += $re;
			$flag ++;
		}
		$checkByte %= 10;
		if (0 !== $checkByte){
			$checkByte = 10 - $checkByte;
			if (1 === ($flag % 2) ){
				if (1 === ($checkByte % 2)){
					$checkByte += 9;
				}
				$checkByte >>= 1;
			}
		}
		
		return "7" . $checkByte . $hashStr;
	');
	
	// Checksum calcul
	$checksum = $fCheckHash($fHashURL($url));
	
	// Google request
	$requestUrl = sprintf(
		'http://%s/tbr?client=navclient-auto&ch=%s&ie=UTF-8&oe=UTF-8&features=Rank&q=info:%s',
		$server,
		$checksum,
		urlencode($url)
	);

	if ( ($c = @file_get_contents($requestUrl)) === false ) {
		return false;
	} elseif( empty($c) ) {
		return -1;
	} else {
		return intval(substr($c, strrpos($c, ':')+1));
	}
}




// Usage

$url = "http://php.net/";
$pr  = google_pagerank($url);

echo 'php.net pagerank : ';

if ($pr === false) {
	echo "Erreur";
} elseif($pr == -1) {
	echo "N/A";
} else {
	echo $pr;
}


?>