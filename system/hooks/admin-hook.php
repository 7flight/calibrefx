<?php
/**
 * This admin hook only run in admin area
 */

// This function adds the top-level menu
function calibrefx_register_admin_menu() {
	global $menu, $calibrefx;

	$calibrefx->load->library( 'theme_settings' );

	$calibrefx->theme_settings->pagehook = add_theme_page( __( 'Theme Settings', 'calibrefx' ), 'Theme Settings', 'edit_theme_options', 'calibrefx', array( $calibrefx->theme_settings, 'dashboard' ) );
}
add_action( 'admin_menu', 'calibrefx_register_admin_menu' );

/**
 *
 * Make custom login box
 *
 * @access public
 */
function calibrefx_login_logo() {

	$background_image = apply_filters( 'calibrefx_login_logo_url', CALIBREFX_IMAGES_URL . '/calibrefx-logo.png' );

	echo '<style type="text/css">
			html, body { border: 0 !important;}
			body.login { background: #F5F5F5; }

			div#login { width: 400px !important; }
			div#login h1 a { width:298px !important; background-size: 298px 66px; padding-bottom: 0; height: 70px !important; background-image: url( '.$background_image.' ) !important; background-repeat:no-repeat; }
			div#login form { -moz-box-shadow: 1px 1px 5px #EEEEEE, -1px -1px 5px #EEEEEE ; -webkit-box-shadow: 1px 1px 5px #EEEEEE, -1px -1px 5px #EEEEEE; box-shadow: 1px 1px 5px #EEEEEE, -1px -1px 5px #EEEEEE; }
			div#login form label { cursor:pointer; }
			div#login form p.submit { margin-bottom: 0 !important; }
			div#login form#lostpasswordform { padding-bottom: 16px !important; } div#login form#lostpasswordform p.submit { float: none !important; } div#login form#lostpasswordform input[type="submit"] { width: 100% !important; }
			div#login form#registerform { padding-bottom: 16px !important; } div#login form#registerform p.submit { float: none !important; margin-top: -10px !important; } div#login form#registerform input[type="submit"] { width: 100% !important; }
			div#login form#registerform p#reg_passmail { font-style: italic !important; }
			div#login p.submit::after{ clear: both; }
			div#login p.submit::before, div#login p.submit::after{ display: table; content: \'\';  }
		</style>';
}
add_action( 'login_head', 'calibrefx_login_logo' );

/**
 * Change url in logo login
 */
function calibrefx_wp_login_url() {
	return apply_filters( 'calibrefx_wp_login_url', FRAMEWORK_URL );
}
add_filter( 'login_headerurl', 'calibrefx_wp_login_url' );

/**
 * Change the logo title in login page
 */
function calibrefx_wp_login_title() {
	return apply_filters( 'calibrefx_wp_login_title', __( 'Powered By ', 'calibrefx' ) . FRAMEWORK_NAME . ' ' . FRAMEWORK_VERSION );
}
add_filter( 'login_headertitle', 'calibrefx_wp_login_title' );

function calibrefx_create_legal_page(){
	global $calibrefx;

	check_ajax_referer( 'cfx_create-legal-page_' . $_POST['param'] );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( __( 'ERROR: You lack permissions to create posts.', 'calibrefx' ) );
	}

	if ( empty( $_POST['action'] ) OR empty( $_POST['param'] ) ) {
		die( __( 'ERROR: No slug was passed to the AJAX callback.', 'calibrefx' ) );
	}

	$name = $_POST['name'];
	$url = $_POST['url'];
	$info = $_POST['info'];

	$response = wp_remote_post( 'http://www.calibreworks.com/tosgen/api.php', array(
			'method'      => 'POST',
			'timeout'     => 60,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'company_name' => $_POST['name'],
				'company_url'  => $_POST['url'],
				'company_info' => $_POST['info'],
				),
			'cookies' => array(),
		)
	);

	$json = json_decode( $response['body'] );

	$asp = $json->data->asp;
	$cn = $json->data->cn;
	$disclaimer = $json->data->disclaimer;
	$dmca = $json->data->dmca;
	$federal = $json->data->federal;
	$privacy = $json->data->privacy;
	$social = $json->data->social;
	$terms = $json->data->terms;

	global $user_ID;

	switch ( $_POST['param'] ) {
		case 'asp':
			$new_post = array(
				'post_title'    => 'Anti-Spam Policy',
				'post_content'  => $asp,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		case 'cn':
			$new_post = array(
				'post_title'    => 'Copyright Notice',
				'post_content'  => $cn,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		case 'disclaimer':
			$new_post = array(
				'post_title'    => 'Disclaimer',
				'post_content'  => $disclaimer,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		case 'dmca':
			$new_post = array(
				'post_title'    => 'DMCA Compliance',
				'post_content'  => $dmca,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		case 'federal':
			$new_post = array(
				'post_title'    => 'Federal Trade Commission Compliance',
				'post_content'  => $federal,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		case 'privacy':
			$new_post = array(
				'post_title'    => 'Privacy Policy',
				'post_content'  => $privacy,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		case 'social':
			$new_post = array(
				'post_title'    => 'Social Media Disclosure',
				'post_content'  => $social,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
		case 'tos':
			$new_post = array(
				'post_title'    => 'Terms Of Service & Conditions Of Use',
				'post_content'  => $tos,
				'post_status'   => 'publish',
				'post_date'     => date( 'Y-m-d H:i:s' ),
				'post_author'   => $user_ID,
				'post_type'     => 'page',
				'post_category' => array( 0 )
			);
			break;
		default:
			echo 0;
			die;
			break;
	}

	$post_id = wp_insert_post( $new_post );

	echo 1;
	exit;
}
add_action( 'wp_ajax_create-legal-page', 'calibrefx_create_legal_page' );