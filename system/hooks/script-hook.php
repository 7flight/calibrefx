<?php
/**
 * Calibrefx Script Hooks
 */

global $calibrefx;

$calibrefx->hooks->calibrefx_meta = array(
	array( 'function' => 'calibrefx_load_scripts', 'priority' => 5 ),
	array( 'function' => 'calibrefx_load_styles', 'priority' => 5 ),
);

/**
 * This function register our style and script files
 */
function calibrefx_register_scripts() {
	wp_register_style( 'jquery-superfish', CALIBREFX_CSS_URL . '/superfish.css' );
	wp_register_style( 'jquery-lightbox', CALIBREFX_CSS_URL . '/lightbox.css' );

	wp_register_style( 'twitter-bootstrap', CALIBREFX_CSS_URL . '/bootstrap.min.css' );
	wp_register_style( 'calibrefx-shortcodes', CALIBREFX_CSS_URL . '/shortcodes.css' );
	wp_register_style( 'font-awesome', CALIBREFX_CSS_URL . '/font-awesome.min.css' );
	wp_register_style( 'calibrefx-icons', CALIBREFX_CSS_URL . '/cfxicons.css' );
	wp_register_style( 'genericons', CALIBREFX_CSS_URL . '/genericons.css', array(), '3.3' );

	wp_register_script( 'modernizr', CALIBREFX_JS_URL . '/modernizr.min.js', false );
	wp_register_script( 'jquery-validate', CALIBREFX_JS_URL . '/jquery.validate.js', 'jquery' );
	wp_register_script( 'jquery-sticky', CALIBREFX_JS_URL . '/jquery.sticky.js', 'jquery', '', true );
	wp_register_script( 'jquery.cycle2', CALIBREFX_JS_URL . '/jquery.cycle2.js', 'jquery', '', true );
	wp_register_script( 'jquery.cycle2.optional', CALIBREFX_JS_URL . '/jquery.cycle2.optional.js', 'jquery', '', true );
	wp_register_script( 'jquery-appear', CALIBREFX_JS_URL . '/jquery.appear.js', 'jquery', '', true );
	wp_register_script( 'jquery-easing', CALIBREFX_JS_URL . '/jquery.easing.js', 'jquery', '', true );
	wp_register_script( 'jquery-transition', CALIBREFX_JS_URL . '/jquery.transit.min.js', 'jquery', '', true );
	wp_register_script( 'jquery-lightbox', CALIBREFX_JS_URL . '/lightbox.min.js', 'jquery', '', true );
	wp_register_script( 'infobox', CALIBREFX_JS_URL . '/infobox.js', 'jquery', '', true );
	wp_register_script( 'jquery-googlemap', CALIBREFX_JS_URL . '/jquery.googlemap.js', 'jquery', '', true );

	wp_register_script( 'twitter-bootstrap', CALIBREFX_JS_URL . '/bootstrap.min.js', 'jquery' );
	wp_register_script( 'calibrefx-shortcodes', CALIBREFX_JS_URL . '/shortcodes.js', 'jquery', '', true );
	wp_register_script( 'calibrefx-script', CALIBREFX_JS_URL . '/calibrefx.js', array( 'jquery', 'jquery-validate' ) );

}
add_action( 'init', 'calibrefx_register_scripts' );

/**
 * Load default calibrefx scripts
 *
 * since @1.0
 */
function calibrefx_load_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'modernizr' );
	wp_enqueue_script( 'twitter-bootstrap' );
	wp_enqueue_script( 'calibrefx-script' );
	wp_enqueue_script( 'calibrefx-shortcodes' );

	if ( is_singular() AND get_option( 'thread_comments' ) AND comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'superfish', CALIBREFX_JS_URL . '/superfish.js', 'jquery', '', true );

	wp_localize_script( 'calibrefx-script', 'cfx_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ajax_action' => 'cfx_ajax', '_ajax_nonce' => wp_create_nonce( 'calibrefx_ajax_nonce' ) ) );
}

/**
 * Load default calibrefx styles
 *
 * since @1.0
 */
function calibrefx_load_styles() {
	global $wp_styles;

	wp_enqueue_style( 'font-awesome' );
	wp_enqueue_style( 'calibrefx-icons' );
	wp_enqueue_style( 'genericons' );
	wp_enqueue_style( 'jquery-superfish' );
	wp_enqueue_style( 'calibrefx-shortcodes' );

	$calibrefx_default_style = get_theme_support( 'calibrefx-default-styles' );

	if( ! is_child_theme() ){
		wp_enqueue_style( 'calibrefx-fonts', calibrefx_fonts_url(), array(), null );
	}

	wp_enqueue_style( 'twitter-bootstrap' );
	wp_enqueue_style( 'calibrefx-style' );
	wp_enqueue_style( 'calibrefx-child-style', get_stylesheet_uri() );
}

/**
 * This function loads the admin JS files
 *
 */
function calibrefx_load_admin_scripts() {
	global $pagenow;

	// Include media upload script in calibrefx settings page to make upload field in meta options working
	if ( 'admin.php' == $pagenow AND ( isset( $_GET['page'] ) AND 'calibrefx' == esc_attr( $_GET['page'] ) ) AND ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media(); 
	}

	// add_thickbox();
	wp_enqueue_script( 'theme-preview' );
	wp_enqueue_script( 'calibrefx_admin_js', CALIBREFX_JS_URL . '/admin.js', array( 'jquery', 'jquery-sticky', 'wp-color-picker' ), '' );
	wp_enqueue_script( 'jquery-chosen', CALIBREFX_JS_URL . '/jquery.chosen.min.js', 'jquery', '' );
	$params = array(
		'category_checklist_toggle' => __( 'Select / Deselect All', 'calibrefx' ),
		'shortcode_url' => CALIBREFX_SHORTCODE_URL,
		'assets_img_url' => CALIBREFX_IMAGES_URL,
	);
	wp_localize_script( 'calibrefx_admin_js', 'calibrefx_local', $params );

	wp_enqueue_script( 'magnific', CALIBREFX_JS_URL . '/magnific-popup.js', 'jquery', false, true );
}
add_action( 'admin_enqueue_scripts', 'calibrefx_load_admin_scripts' );

/**
 * This function loads the admin CSS files
 *
 */
function calibrefx_load_admin_styles() {
	wp_enqueue_style( 'admin-bar' );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'magnific', CALIBREFX_CSS_URL . '/magnific-popup.css' );
	wp_enqueue_style( 'calibrefx-admin-css', CALIBREFX_CSS_URL . '/calibrefx.admin.css', array() );
	wp_enqueue_style( 'font-awesome' );
	wp_enqueue_style( 'calibrefx-icons' );
	wp_enqueue_style( 'genericons' );
	wp_enqueue_style( 'chosen-css', CALIBREFX_CSS_URL . '/chosen.css' );

	if ( current_theme_supports( 'calibrefx-admin-bar' ) ) {
		wp_enqueue_style( 'calibrefx-admin-bar-css', CALIBREFX_CSS_URL . '/calibrefx.admin.bar.css', array() );
	}
}
add_action( 'admin_enqueue_scripts', 'calibrefx_load_admin_styles' );

function calibrefx_remove_script_version( $src ) {
	$parts = explode( '?', $src );
	if ( false !== strpos( $src, $_SERVER['HTTP_HOST'] ) ) {
		return $parts[0];
	}

	return $src;
}
add_filter( 'script_loader_src', 'calibrefx_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'calibrefx_remove_script_version', 15, 1 );