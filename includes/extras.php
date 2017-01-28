<?php
/**
 * Extra functions for theme 
 */
namespace Starter\Extras;

use function Starter\Settings\caching_is_on as caching_is_on;
use function Starter\Settings\dir as dir;

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

/**
 * Add <body> classes
 */
function body_class( $classes ) {

  // Add page slug if it doesn't exist
  if ( is_single() || is_page() && !is_front_page() ) {
    if ( !in_array( basename( get_permalink() ), $classes ) ) {
      $classes[] = basename( get_permalink() );
    }
  }

  return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );

/**
 * Get page's header
 */
function get_the_header() {

  $headers = dir( 'headers' );
  $transient = 'header-main';
  $template = 'header__.php';

  // Set up conditional templates
  // if( is_page() ) {
  //   $template = '';
  //   $transient = '';
  // }

  global $header_for_caching;
  $header_for_caching = $headers . '/' . $template; // not ideal

  fragment_cache( $transient, WEEK_IN_SECONDS, function() {

    global $header_for_caching;
    include( locate_template( $header_for_caching ) );

  });

}

/**
 * Get page's footer
*/
function get_the_footer() {

  $footers = dir( 'footers' );
  $template = 'footer__.php';
  $transient = 'footer-main';

  // Set up conditional templates
  // if( is_page() ) {
  //   $template = '';
  //   $transient = '';
  // }

  global $footer_for_caching;
  $footer_for_caching = $footers . '/' . $template; // not ideal

  fragment_cache( $transient, WEEK_IN_SECONDS, function() {

    global $footer_for_caching;
    include( locate_template( $footer_for_caching ) );

  });
  

}

/**
 * Page titles
 */
function titles() {

  if ( is_home() ) {
    // if ( get_option( 'page_for_posts', true ) ) {
    //   return get_the_title( get_option( 'page_for_posts', true ) );
    // } else {
      return __( 'Latest Posts', 'starter' );
    //}
  } elseif ( is_archive() ) {
    return get_the_archive_title();
  } elseif ( is_search() ) {
    return sprintf( __( 'Search Results for "%s"', 'starter'), get_search_query() );
  } elseif ( is_404() ) {
    return __( 'Not Found', 'starter' );
  } else {
    return get_the_title();
  }
}

function trim_titles( $title ) {

  $title = attribute_escape( $title );
  $find = [
    '#Protected:#',
    '#Private:#'
  ];
  $replace = [
    '',
    ''
  ];

  $title = preg_replace( $find, $replace, $title );

  return $title;
}
add_filter( 'the_title', __NAMESPACE__ . '\\trim_titles' );


/*-----------------------------------------
    = PERFORMANCE
------------------------------------------*/

/**
 *  Cache portions of repetitive code
 *  See https://css-tricks.com/wordpress-fragment-caching-revisited/
 */
function fragment_cache( $key, $ttl, $function ) {

  if( caching_is_on() ) {

    $key = apply_filters( 'fragment_cache_prefix', 'fragment_cache_' ) . $key;
    $output = get_transient( $key );

    if ( empty( $output ) ) {

      ob_start();
      call_user_func ($function );
      $output = ob_get_clean();
      set_transient( $key, $output, $ttl );
    }
  }
  else {

    ob_start();
    call_user_func( $function );
    $output = ob_get_clean();
  }
 
  echo $output;
}

/*-----------------------------------------
    = FORMS
------------------------------------------*/

/**
 * Enable hidden label option
 */
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );


/*-------------------------------------
  = QUERY FUNCTIONS
--------------------------------------*/

/**
 * Extend get terms with post type parameter.

 * See https://www.dfactory.eu/get_terms-post-type/
 *
 * @global $wpdb
 * @param string $clauses
 * @param string $taxonomy
 * @param array $args
 * @return string
 */
function terms_clauses( $clauses, $taxonomy, $args ) {

  if ( isset( $args['post_type'] ) && ! empty( $args['post_type'] ) && $args['fields'] !== 'count' ) {
    
    global $wpdb;

    $post_types = array();

    if ( is_array( $args['post_type'] ) ) {
      foreach ( $args['post_type'] as $cpt ) {
        $post_types[] = "'" . $cpt . "'";
      }
    } else {
      $post_types[] = "'" . $args['post_type'] . "'";
    }

    if ( ! empty( $post_types ) ) {
      $clauses['fields'] = 'DISTINCT ' . str_replace( 'tt.*', 'tt.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent', $clauses['fields'] ) . ', COUNT(p.post_type) AS count';
      $clauses['join'] .= ' LEFT JOIN ' . $wpdb->term_relationships . ' AS r ON r.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN ' . $wpdb->posts . ' AS p ON p.ID = r.object_id';
      $clauses['where'] .= ' AND (p.post_type IN (' . implode( ',', $post_types ) . ') OR p.post_type IS NULL)';
      $clauses['orderby'] = 'GROUP BY t.term_id ' . $clauses['orderby'];
    }
  }
  return $clauses;
}

add_filter( 'terms_clauses', __NAMESPACE__ . '\\terms_clauses', 10, 3 );

/**
 * Get Adjacent Terms
 *
 * Retrieves prev and next terms for current taxonomy term
 * See http://wordpress.stackexchange.com/questions/99513/how-to-get-next-previous-category-in-same-taxonomy
 *
 */
class Adjacent_Terms {

  public $sorted_taxonomies;

  /**
   * @param string Taxonomy name. Defaults to 'category'.
   * @param string Sort key. Defaults to 'id'.
   * @param boolean Whether to show empty (no posts) taxonomies.
   */
  public function __construct( $taxonomy = 'category', $order_by = 'id', $skip_empty = true ) {

    $this->sorted_taxonomies = get_terms(
      $taxonomy,
      array(
        'get'          => $skip_empty ? '' : 'all',
        'fields'       => 'ids',
        'hierarchical' => false,
        //'order'        => 'DESC',
        //'orderby'      => $order_by,
      )
    );
  }

  /**
   * @param int Taxonomy ID.
   * @return int|bool Next taxonomy ID or false if this ID is last one. False if this ID is not in the list.
   */
  public function next( $taxonomy_id ) {

    $current_index = array_search( $taxonomy_id, $this->sorted_taxonomies );

    if ( false !== $current_index && isset( $this->sorted_taxonomies[ $current_index + 1 ] ) )
      return $this->sorted_taxonomies[ $current_index + 1 ];

    return false;

  }

  /**
   * @param int Taxonomy ID.
   * @return int|bool Previous taxonomy ID or false if this ID is last one. False if this ID is not in the list.
   */
  public function previous( $taxonomy_id ) {

    $current_index = array_search( $taxonomy_id, $this->sorted_taxonomies );

    if ( false !== $current_index && isset( $this->sorted_taxonomies[ $current_index - 1 ] ) )
      return $this->sorted_taxonomies[ $current_index - 1 ];

    return false;

  }
}

/*-------------------------------------
  = FILTERS
--------------------------------------*/
function custom_excerpt_length( $length ) {
  return 20;
}
add_filter( 'excerpt_length', __NAMESPACE__ . '\\custom_excerpt_length', 999 );


/*-------------------------------------
  = POPULAR POSTS
--------------------------------------*/
// See http://www.wpbeginner.com/wp-tutorials/how-to-track-popular-posts-by-views-in-wordpress-without-a-plugin/

// To keep the count accurate, lets get rid of prefetching
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

/**
 * Set views
 */
function set_post_views( $post_id ) {

  $count_key = 'dartdrones_views_count';
  $count = get_post_meta( $post_id, $count_key, true );

  if( $count == '' ){
    $count = 0;
    delete_post_meta( $post_id, $count_key );
    add_post_meta( $post_id, $count_key, '0' );
  }
  else{
    $count++;
    update_post_meta( $post_id, $count_key, $count );
  }
}

/**
 * Run the function in wp_head
 */
function track_post_views( $post_id ) {

  if ( !is_single() ) {
    return;
  } 

  if ( empty( $post_id ) ) {
    global $post;
    $post_id = $post->ID;    
  }
  set_post_views( $post_id );

}
add_action( 'wp_head', __NAMESPACE__ . '\\track_post_views' );