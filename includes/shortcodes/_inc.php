<?php
/**
 * Shortcodes
 *
 * Retrieve all shortcode templates for registration and output
 */
foreach( glob( __DIR__ . '[^_]*.php' ) as $filename ) {
    include $filename;
}