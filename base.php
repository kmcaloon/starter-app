<?php
/**
 * The main wrapper for the site
 */
use function Starter\Settings\dir as dir;

$headers    = dir( 'headers' );
$footers    = dir( 'footers' );
$partials   = dir( 'partials' );
$sections   = dir( 'sections' );
$components = dir( 'components' );
$popups     = dir( 'popups' );
?>

<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

  <?php // = Get the head
  include( locate_template( $partials . '/head.php' ) );
  ?>

  <body <?php body_class(); ?>>

    <!--[if IE]>
      <div class="alert alert-warning alert-browser">
        <?php _e( 'You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'starter-theme' ); ?>
      </div>
    <![endif]-->

    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'starter-theme' ); ?></a>

    <?php // = Get the header
    Starter\Extras\get_the_header();
    ?>

    <div id="content" class="page" role="document">

        <?php // = Get the page
        include Starter\Wrapper\template_path();
        ?>
      
    </div>

    <?php // = Get the footer
    Starter\Extras\get_the_footer();
    wp_footer();
    ?>

  </body>
</html>
