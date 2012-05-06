<?php

header('Content-Type: text/html; charset=utf-8');

?>
<html>
<head>
<title>Table Unicode</title>
<style>
html,
body {
	margin:0; padding:0;
	color:#000;
	background:#fff;
	}
table {
	margin:1em auto 4em auto;
	border-spacing: 0px;
	border-collapse: collapse; 
	font-size:12px;
	font-family:sans-serif;
	}
table th,
table td {
	border:1px solid #ccc;
	}
table th {
	padding:5px;
	width:4.5em;
	text-transform:uppercase;
	background:#f8f8f8;
	}
table th.left {
	width:auto;
	}
table td {
	padding:2px;
	height:3.5em;
	text-align:center;
	vertical-align:bottom;
	}
table td div.cell,
table td div.content {
	position:relative;
	width:100%; height:100%;
	}
table td span {
	display:block;
	}
span.chr {
	height:2em; line-height:2em;
	font-size:1.1em;
	color:darkblue;
	}
span.entity {
	font-size:.8em;
	font-family:monospace;
	color:#666;
	}
span.entity span {
	display:none;
	}

table td:hover div.content {
	position:absolute;
	top:-50%; left:-62%;
	width:250%; height:3.5em;
	z-index:999;
	font-size:2.5em;
	background-color:#fff;
	border-radius:5px;
	box-shadow:0 0 6px #000;
	}
table td:hover span.entity {
	font-size:.7em;
	}
table td:hover span.entity span {
	display:block;
	}

code {
	font-size:.7em;
	}
</style>
</head>
<body>

<?php

for ($i = 0; $i < 4096; $i++) {
	
	$mod256 = $i % 256;
	$mod16  = $i % 16;
	
	if ($mod256 == 0) {
		print('<table><tr>
			<th></th>
			<th>0</th>
			<th>1</th>
			<th>2</th>
			<th>3</th>
			<th>4</th>
			<th>5</th>
			<th>6</th>
			<th>7</th>
			<th>8</th>
			<th>9</th>
			<th>a</th>
			<th>b</th>
			<th>c</th>
			<th>d</th>
			<th>e</th>
			<th>f</th>'
		);
	}
	if ($mod16 == 0) {
		$floor = floor($i / 16);
		printf('</tr><tr><th class="left">%03s</th>', 
				dechex($floor)
			);
	}
	
	$chr    = unichr($i);
	$entity = htmlentities($chr, ENT_NOQUOTES, 'UTF-8');
	if (!mb_strpos($entity, ';')) {
		$entity = "<span>&#{$i};</span>";
	}
	
	switch($chr) {
		case ' ':
			$chr = '<code>[space]</code>';
			break;
		case "\n":
			$chr = '<code>[\n]</code>';
			break;
		case "\r":
			$chr = '<code>[\r]</code>';
			break;
		case "\t":
			$chr = '<code>[\t]</code>';
			break;
	}
	
	$cell = '
		<td><div class="cell">
			<div class="content">
			<span class="chr">%s</span>
			<span class="entity">%s</span>
			</div>
		</div></td>
	';
	printf($cell,
			$chr,
			str_replace('&', '&amp;', $entity)
		);
	
	
	if ($mod256 == 255) {
		printf('</tr></table>');
	}
}

?>
</body>
</html>
<?php

function unichr( $unicode , $encoding = 'UTF-8' ) {
	return mb_convert_encoding("&#{$unicode};", $encoding, 'HTML-ENTITIES');
}

?>