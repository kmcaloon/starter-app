<?php
/**
 * Import all admin functionality
 */
foreach( glob( __DIR__ . '[^_]*.php' ) as $filename ) {
    include $filename;
}