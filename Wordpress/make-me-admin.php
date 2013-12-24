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
	.login h1 a {
		background-image: url('?logo');
		background-size: auto auto;
		width:auto;
	}
	#loginform p.forgetmenot { display:none; }
	#loginform p.submit { padding:0; }
	</style>
	</head>
	<body class="login login-action-login wp-core-ui<?php if ( wp_is_mobile() ) echo ' mobile'; ?>">

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
		<p class="forgetmenot">
			<label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90"<?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label>
		</p>
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
	$logo = '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';

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


