<?php
/**
 * Import and register all widgets
 */
foreach( glob( __DIR__ . '[^_]*.php' ) as $filename ) {
    include $filename;
}