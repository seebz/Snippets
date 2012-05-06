<?php


/**
 * Envoie la miniature d'une image vers un navigateur ou un fichier.
 * @param string $image_src Chemin vers l'image source.
 * @param string|null $image_dest Le chemin de destination. S'il n'est pas défini ou s'il vaut NULL, le flux brut de l'image sera affiché directement.
 * @param intger|array $size La taille maximale de l'image de destination.
 * @param boolean $crop Si ce paramètre vaut TRUE, la miniature gardera les proportions de la source.
 * @param boolean $stretch Si ce paramètre vaut TRUE, l'image pourra éventuellement être étirée pour toujours avoir la taille $size définie.
 * @return boolean Cette fonction retourne TRUE en cas de succès ou FALSE si une erreur survient. 
 */
function imagethumb($image_src, $image_dest = NULL, $size = 100, $crop = FALSE, $stretch = FALSE) {
	if (!file_exists($image_src)) return FALSE;

	if (is_array($size)) {
		list($max_width, $max_height) = $size;
	} else {
		$max_width = $max_height = $size;
	}
	
	// Récupère les infos de l'image originale
	$fileinfo = getimagesize($image_src);
	if (!$fileinfo) return FALSE;

	$width     = $fileinfo[0];
	$height    = $fileinfo[1];
	$type_mime = $fileinfo['mime'];
	$type      = str_replace('image/', '', $type_mime);

	// Calcul des dimensions de la miniature
	$ratio   = $width / $height;
	$ratio_w = $width / $max_width;
	$ratio_h = $height / $max_height;

	if ($ratio_w > $ratio_h) {
		// On se base sur la largeur
		$func = ($stretch ? 'max' : 'min');
		if ($crop) {
			// on va couper à gauche et droite
			$new_height = $func($height, $max_height);
		} else {
			// on garde les proportions (largeur OK)
			$new_width  = $func($width, $max_width);
		}
	} else {
		// On se base sur la hauteur
		$func = ($stretch ? 'max' : 'min');
		if ($crop) {
			// on va couper en haut et bas
			$new_width = $func($width, $max_width);
		} else {
			// on garde les proportions (hauteur OK)
			$new_height = $func($height, $max_height);
		}
	}

	if (isset($new_width)) {
		$new_height = $new_width / $ratio;
	} else {
		$new_width = $new_height * $ratio;
	}

	$new_width  = min($max_width, round($new_width));
	$new_height = min($max_height, round($new_height));

	// Calcul de la zone de découpe de l'image source
	$src_x = $src_y = 0;
	$src_w = $width;
	$src_h = $height;

	if ($crop) {
		$ratio = $max_width / $max_height;
		
		if ($ratio_w > $ratio_h) {
			// on va couper à gauche et droite
			$src_w = round($height * $ratio);
			$src_x = round(($width - $src_w) / 2);
		} else {
			// on va couper en haut et bas
			$src_h = round($width / $ratio);
			$src_y = round(($height - $src_h) / 2);
		}
	}

	// Ouvre l'image originale
	$func = 'imagecreatefrom' . $type;
	if (!function_exists($func)) return FALSE;

	$image_src = $func($image_src);
	$new_image = imagecreatetruecolor($new_width,$new_height);

	// Gestion de la transparence pour les png
	if ($type=='png') {
		imagealphablending($new_image,false);
		if (function_exists('imagesavealpha'))
			imagesavealpha($new_image,true);
	}

	// Gestion de la transparence pour les gif
	elseif ($type=='gif' && imagecolortransparent($image_src)>=0) {
		$transparent_index = imagecolortransparent($image_src);
		$transparent_color = imagecolorsforindex($image_src, $transparent_index);
		$transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
		imagefill($new_image, 0, 0, $transparent_index);
		imagecolortransparent($new_image, $transparent_index);
	}

	// Redimensionnement de l'image
	imagecopyresampled(
		$new_image, $image_src,
		0, 0, $src_x, $src_y,
		$new_width, $new_height, $src_w, $src_h
	);

	// Enregistrement de l'image
	$func = 'image'. $type;
	if ($image_dest) {
		$func($new_image, $image_dest);
	} else {
		header('Content-Type: '. $type_mime);
		$func($new_image);
	}

	// Libération de la mémoire
	imagedestroy($new_image); 

	return TRUE;
}


?>