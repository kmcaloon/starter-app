<?php
/**
 * Include all necessary code into the app
 */
$starter_includes = [
  'includes/dev.php',                    // For debugging and whatnot
  'includes/settings.php',               // Global dirs & pages
  'includes/acf.php',                    // Custom ACF functions
  'includes/admin/_inc.php',             // Admin functionality
  'includes/assets.php',                 // Scripts and stylesheets
  'includes/classes/_inc.php',           // Classes
  'includes/extras.php',                 // Custom functions
  'includes/integrations.php',           // Third Party integrations
  'includes/setup.php',                  // Theme setup
  'includes/shortcodes/_inc.php',        // Shortcodes
  'includes/theme-wrapper.php',          // Setup custom theme wrapping
  'includes/widgets/_inc.php' ,          // Extra Woo functions
  'includes/woocommerce.php' ,           // Extra Woo functions
];
foreach( $starter_includes as $filepath ) {
  include_once $filepath;
}
unset( $filepath );
