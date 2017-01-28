<?php
/**
 * Custom Field Helpers & Functionalty
 */
namespace Starter\ACF;

/**
 * Processes and localizes individual fields
 */
function generate_field( $name, $post_id, $is_subfield ) {

  $localize = false;
  $field = '';

  // Detect if this is text that needs localization and prepare field
  if( substr( $name, 0, 10 ) == 'localize--' ) {
    $name = str_replace( 'localize--', '', $name );
    $localize = true;
  }

  // Detect subfield
  if( $is_subfield ) {
    $field = get_sub_field( $name );
  }
  else {
    $field = get_field( $name, $post_id );
  }

  // Localize if necessary
  if( $localize ) {
    $field = sprintf( __( '%s', 'dd' ), $field );
  }

  return $field;
}

/**
 * Rename and prepare fields for final array
 */
function rename_field( $name ) {
  $original_name = $name;

  // Rename 'localize' fields
  if( substr( $name, 0, 10 ) == 'localize--' ) {
    $name = str_replace( 'localize--', '', $name );
  }
  $name_array = array(
    'original_name' => $original_name,
    'name' => $name
  );

  return $name_array;

}

/**
 * Processes and localizes an array of fields
 */
function get_fields( $names, $post_id = '', $force_parent = false ) {
  $field_group = array();
  $subfield = false;
  // if( get_row_index() > 0 ) {
  //   //$subfield = true;
  // }
  $row_check = get_row();
  if( $row_check && !$force_parent && empty( $post_id ) ) {
    $subfield = true;
  };

  if( is_array( $names ) ) {
    foreach( $names as $name ) {

      $field = rename_field( $name );
      $field_group[$field['name']] = generate_field( $field['original_name'], $post_id, $subfield );
    }
  }
  elseif( preg_match( '/\s/', $names ) == 0 ) {
    $field_group[] = generate_field( $name, $post_id, $subfield );
  }

  return $field_group;
}

/**
 * Add on additional fields to an existing custom field array
 */
function include_fields( $field_group, $new_fields, $post_id = '' ) {
  $subfield = false;

  // if( get_row_index() > 0 ) {
  //   $subfield = true;
  // }
  $row_check = get_row();
  if( $row_check ) {
    $subfield = true;
  };

  if( is_array( $new_fields ) ) {
    foreach( $new_fields as $name ) {

      $field = rename_field( $name );
      $field_group[$field['name']] = generate_field( $field['original_name'], $post_id, $subfield );
    }
  }
  elseif( preg_match( '/\s/', $new_fields ) == 0 ) {
    $field_group[] = generate_field( $name, $post_id, $subfield );
  } 

  return $field_group;
}

/**
 * Simplify live edit function
 */
function edit( $fields, $post_id ) {
  if( function_exists( 'live_edit' ) ) {
    return live_edit( $fields, $post_id );
  }
  else {
    return null;
  }
}

/**
 *  Allow shortcodes inside of textareas
 */
function format_shortcodes( $content, $post_id, $field ) {

  return do_shortcode( $content );

}
add_filter( 'acf/format_value/type=textarea', __NAMESPACE__ . '\\format_shortcodes', 10, 3 );

/**
 * Update term meta based on acf term meta fields
 */
// function update_term_meta( $value, $post_id, $field ) {
//   $term_id = intval( filter_var( $post_id, FILTER_SANITIZE_NUMBER_INT ) );

//   // if( substr( $field['name'], 0, 5 ) === 'meta_' ) {
//   //if( $term_id > 0 ) {
//       $update_term_meta = update_term_meta( 162, 'meta_demo', 'demo-works' );
//   // }
//  // }

//   return $value;
// }
// add_filter( 'acf/update_value/name=meta_type_option', __NAMESPACE__ . '\\update_term_meta', 10, 3 );

/**
 * Load term meta in for acf meta fields
 */
// function load_term_meta( $value, $post_id, $field ) {
//   $term_id = intval( filter_var( $post_id, FILTER_SANITIZE_NUMBER_INT ) );

//   if( substr( $field['name'], 0, 5 ) === 'meta_' ) {

//     if( $term_id > 0 ) {
//       $value = get_term_meta( $term_id, 'demo', true );
//     }
//   }

//   return $value;
// }
// add_filter( 'acf/load_value', __NAMESPACE__ . '\\load_term_meta', 10, 3 );

/**
 * Flex layouts
 */
function get_layouts( $fc_field = 'sections', $single_layout = false, $exclusions = array() ) {

  $layouts_dir = \Starter\Settings\dir( 'flex-layouts' );
  $content = '';

  if( have_rows( $fc_field ) ) :
    $i = 1; // Counter

    while ( have_rows( $fc_field ) ) : the_row();

      $layouts = \Starter\Settings::$layouts;

      if( $layouts ) {

        foreach ( $layouts as $layout ) {

          // Single Layout
          if( $single_layout ) {

            if( get_row_layout() == $layout && get_row_layout() == $single_layout ) {
              $content .= include( locate_template( $layouts_dir . '/' . $layout . '.php' ) );
            }
          }
          else if( get_row_layout() == $layout ) {

            if( !in_array( $layout, $exclusions ) ) {
              $content .= include( locate_template( $layouts_dir . '/' . $layout . '.php' ) );
            }
          }
        }
      }
      $i++; // Increment

    endwhile;

    return $content;

  else :

   // no layouts found
    //return 'No Layouts Found';


  endif;

}

/**
 * GMaps API
 */
add_filter('acf/settings/google_api_key', function () {
    return 'get_api_key';
});

/**
 * Extract URL from video field
 */
function get_video_url( $embed ) {

  $start_of_url = explode( 'src="', $embed )[1];
  $url = explode( '"', $start_of_url, 2 )[0];

  return $url;
  
}