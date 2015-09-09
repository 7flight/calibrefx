<?php 

// custom login for CalibreFX Titan
wp_register_style( 'titan-login-css', get_template_directory_uri() . '/modules/titan/assets/css/titan-login.css' );
add_action( 'login_enqueue_scripts', 'cfx_titan_enqueue_login_css' );
function cfx_titan_enqueue_login_css() {
	wp_enqueue_style( 'titan-login-css' );
}

// add_filter( 'header_title_class', function () {
// 	return '';
// } );