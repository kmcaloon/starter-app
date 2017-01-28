<?php
/**
 * Import all classes
 */
foreach( glob( __DIR__ . '[^_]*.php' ) as $filename ) {
    include $filename;
}