<?php
/**
 * Theme setup, sidebars, etc
 */
namespace Starter\Setup;

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on dartdrones, use a find and replace
	 * to change 'starter' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'starter-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Menus
	 */
	// register_nav_menus( array(
	// 	'menu_id'    => __( 'Menu Name', 'starter-theme' ),
	// ) );


	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	// add_theme_support( 'post-formats', array(
	// 	'aside',
	// 	'image',
	// 	'video',
	// 	'quote',
	// 	'link',
	// ) );

}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );

/**
 * Register widgets
 */
foreach( glob( __DIR__ . '/widgets/*.php' ) as $filename ) {
  
  if( $filename !== __DIR__ . '/widgets/acf-widget-template.php' ) {
    include $filename;
  }
}

/**
 * Register Sidebars
 */
// function widgets_init() {

//   register_sidebar([
//     'name'          => __( 'Sidebar Name', 'starter-theme' ),
//     'id'            => 'sidebar-id',
//     'before_widget' => '<section class="widget %1$s %2$s">',
//     'after_widget'  => '</section>',
//     'before_title'  => '<h3>',
//     'after_title'   => '</h3>'
//   ]);

// }
// add_action( 'widgets_init', __NAMESPACE__ . '\\widgets_init' );