<?php

/**
 * Module Name: CalibreFX Theme
 * Module Description: Original CalibreFX theme.
 * First Introduced: 0.0.0
 * Requires Connection: No
 * Auto Activate: No
 * Sort Order: 1
 * Module Tags: Theme
 */

wp_register_style( 'calibrefx-style', CALIBREFX_URL . '/modules/calibrefx/css/calibrefx.css' );
add_action( 'wp_enqueue_scripts', 'calibrefx_enqueue_css' );

function calibrefx_enqueue_css() {
	wp_enqueue_style( 'calibrefx-style' );
}