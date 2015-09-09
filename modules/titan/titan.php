<?php 
global $calibrefx;

$calibrefx->hooks->add( 'wp_enqueue_scripts', 'titan_enqueue_scripts' );
$calibrefx->hooks->add( 'calibrefx_header', 'titan_before_header' );
$calibrefx->hooks->add( 'calibrefx_footer', 'titan_footer' );

// 	lib
function titan_enqueue_scripts () {

	wp_enqueue_script( 'function.mobile', TITAN_URI . '/titan.js', array( 'jquery' ), false, true );
}

function titan_footer () {}

function titan_before_header () {

	global $calibrefx;

	$target = 'calibrefx_before_loop';

	$hooks = $calibrefx->hooks->get_hook();

	$output = '';


	$output .= '<ul>';

	foreach ( $hooks as $key => $value ) {

		$tags = $value;

		$output .= '<li>>'. $key .'';

		$output .= '<ul>';

		foreach ( $tags as $key => $value ) {

			$output .= '<li>--'. $value['function'] .' ( '. $value['priority'] .' )</li>';
		}

		$output .= '</ul>';

		$output .= '<br>';

		$output .= '</li>';
	}

	$output .= '</ul>';

	echo $output;

	exit;
}


/*
>calibrefx_meta
--calibrefx_print_favicon ( 10 )
--calibrefx_do_meta ( 10 )
--calibrefx_do_link_author ( 10 )
--calibrefx_do_fb_og ( 10 )
--calibrefx_load_scripts ( 5 )
--calibrefx_load_styles ( 5 )


>calibrefx_after_post_content
--calibrefx_get_comments_template ( 30 )
--calibrefx_post_meta ( 10 )
--calibrefx_do_author_box_single ( 20 )


>calibrefx_comments
--calibrefx_do_comments ( 10 )


>calibrefx_pings
--calibrefx_do_pings ( 10 )


>calibrefx_list_comments
--calibrefx_default_list_comments ( 10 )


>calibrefx_list_pings
--calibrefx_default_list_pings ( 10 )


>calibrefx_comment_form
--calibrefx_do_comment_form ( 10 )


>calibrefx_before_footer
--calibrefx_do_footer_widgets ( 10 )


>calibrefx_footer
--calibrefx_footer_area ( 10 )


>calibrefx_footer_content
--calibrefx_do_footer ( 10 )


>wp_footer
--calibrefx_footer_scripts ( 10 )
--calibrefx_add_socials_script ( 10 )
--calibrefx_show_tracking_scripts ( 10 )


>template_redirect
--calibrefx_submit_handler ( 99 )


>wp_head
--calibrefx_print_wrap ( 10 )
--calibrefx_header_scripts ( 30 )
--calibrefx_header_custom_styles ( 30 )


>calibrefx_do_header
--calibrefx_do_header ( 10 )


>calibrefx_site_title
--calibrefx_do_site_title ( 10 )


>calibrefx_site_description
--calibrefx_do_site_description ( 10 )


>calibrefx_header
--calibrefx_header_area ( 10 )


>calibrefx_header_right_widget
--calibrefx_do_header_right_widget ( 10 )


>calibrefx_wrapper
--calibrefx_do_open_wrapper ( 0 )


>calibrefx_after_wrapper
--calibrefx_do_close_wrapper ( 20 )


>calibrefx_inner
--calibrefx_do_open_inner ( 0 )


>calibrefx_after_inner
--calibrefx_do_close_inner ( 99 )


>calibrefx_after_content
--calibrefx_get_sidebar ( 10 )


>calibrefx_before_content
--calibrefx_get_sidebar_alt ( 10 )


>calibrefx_sidebar
--calibrefx_do_sidebar ( 10 )


>calibrefx_sidebar_alt
--calibrefx_do_sidebar_alt ( 10 )


>get_header
--calibrefx_setup_custom_layout ( 0 )
--calibrefx_header_body_classes_filter ( 0 )


>calibrefx_after_header
--calibrefx_do_nav ( 10 )
--calibrefx_do_subnav ( 15 )


>calibrefx_before_content_wrapper
--calibrefx_do_inner_wrap_open ( 5 )


>calibrefx_before_loop
--calibrefx_do_breadcrumbs ( 10 )
--calibrefx_do_notification ( 20 )


>calibrefx_loop
--calibrefx_do_loop ( 10 )


>calibrefx_before_post_title


>calibrefx_post_title
--calibrefx_do_post_title ( 10 )


>calibrefx_after_post_title


>calibrefx_before_post_content
--calibrefx_post_info ( 10 )


>calibrefx_post_content
--calibrefx_do_post_image ( 10 )
--calibrefx_do_post_content ( 15 )


>calibrefx_no_post
--calibrefx_do_no_post ( 10 )


>calibrefx_after_loop
--calibrefx_posts_nav ( 20 )


>calibrefx_after_content_wrapper
--calibrefx_do_inner_wrap_close ( 15 )


>pre_ping
--calibrefx_no_self_ping ( 10 )
*/