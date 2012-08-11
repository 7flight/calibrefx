<?php
/**
 * CalibreFx
 *
 * WordPress Themes Framework by CalibreWorks Team
 *
 * @package		CalibreFx
 * @author		CalibreWorks Team
 * @copyright           Copyright (c) 2012, Suntech Inti Perkasa.
 * @license		Commercial
 * @link		http://www.calibrefx.com
 * @since		Version 1.0
 * @filesource 
 *
 * WARNING: This file is part of the core CalibreFx framework. DO NOT edit
 * this file under any circumstances. 
 *
 * This File will handle theme-settings and provide default settings
 *
 * @package CalibreFx
 */
add_action('pre_update_option_' . CALIBREFX_SETTINGS_FIELD, 'calibrefx_update_theme_settings', 5, 2);

/**
 * When WordPress save the options
 * it only save the submitted field
 * This function is to merge with default or current settings in 
 * serialized data
 */
function calibrefx_update_theme_settings($_newvalue, $_oldvalue) {
    //We merge newvalue and oldvalue
    if (calibrefx_get_option('reset')) {
        return $_newvalue;
    }
    
    //Get the value from post settings
    $_newvalue = $_POST[CALIBREFX_SETTINGS_FIELD];
    
    //merge value from old settings
    $_newvalue = array_merge($_oldvalue, $_newvalue);
    
    //We merge with default value too
    $_newvalue = array_merge(calibrefx_theme_settings_defaults(), $_newvalue);
    
    return $_newvalue;
}

add_action('admin_init', 'calibrefx_register_theme_settings', 5);

/**
 * This function will save or reset settings
 */
function calibrefx_register_theme_settings() {
    register_setting(CALIBREFX_SETTINGS_FIELD, CALIBREFX_SETTINGS_FIELD);
    add_option(CALIBREFX_SETTINGS_FIELD, calibrefx_theme_settings_defaults());

    if (!isset($_REQUEST['page']) || $_REQUEST['page'] != 'calibrefx')
        return;

    if (calibrefx_get_option('reset')) {
        update_option(CALIBREFX_SETTINGS_FIELD, calibrefx_theme_settings_defaults());

        calibrefx_admin_redirect('calibrefx', array('reset' => 'true'));
        exit;
    }
}

add_action('admin_notices', 'calibrefx_theme_settings_notice');

/**
 * This function will show notification after save/reset settings
 */
function calibrefx_theme_settings_notice() {

    if (!isset($_REQUEST['page']) || $_REQUEST['page'] != 'calibrefx')
        return;

    if (isset($_REQUEST['reset']) && 'true' == $_REQUEST['reset'])
        echo '<div id="message" class="updated"><p><strong>' . __('Settings reset.', 'calibrefx') . '</strong></p></div>';
    elseif (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true')
        echo '<div id="message" class="updated"><p><strong>' . __('Settings saved.') . '</strong></p></div>';
}

add_action('admin_menu', 'calibrefx_theme_settings_init');

/**
 * This function will load scripts, styles and settings field
 */
function calibrefx_theme_settings_init() {
    global $_calibrefx_theme_settings_pagehook;

    add_action('load-' . $_calibrefx_theme_settings_pagehook, 'calibrefx_theme_settings_scripts');
    add_action('load-' . $_calibrefx_theme_settings_pagehook, 'calibrefx_theme_settings_styles');
    add_action('load-' . $_calibrefx_theme_settings_pagehook, 'calibrefx_theme_settings_boxes');
}

/**
 * This function load required javascripts 
 */
function calibrefx_theme_settings_scripts() {
    wp_enqueue_script('common');
    wp_enqueue_script('wp-lists');
    wp_enqueue_script('postbox');
}

/**
 * This function load required styles 
 */
function calibrefx_theme_settings_styles() {
    wp_enqueue_style('calibrefx_admin_css');
}

/**
 * This function load meta boxes
 */
function calibrefx_theme_settings_boxes() {
    global $_calibrefx_theme_settings_pagehook, $calibrefx_current_section;
    
    calibrefx_clear_meta_section();
    
    calibrefx_add_meta_section('general', __('General', 'calibrefx'));
    calibrefx_add_meta_section('design', __('Design', 'calibrefx'));
    calibrefx_add_meta_section('social', __('Social', 'calibrefx'));

    calibrefx_add_meta_box('general', 'basic', 'calibrefx-theme-settings-navigation', __('Navigation Settings', 'calibrefx'), 'calibrefx_theme_settings_navigation_box', $_calibrefx_theme_settings_pagehook, 'main', 'high');
    calibrefx_add_meta_box('general', 'professor', 'calibrefx-theme-settings-content-archive', __('Content Archives', 'calibrefx'), 'calibrefx_theme_settings_content_archive_box', $_calibrefx_theme_settings_pagehook, 'side');
    calibrefx_add_meta_box('general', 'professor', 'calibrefx-theme-settings-breadcrumb', __('Breadcrumbs', 'calibrefx'), 'calibrefx_theme_settings_breadcrumb_box', $_calibrefx_theme_settings_pagehook, 'side');
    calibrefx_add_meta_box('general', 'professor', 'calibrefx-theme-settings-comment', __('Comment and Trackbacks', 'calibrefx'), 'calibrefx_theme_settings_comment_box', $_calibrefx_theme_settings_pagehook, 'side');

    calibrefx_add_meta_box('design', 'basic', 'calibrefx-theme-settings-layout', __('Default Layout Settings', 'calibrefx'), 'calibrefx_theme_settings_layout_box', $_calibrefx_theme_settings_pagehook, 'main', 'high');
    calibrefx_add_meta_box('design', 'professor', 'calibrefx-theme-settings-custom-script', __('Themes Custom Script', 'calibrefx'), 'calibrefx_theme_settings_custom_script_box', $_calibrefx_theme_settings_pagehook, 'side');


    calibrefx_add_meta_box('social', 'basic', 'calibrefx-theme-settings-feeds', __('Feeds Setting', 'calibrefx'), 'calibrefx_theme_settings_feeds_box', $_calibrefx_theme_settings_pagehook, 'main');
    calibrefx_add_meta_box('social', 'professor', 'calibrefx-theme-settings-socials', __('Social Settings', 'calibrefx'), 'calibrefx_theme_settings_socials_box', $_calibrefx_theme_settings_pagehook, 'side');


    $calibrefx_current_section = 'general';
    if (!empty($_GET['section'])) {
        $calibrefx_current_section = sanitize_text_field($_GET['section']);
    }
}

/**
 * This function will outout the settings layout to wordpress
 */
function calibrefx_theme_settings_admin() {
    global $_calibrefx_theme_settings_pagehook,
    $calibrefx_sections,
    $calibrefx_current_section;
    ?>
    <div id="calibrefx-theme-settings-page" class="wrap calibrefx-metaboxes">
        <form method="post" action="options.php">
            <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
            <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
            <?php settings_fields(CALIBREFX_SETTINGS_FIELD); // important! ?>
            <input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[calibrefx_version]>" value="<?php echo esc_attr(calibrefx_get_option('calibrefx_version')); ?>" />
            <input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[calibrefx_db_version]>" value="<?php echo esc_attr(calibrefx_get_option('calibrefx_db_version')); ?>" />
            <div class="calibrefx-header">
                <div class="calibrefx-option-logo">
                    <a target="_blank" href="http://www.calibrefx.com" title="CalibreFx v<?php echo FRAMEWORK_VERSION; ?>">&nbsp;</a>
                </div>
                <div class="calibrefx-version">
                    <span>v<?php calibrefx_option('calibrefx_version'); ?> ( Code Name : <?php echo FRAMEWORK_CODENAME; ?>)</span>
                </div>
                <div class="calibrefx-ability">
                    <a class="calibrefx-general" href="<?php echo admin_url("admin.php?page=calibrefx&ability=basic&section=" . $calibrefx_current_section); ?>">Basic</a>
                    <a class="calibrefx-professor" href="<?php echo admin_url("admin.php?page=calibrefx&ability=professor&section=" . $calibrefx_current_section); ?>">Professor</a>
                </div>
            </div>
            <div class="calibrefx-content">
                <div class="calibrefx-submit-button">
                    <input type="submit" class="button-primary calibrefx-h2-button" value="<?php _e('Save Settings', 'calibrefx') ?>" />
                    <input type="submit" class="button-highlighted calibrefx-h2-button" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[reset]" value="<?php _e('Reset Settings', 'calibrefx'); ?>" onclick="return calibrefx_confirm('<?php echo esc_js(__('Are you sure you want to reset?', 'calibrefx')); ?>');" />
                </div>
                <div class="metabox-holder">
                    <div class="calibrefx-tab">
                        <ul class="calibrefx-tab-option">
                            <?php
                            foreach ($calibrefx_sections as $section) {
                                $current_class = ($calibrefx_current_section === $section['slug']) ? 'class="current"' : '';
                                $section_link = admin_url('admin.php?page=calibrefx&section=' . $section['slug']);
                                echo "<li $current_class><a href='$section_link'>" . $section['title'] . "</a><span></span></li>";
                            }
                            ?>
                        </ul>
                        <div class="calibrefx-option">
                            <h2><?php echo $calibrefx_sections[$calibrefx_current_section]['title']; ?></h2>
                            <div class="postbox-container main-postbox">
                                <?php
                                calibrefx_do_meta_sections($calibrefx_current_section, $_calibrefx_theme_settings_pagehook, 'main', null);
                                ?>
                            </div>

                            <div class="postbox-container side-postbox">
                                <?php
                                calibrefx_do_meta_sections($calibrefx_current_section, $_calibrefx_theme_settings_pagehook, 'side', null);
                                ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="calibrefx-submit-button calibrefx-bottom">
                    <input type="submit" class="button-primary calibrefx-h2-button" value="<?php _e('Save Settings', 'calibrefx') ?>" />
                    <input type="submit" class="button-highlighted calibrefx-h2-button" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[reset]" value="<?php _e('Reset Settings', 'calibrefx'); ?>" onclick="return calibrefx_confirm('<?php echo esc_js(__('Are you sure you want to reset?', 'calibrefx')); ?>');" />
                </div>

            </div>
        </form>
    </div>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
            // close postboxes that should be closed
            $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
            // postboxes setup
            postboxes.add_postbox_toggles('<?php echo $_calibrefx_theme_settings_pagehook; ?>');
                    
            postboxes._mark_area = function() {
                var visible = $('div.postbox:visible').length, side = $('#post-body #side-sortables');

                $('#calibrefx-theme-settings-page .meta-box-sortables:visible').each(function(n, el){
                    var t = $(this);

                    if ( visible == 1 || t.children('.postbox:visible').length )
                        t.removeClass('empty-container');
                    else
                        t.addClass('empty-container');
                });

                if ( side.length ) {
                    if ( side.children('.postbox:visible').length )
                        side.removeClass('empty-container');
                    else if ( $('#postbox-container-1').css('width') == '280px' )
                        side.addClass('empty-container');
                }
            };
            postboxes._mark_area();
        });
        //]]>
    </script>
    <?php
}

/**
 * Show navigation setting box
 */
function calibrefx_theme_settings_navigation_box() {
    ?>
    <?php if (calibrefx_nav_menu_supported('primary')) : ?>
        <h4><?php _e('Primary Navigation', 'calibrefx'); ?></h4>
        <p>
            <input type="checkbox" name="" target="calibrefx-settings-nav" value="1" id="calibrefx-settings-checkbox-nav" class="calibrefx-settings-checkbox" <?php checked(1, calibrefx_get_option('nav')); ?> /> <label for="calibrefx-settings-checkbox-nav"><?php _e("Include Primary Navigation Menu?", 'calibrefx'); ?></label>
			<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[nav]" id="calibrefx-settings-nav" value="<?php echo calibrefx_get_option('nav'); ?>" />
        </p>
        <hr class="div" />
    <?php endif; ?>

    <?php if (calibrefx_nav_menu_supported('secondary')) : ?>
        <h4><?php _e('Secondary Navigation', 'calibrefx'); ?></h4>
        <p>
            <input type="checkbox" name="" target="calibrefx-settings-subnav" id="calibrefx-settings-checkbox-subnav" value="1" class="calibrefx-settings-checkbox" <?php checked(1, calibrefx_get_option('subnav')); ?> /> <label for="calibrefx-settings-checkbox-subnav"><?php _e("Include Secondary Navigation Menu?", 'calibrefx'); ?></label>
			<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[subnav]" id="calibrefx-settings-subnav" value="<?php echo calibrefx_get_option('subnav'); ?>" />
        </p>

        <hr class="div" />
    <?php endif; ?>

    <p><span class="description"><?php printf(__('Please build a <a href="%s">custom menu</a>, then assign it to the proper Menu Location.', 'calibrefx'), admin_url('nav-menus.php')); ?></span></p>
    <?php
}

/**
 * Show default layout box
 */
function calibrefx_theme_settings_layout_box() {
    global $calibrefx_user_ability;
    if ($calibrefx_user_ability === 'professor') {
        ?>
        <p><label><?php _e('Enable Bootstrap', 'calibrefx'); ?></label>
            <label for="calibrefx-settings-checkbox-enable-bootstrap">
				<input type="checkbox" name="" id="calibrefx-settings-checkbox-enable-bootstrap" value="1" <?php checked(1, calibrefx_get_option('enable_bootstrap')); ?> target="calibrefx-settings-enable-bootstrap" class="calibrefx-settings-checkbox"  />
			</label>
			<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_bootstrap]" id="calibrefx-settings-enable-bootstrap" value="<?php echo calibrefx_get_option('enable_bootstrap'); ?>" />
            <span class="description"><?php printf(__('This option will use Twitter Bootstrap as css and javascript libraries.', 'calibrefx'), admin_url('nav-menus.php')); ?></span>
        </p>

        <hr class="div" />

        <p><label><?php _e('Enable Responsive Layout', 'calibrefx'); ?></label>
            <label for="calibrefx-settings-checkbox-enable-responsive">
				<input type="checkbox" name="" id="calibrefx-settings-checkbox-enable-responsive" value="1" <?php checked(1, calibrefx_get_option('enable_responsive')); ?> target="calibrefx-settings-enable-responsive" class="calibrefx-settings-checkbox" />
			</label>
			<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_responsive]" id="calibrefx-settings-enable-responsive" value="<?php echo calibrefx_get_option('enable_responsive'); ?>" />
            <span class="description"><?php printf(__('This option will enable responsive layout.', 'calibrefx'), admin_url('nav-menus.php')); ?></span>
        </p>

        <hr class="div" />
    <?php } ?>
    <p>
        <label>Layout Type:</label>
        <select name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[layout_type]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[layout_type]">
            <?php
            $layout_type = apply_filters(
                    'calibrefx_layout_type_options', array(
                'static' => __('Static Layout', 'calibrefx'),
                'fluid' => __('Fluid Layout', 'calibrefx'),
                    )
            );
            foreach ((array) $layout_type as $value => $name)
                echo '<option value="' . esc_attr($value) . '"' . selected(calibrefx_get_option('layout_type'), esc_attr($value), false) . '>' . esc_html($name) . '</option>' . "\n";
            ?>
        </select>

    </p>

    <div id="calibrefx_layout_width">
        <p>
            <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[calibrefx_layout_width]"><?php _e('Layout Width', 'calibrefx'); ?>
                <input type="text" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[calibrefx_layout_width]" value="<?php echo esc_attr(calibrefx_get_option('calibrefx_layout_width')); ?>" size="3" />
                <?php _e('pixels', 'calibrefx'); ?></label>
        </p>

        <p><span class="description"><?php _e('This option will limit the width in pixels size.', 'calibrefx'); ?></span></p>
    </div>

    <hr class="div" />

    <p class="calibrefx-layout-selector">
        <?php
        calibrefx_layout_selector(array('name' => CALIBREFX_SETTINGS_FIELD . '[site_layout]', 'selected' => calibrefx_get_option('site_layout')));
        ?>
    </p>

    <br class="clear" />

    <?php
}

/**
 * Show setting box inside Theme Settings
 */
function calibrefx_theme_settings_custom_script_box() {
    ?>
    <p><?php _e("Custom CSS code will be output at <code>wp_head()</code>:", 'calibrefx'); ?></p>
    <textarea name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[custom_css]" cols="78" rows="8"><?php echo esc_textarea(calibrefx_get_option('custom_css')); ?></textarea>
    <p><span class="description"><?php _e('The <code>wp_head()</code> hook executes immediately before the closing <code>&lt;/head&gt;</code> tag in the document source.', 'calibrefx'); ?></span></p>

    <hr class="div" />

    <p><?php _e("Header script will be output at <code>wp_head()</code>:", 'calibrefx'); ?></p>
    <textarea name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[header_scripts]" cols="78" rows="8"><?php echo esc_textarea(calibrefx_get_option('header_scripts')); ?></textarea>
    <p><span class="description"><?php _e('The <code>wp_head()</code> hook executes immediately before the closing <code>&lt;/head&gt;</code> tag in the document source.', 'calibrefx'); ?></span></p>

    <hr class="div" />

    <p><?php _e("Footer scripts will be output at <code>wp_footer()</code>:", 'calibrefx'); ?></p>
    <textarea name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[footer_scripts]" cols="78" rows="8"><?php echo esc_textarea(calibrefx_get_option('footer_scripts')); ?></textarea>
    <p><span class="description"><?php _e('The <code>wp_footer()</code> hook executes immediately before the closing <code>&lt;/body&gt;</code> tag in the document source.', 'calibrefx'); ?></span></p>
    <?php
}

/**
 * Show content archive box inside Theme Settings 
 */
function calibrefx_theme_settings_content_archive_box() {
    ?>
    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[content_archive]"><?php _e('Select one of the following:', 'calibrefx'); ?></label>
        <select name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[content_archive]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[content_archive]">
            <?php
            $archive_display = apply_filters(
                    'calibrefx_archive_display_options', array(
                'full' => __('Display post content', 'calibrefx'),
                'excerpts' => __('Display post excerpts', 'calibrefx'),
                    )
            );
            foreach ((array) $archive_display as $value => $name)
                echo '<option value="' . esc_attr($value) . '"' . selected(calibrefx_get_option('content_archive'), esc_attr($value), false) . '>' . esc_html($name) . '</option>' . "\n";
            ?>
        </select>
    </p>

    <div id="calibrefx_content_limit_setting">
        <p>
            <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[content_archive_limit]"><?php _e('Limit content to', 'calibrefx'); ?>
                <input type="text" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[content_archive_limit]" value="<?php echo esc_attr(calibrefx_get_option('content_archive_limit')); ?>" size="3" />
                <?php _e('characters', 'calibrefx'); ?></label>
        </p>

        <p><span class="description"><?php _e('This option will limit the text and strip all formatting from the text displayed. Use this option, with "Display post content" in the selected box above.', 'calibrefx'); ?></span></p>
    </div>

    <hr class="div" />

    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[posts_nav]"><?php _e('Select Post Navigation:', 'calibrefx'); ?></label>
        <select name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[posts_nav]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[posts_nav]">
            <?php
            $postnav_display = apply_filters(
                    'calibrefx_post_navigation_options', array(
                'older-newer' => __('older/Newer', 'calibrefx'),
                'prev-next' => __('Previous/Next', 'calibrefx'),
                'numeric' => __('Numeric', 'calibrefx'),
                    )
            );
            foreach ((array) $postnav_display as $value => $name)
                echo '<option value="' . esc_attr($value) . '"' . selected(calibrefx_get_option('posts_nav'), esc_attr($value), false) . '>' . esc_html($name) . '</option>' . "\n";
            ?>
        </select>
    </p>
    <?php
}

/**
 * Show breadcrumb box inside Theme Settings
 */
function calibrefx_theme_settings_breadcrumb_box() {
    ?>
    <p><?php _e("Show Breadcrumb on:", 'calibrefx'); ?></p>

	<!-- breadcrumb breadcrumb_home -->
    <label for="calibrefx-settings-checkbox-breadcrumb-home">
	<input type="checkbox" name="" id="calibrefx-settings-checkbox-breadcrumb-home" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_home')); ?> target="calibrefx-settings-breadcrumb-home" class="calibrefx-settings-checkbox" /> <?php _e("Front Page", 'calibrefx'); ?>
	</label>
	<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_home]" id="calibrefx-settings-breadcrumb-home" value="<?php echo calibrefx_get_option('breadcrumb_home'); ?>" />
	
	<!-- breadcrumb breadcrumb_single -->
    <label for="calibrefx-settings-checkbox-breadcrumb-single">
	<input type="checkbox" name="" id="calibrefx-settings-checkbox-breadcrumb-single" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_single')); ?> target="calibrefx-settings-breadcrumb-single" class="calibrefx-settings-checkbox" /> <?php _e("Posts", 'calibrefx'); ?></label>
	<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_single]" id="calibrefx-settings-breadcrumb-single" value="<?php echo calibrefx_get_option('breadcrumb_single'); ?>" />
	
	<!-- breadcrumb breadcrumb_page -->
    <label for="calibrefx-settings-checkbox-breadcrumb-page">
	<input type="checkbox" name="" id="calibrefx-settings-checkbox-breadcrumb-page" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_page')); ?> target="calibrefx-settings-breadcrumb-page" class="calibrefx-settings-checkbox" /> <?php _e("Pages", 'calibrefx'); ?></label>
	<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_page]" id="calibrefx-settings-breadcrumb-page" value="<?php echo calibrefx_get_option('breadcrumb_page'); ?>" />
	
	<!-- breadcrumb breadcrumb_archive -->
    <label for="calibrefx-settings-checkbox-breadcrumb-archive">
	<input type="checkbox" name="" id="calibrefx-settings-checkbox-breadcrumb-archive" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_archive')); ?> target="calibrefx-settings-breadcrumb-archive" class="calibrefx-settings-checkbox" /> <?php _e("Archives", 'calibrefx'); ?></label>
	<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_archive]" id="calibrefx-settings-breadcrumb-archive" value="<?php echo calibrefx_get_option('breadcrumb_archive'); ?>" />
	
	<!-- breadcrumb breadcrumb_404 -->
    <label for="calibrefx-settings-checkbox-breadcrumb-404">
	<input type="checkbox" name="" id="calibrefx-settings-checkbox-breadcrumb-404" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_404')); ?> target="calibrefx-settings-breadcrumb-404" class="calibrefx-settings-checkbox" /> <?php _e("404 Page", 'calibrefx'); ?></label>
	<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_404]" id="calibrefx-settings-breadcrumb-404" value="<?php echo calibrefx_get_option('breadcrumb_404'); ?>" />
	
	
    <?php
}

/**
 * Show breadcrumb box inside Theme Settings
 */
function calibrefx_theme_settings_comment_box() {
    ?>

    <p><label><?php _e('Enable Comments', 'calibrefx'); ?></label>
		<!-- comment comments_posts -->
        <label for="calibrefx-settings-checkbox-comments-posts">
			<input type="checkbox" name="" id="calibrefx-settings-checkbox-comments-posts" value="1" <?php checked(1, calibrefx_get_option('comments_posts')); ?> target="calibrefx-settings-comments-posts" class="calibrefx-settings-checkbox" /> <?php _e("on posts?", 'calibrefx'); ?>
		</label>
		<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_posts]" id="calibrefx-settings-comments-posts" value="<?php echo calibrefx_get_option('comments_posts'); ?>" />
		
		<!-- comment comments_pages -->
        <label for="calibrefx-settings-checkbox-comments-pages">
			<input type="checkbox" name="" id="calibrefx-settings-checkbox-comments-pages" value="1" <?php checked(1, calibrefx_get_option('comments_pages')); ?> target="calibrefx-settings-comments-pages" class="calibrefx-settings-checkbox" /> <?php _e("on pages?", 'calibrefx'); ?>
		</label>
		<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_pages]" id="calibrefx-settings-comments-pages" value="<?php echo calibrefx_get_option('comments_pages'); ?>" />
    </p>

    <p><label><?php _e('Enable Trackbacks', 'calibrefx'); ?></label>
        <!-- trackback trackbacks_posts -->
		<label for="calibrefx-settings-checkbox-trackbacks-posts">
			<input type="checkbox" name="" id="calibrefx-settings-checkbox-trackbacks-posts" value="1" <?php checked(1, calibrefx_get_option('trackbacks_posts')); ?> target="calibrefx-settings-trackbacks-posts" class="calibrefx-settings-checkbox" /> <?php _e("on posts?", 'calibrefx'); ?>
		</label>
		<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_posts]" id="calibrefx-settings-trackbacks-posts" value="<?php echo calibrefx_get_option('trackbacks_posts'); ?>" />
		
		<!-- trackback trackbacks_pages -->
        <label for="calibrefx-settings-checkbox-trackbacks-pages">
			<input type="checkbox" name="" id="calibrefx-settings-checkbox-trackbacks-pages" value="1" <?php checked(1, calibrefx_get_option('trackbacks_pages')); ?> target="calibrefx-settings-trackbacks-pages" class="calibrefx-settings-checkbox" /> <?php _e("on pages?", 'calibrefx'); ?>
		</label>
		<input type="hidden" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_pages]" id="calibrefx-settings-trackbacks-pages" value="<?php echo calibrefx_get_option('trackbacks_pages'); ?>" />
    </p>

    <p><span class="description"><?php _e("You can generally enabled/disabled comments and trackbacks per post/page.", 'calibrefx'); ?></span></p>
    <?php
}

/**
 * This function calibrefx_theme_settings_socials_box is to show feeds setting
 */
function calibrefx_theme_settings_feeds_box() {
    ?>
    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[feed_uri]"><?php _e('Main Feed URL:', 'calibrefx'); ?></label>
        <input type="text" size="30" value="<?php echo calibrefx_get_option('feed_uri'); ?>" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[feed_uri]" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[feed_uri]">
    </p>
    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_feed_uri]"><?php _e('Comment Feed URL:', 'calibrefx'); ?></label>
        <input type="text" size="30" value="<?php echo calibrefx_get_option('comments_feed_uri'); ?>" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_feed_uri]" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_feed_uri]">
    </p>
    <?php
}

/**
 * This function calibrefx_theme_settings_socials_box is to show social settings
 * Use For Widgets
 */
function calibrefx_theme_settings_socials_box() {
    ?>
    <h4><?php _e('Facebook Settings:', 'calibrefx'); ?></h4>
    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[facebook_admins]"><?php _e('Facebook Admin ID:', 'calibrefx'); ?></label>
        <input type="text" size="30" value="<?php echo calibrefx_get_option('facebook_admins'); ?>" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[facebook_admins]" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[facebook_admins]">
        <span class="description"><?php _e("This will be used in Facebook open graph meta <code>fb:admins</code>.", 'calibrefx'); ?></span>
    </p>
    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[facebook_og_type]"><?php _e('Facebook Page Type:', 'calibrefx'); ?></label>
        <select name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[facebook_og_type]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[facebook_og_type]">
            <?php
            $page_types = apply_filters(
                    'calibrefx_facebook_og_types', array(
                'article' => 'Article',
                'website' => 'Website',
                'blog' => 'Blog',
                'movie' => 'Movie',
                'song' => 'Song',
                'product' => 'Product',
                'book' => 'Book',
                'food' => 'Food',
                'drink' => 'Drink',
                'activity' => 'Activity',
                'sport' => 'Sport',
                    )
            );
            foreach ((array) $page_types as $value => $name)
                echo '<option value="' . esc_attr($value) . '"' . selected(calibrefx_get_option('facebook_og_type'), esc_attr($value), false) . '>' . esc_html($name) . '</option>' . "\n";
            ?>
        </select>
        <span class="description"><?php _e("This will be used in Facebook open graph meta <code>og:type</code>.", 'calibrefx'); ?></span>
    </p>

    <h4><?php _e('Twitter Settings:', 'calibrefx'); ?></h4>
    <p>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[twitter_username]"><?php _e('Twiiter Username:', 'calibrefx'); ?></label>
        <input type="text" size="30" value="<?php echo calibrefx_get_option('twitter_username'); ?>" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[twitter_username]" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[twitter_username]">
    </p>
    <?php
}