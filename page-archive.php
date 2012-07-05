<?php
/* Template Name: Archive
 *
 * CalibreFx Framework
 *
 * WordPress Themes by CalibreFx Team
 *
 * @package		CalibreFx
 * @author		CalibreFx Team
 * @authorlink	http://www.calibrefx.com
 * @copyright	Copyright (c) 2012, Suntech Inti Perkasa.
 * @license		Commercial
 * @link		http://www.calibrefx.com
 * @since		Version 1.0
 * @filesource 
 *
 * CalibreFx Archive Template file
 *
 * @package CalibreFx
 */

remove_action( 'calibrefx_post_content', 'calibrefx_do_post_content' );
add_action( 'calibrefx_post_content', 'calibrefx_do_archive_content' );

/**
 * CalibreFx Loop for blog bage
 *
 * It's just like the default loop except it is used for displaying blog post category
 *
 */
function calibrefx_do_archive_content() { ?>

	<div class="archive-page">

		<h4><?php _e( 'Pages:', 'calibrefx' ); ?></h4>
		<ul>
			<?php wp_list_pages( 'title_li=' ); ?>
		</ul>

		<h4><?php _e( 'Categories:', 'calibrefx' ); ?></h4>
		<ul>
			<?php wp_list_categories( 'sort_column=name&title_li=' ); ?>
		</ul>

	</div><!-- end .archive-page-->

	<div class="archive-page">

		<h4><?php _e( 'Authors:', 'calibrefx' ); ?></h4>
		<ul>
			<?php wp_list_authors( 'exclude_admin=0&optioncount=1' ); ?>
		</ul>

		<h4><?php _e( 'Monthly:', 'calibrefx' ); ?></h4>
		<ul>
			<?php wp_get_archives( 'type=monthly' ); ?>
		</ul>

		<h4><?php _e( 'Recent Posts:', 'calibrefx' ); ?></h4>
		<ul>
			<?php wp_get_archives( 'type=postbypost&limit=100' ); ?>
		</ul>

	</div><!-- end .archive-page-->

<?php
}

calibrefx();