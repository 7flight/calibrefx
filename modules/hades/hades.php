<?php

/**
 * Module Name: Hades Theme
 * Module Description: One of them themes for CFX Titan.
 * First Introduced: 0.0.0
 * Requires Connection: No
 * Auto Activate: No
 * Sort Order: 1
 * Module Tags: Theme
 */

wp_register_style( 'hades_style', CALIBREFX_URL . '/modules/hades/css/hades.css' );
add_action( 'wp_enqueue_scripts', 'hades_enqueue_css' );

function hades_enqueue_css() {
	wp_enqueue_style( 'hades_style' );
}