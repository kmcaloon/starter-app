<?php
/**
 * Theme scripts, styles, etc
 */
namespace Starter\Assets;
use function Starter\Settings\dir as get_template_dir;
use function Starter\Settings\is_cached as is_cached;

/**
 * Enable async
 * - https://ikreativ.com/async-with-wordpress-enqueue/
 */
function enable_async( $url ) {

	if ( strpos( $url, '#asyncload') === false ) {
        return $url;
	}
    elseif( is_admin() ) {
        return str_replace( '#asyncload', '', $url );
    }
    else {
		return str_replace( '#asyncload', '', $url ) . "' async='async"; 
    }
}
add_filter( 'clean_url', __NAMESPACE__ . '\\enable_async', 11, 1 );

/**
 * Enable defer
 * - https://ikreativ.com/async-with-wordpress-enqueue/
 */
function enable_defer( $url ) {

	if ( strpos( $url, '#deferload') === false ) {
        return $url;
	}
    elseif( is_admin() ) {
        return str_replace( '#deferload', '', $url );
    }
    else {
		return str_replace( '#deferload', '', $url ) . "' defer='defer"; 
    }
}
add_filter( 'clean_url', __NAMESPACE__ . '\\enable_defer', 11, 1 );


/**
 * Fonts
 */
function fonts() { 

 // Typekit, etc. fonts go here 

}
add_action( 'wp_head', __NAMESPACE__ . '\\fonts' );

/**
 * Load critical styles
 */
function load_critical_styles() {

    // If global stylesheet is not already cached, load the critical styles in head
    if( ! is_cached( 'main-css' ) ) {

        $critical_path = get_template_dir( 'critical' );
        
        if( locate_template( $critical_path . '/critical.html' ) ) {

            include( locate_template( $critical_path . '/critical.html' ) );

        }
    }

}
add_action( 'wp_head', __NAMESPACE__ . '\\load_critical_styles' );

/**
 * Enqueues
 * - Note that filemtime is used as versioning for cache busting
 */
function enqueues() {

    /*-----------------------------------------
        = STYLES
    ------------------------------------------*/
    $styles = get_template_dir( 'styles' );
    $styles_path = get_template_dir( 'styles', 'PATH' );

    // If global stylesheet is already cached
    if( is_cached( 'main.css' ) ) {

        if( file_exists( $styles_path . '/main.css' ) ) {

             wp_enqueue_style( 'main.css', $styles . '/main.css', array(), filemtime( $styles_path . '/main.css' ) );

        }

    }
    // If main stylsheet is not cached, run page-specific styles
    else {

        wp_enqueue_style( 'main.css', $styles . '/main.css', array(), filemtime( $styles_path . '/main.css' ) );
    }

    /*-----------------------------------------
        = SCRIPTS
    ------------------------------------------*/
    $scripts = get_template_dir( 'scripts' );
    $scripts_path = get_template_dir( 'scripts', 'PATH' );

    // If global stylesheet is already cached
    if( is_cached( 'main.css' ) ) {

        // Make sure onload script is loaded to load and cache main stylsheet asynchronously
        wp_enqueue_script( 'onload', $scripts . '/onload.js', null, null, false );
    }

    // JQuery
    wp_deregister_script( 'jquery' );
    wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js', '', '2.1.4', false );

    // Main
    if( file_exists( $scripts_path . '/main.js' ) ) {

        wp_enqueue_script( 'main', $scripts . '/main.js#asyncload', array( 'jquery' ), filemtime( $scripts_path . '/main.js' ), true );

    }
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueues' );

/**
 * Denqueues
 */
function dequeues() {
	
	
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\dequeues', 9999999 );
add_action( 'wp_head', __NAMESPACE__ . '\\dequeues', 9999999 );
add_action( 'wp_print_styles', __NAMESPACE__ . '\\dequeues', 9999999 );

/**
 * Condional Scripts
 */