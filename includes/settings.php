<?php
/**
 * General Site Settings
 */
namespace Starter\Settings;

define( __NAMESPACE__ . '\FORCE_DEVMODE', false );
define( __NAMESPACE__ . '\CACHE_IN_DEVMODE', false );

/*-------------------------------------
	= PAGES
--------------------------------------*/

/**
 * If necessary, set user-friendly labels
 * for specific pages if you need to manually
 * retrieve them in the theme.
 */
$pages = array(
	'page'	=> 'id',
);

/**
 * Allow for the retrieval of either the page
 * id or url as specified in the params
 */
function page( $name, $att = null ) {

	// Options page
	if( $name == 'options' ) :
		echo get_admin_url('/?page=general-settings');
	
	// Frontend pages
	else :

		$page = $pages[$name];

		if( $att == 'id' ) {
			return $page;
		}
		elseif( $att == 'url' || ! $att ) {
			return get_the_permalink( $page );
		}

	endif;
}

/*-------------------------------------
	= DIRECTORIES
--------------------------------------*/

/**
 * Get the paths to our various directories
 */
function dir( $directory, $output = 'url' ) {

	$theme = ( $output == 'url' ? get_stylesheet_directory_uri() : get_stylesheet_directory() );
	$partials = 'partials';
	$dist = '/dist';

	switch ( $directory ) :

		case 'theme' :
			return $theme;
			break;

		case 'critical-css' :
			return $dist . '/styles/';
			break;

		case 'partials' :
			return $partials;
			break;

		case 'headers' :
			return $partials . '/headers';
			break;

		case 'footers' :
			return $partials . '/footers';
			break;

		case 'sections' :
			return $partials . '/sections';
			break;

		case 'components' :
			return $partials . '/components';
			break;

		case 'popups' :
			return $partials . '/popups';
			break;

		case 'sidebars' :
			return $partials . '/sidebars';
			break;

		case 'blog' :
			return $partials . '/blog';
			break;

		case 'widgets' :
			return $partials . '/widgets';
			break;

		default :
			return $theme . $dist . '/' . $directory;
			break;	

	endswitch;
}

/*-----------------------------------------
    = OPTIMIZATION
------------------------------------------*/
/**
 * Check if file is cached in user's browser
 */
function is_cached( $handle ) {

	if( isset( $_COOKIE[$handle] ) && $_COOKIE[$handle] === 'cached' ) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Check if we are in dev mode
 */
function in_devmode () {

	if( function_exists( 'is_wpe_snapshot' ) ) {

		if( is_wpe_snapshot() ) {
			return true;
		}
	}
	elseif( strpos( $_SERVER['SERVER_NAME'], 'localhost' ) !== false || FORCE_DEVMODE ) {

		return true;
	}
	else {

		return false;
	}
}
/**
 * Check to see if caching is on
 */
function caching_is_on() {

	if( in_devmode() && ! CACHE_IN_DEVMODE ) {

		return false;
	}
	else {
		return true;
	}
}

/*-----------------------------------------
    = PASS SETTINGS TO JS
------------------------------------------*/
function settings_to_js() {

	wp_localize_script( 'main-js', 'Starter\Settings', array(
		'home_url'		=> home_url(),
		'in_devmode'	=> in_devmode()
	) );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\settings_to_js' );
