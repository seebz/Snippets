<?php

/**
 * Script d'accès au backoffice d'un site WordPress.
 *
 * Utilisation:
 *	- renseigner le mot de passe hashé (hash mda5)
 *	- uploader à la racine du site
 *	- accéder à l'adresse http://monsite.com/make-me-admin.php
 *	- indiquer le mot de passe et soummettre le formulaire
 *	- enjoy
 */



// Mot de passe du script `Make me Admin` (md5)
define('MMA_MD5_PASS','HASH MD5 DU MOT DE PASSE `Make me Admin`');




/* ************************************************************************* */



// Chargement de WP
require(dirname(__FILE__) . '/wp-load.php');



// Action
declare_missing_functions();
switch(true)
{

	// On demande le logo
	case isset($_GET['logo']):
		print_logo();
		exit;
		break;


	// Le formulaire a été posté
	case isset($_POST['mma_pass']):
		$mma_pass = stripslashes_deep($_POST['mma_pass']);

		// Pass OK
		if (md5($mma_pass) == MMA_MD5_PASS)
		{

			// On est authentifié
			if (make_me_admin())
			{
				wp_redirect(admin_url());
				exit();
			}

			else
			{
				$message = __('<strong>ERROR</strong>: No administrator found');
			}
		}

		// Pass nok
		else
		{
			$message = __('<strong>ERROR</strong>: Incorrect password.');
		}

		$message = sprintf('<div id="login_error">%s</div>', $message);

		show_form($message);
		break;


	// On affiche le form
	default:
		show_form();
}





/**
 * Affichage du formulaire de connexion `Make me Admin`
 */
function show_form($message = '') {

	$title = 'Make me Admin';

	$login_header_url   = home_url();
	$login_header_title = __( 'Powered by WordPress' );

	$rememberme = false;

	$input_id = 'user_pass';


	?><!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="robots" content="noindex, nofollow" />
	<title><?php bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
	<?php

	wp_admin_css( 'wp-admin', true );
	wp_admin_css( 'login', true );
	wp_admin_css( 'colors-fresh', true );

	if ( wp_is_mobile() ) { ?>
		<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" /><?php
	}

	do_action( 'login_enqueue_scripts' );
	do_action( 'login_head' );

	?>
	<style type="text/css">
	.login h1 a { background-image: url('?logo'); }
	#loginform p.submit { padding:0; }
	</style>
	</head>
	<body class="login<?php if ( wp_is_mobile() ) echo ' mobile'; ?>">

		<div id="login">
			<h1><a href="<?php echo esc_url( $login_header_url ); ?>" title="<?php echo esc_attr( $login_header_title ); ?>"><?php bloginfo( 'name' ); ?></a></h1>

			<?php
				if ( ! empty($message))
					echo $message . "\n";
			?>

	<form name="loginform" id="loginform" action="" method="post">
		<p>
			<input type="password" name="mma_pass" id="user_pass" class="input" value="" size="20" tabindex="20" />
		</p>
	<!--
		<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label></p>
	-->
		<p class="submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="<?php esc_attr_e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
		</p>
	</form>

			<p id="backtoblog"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( 'Are you lost?' ); ?>"><?php printf( __( '&larr; Back to %s' ), get_bloginfo( 'title', 'display' ) ); ?></a></p>
		</div>

		<?php if ( !empty($input_id) ) : ?>
		<script type="text/javascript">
		try{document.getElementById('<?php echo $input_id; ?>').focus();}catch(e){}
		if(typeof wpOnload=='function')wpOnload();
		</script>
		<?php endif; ?>

		<?php do_action('login_footer'); ?>
		<div class="clear"></div>
	</body>
	</html>
	<?php

}



/**
 * Authentification en tant que premier utilisateur `admin` trouvé
 */
function make_me_admin()
{
	if (wp_version_compare('3.1', '>='))
		return _make_me_admin_gte_31();
	else
		return false;
}

function _make_me_admin_gte_31()
{
	// Recherche d'un utilisateur admin
	$administrators = get_users(array(
		'role' => 'administrator',
	));
	if ($administrators)
		$user = array_shift($administrators);
	else
		return false;

	// Création du cookie d'authentification
	$secure_cookie = get_user_option('use_ssl', $user->ID); //is_ssl();
	$remember = false;
	wp_set_auth_cookie($user->ID, $remember, $secure_cookie);

	return $user;
}



/**
 * Compare la version de WordPress avec une chaîne $version passé en paramètre.
 * 
 * Se référer à la doc de la fonction PHP `version_compare` pour les opérateurs
 * possibles et les valeurs de retour.
 * http://php.net/manual/en/function.version-compare.php
 */
function wp_version_compare($version, $operator = null) {
	global $wp_version;
	if (is_null($operator)) {
		return version_compare($wp_version, $version);
	} else {
		return version_compare($wp_version, $version, $operator);
	}
}



/**
 * Affiche le logo `Make me admin`
 */
function print_logo()
{
	$logo = 'iVBORw0KGgoAAAANSUhEUgAAARIAAAA/CAYAAAAsckd/AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
bWFnZVJlYWR5ccllPAAAA2RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdp
bj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6
eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEz
NDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJo
dHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlw
dGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEu
MC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVz
b3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1N
Ok9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpCOUQxRjE1QTQ4MkFFMjExQTA4NkUzMzhGRjVD
MTc2QiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3REVCQkQ4QTJBNDkxMUUyQjkwNDg0MUNF
RDJCNTJDQiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3REVCQkQ4OTJBNDkxMUUyQjkwNDg0
MUNFRDJCNTJDQiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3Mi
PiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpCQkQxRjE1QTQ4
MkFFMjExQTA4NkUzMzhGRjVDMTc2QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpCOUQxRjE1
QTQ4MkFFMjExQTA4NkUzMzhGRjVDMTc2QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRG
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';

	header('Content-Type: image/png');
	echo base64_decode($logo);

}



/**
 * Divers
 */

function declare_missing_functions()
{
	// Since 3.4
	if ( ! function_exists('wp_is_mobile'))
	{
		function wp_is_mobile()
		{
			return false;
		}
	}

}


