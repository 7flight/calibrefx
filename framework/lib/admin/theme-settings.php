<?php
/**
 * CalibreFx
 *
 * WordPress Themes Framework by CalibreWorks Team
 *
 * @package		CalibreFx
 * @author		CalibreWorks Team
 * @copyright	Copyright (c) 2012, Suntech Inti Perkasa.
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

add_action('pre_update_option_'.CALIBREFX_SETTINGS_FIELD, 'calibrefx_update_theme_settings', 5, 2);
/**
 * When WordPress save the options
 * it only save the submitted field
 * This function is to merge with default or current settings in 
 * serialized data
 */
function calibrefx_update_theme_settings($_newvalue, $_oldvalue ){
    //We merge newvalue and oldvalue
    if(calibrefx_get_option('reset'))
        return $_newvalue;
    
    $_newvalue = $_POST[CALIBREFX_SETTINGS_FIELD];
    
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
    global $_calibrefx_theme_settings_pagehook;
    global $calibrefx_section;
    global $calibrefx_user_ability;
    
    $calibrefx_section = 'general';
    if(!empty($_GET['section']))
        $calibrefx_section = sanitize_text_field($_GET['section']);
    
    if($calibrefx_section === "general"){
        add_meta_box('calibrefx-theme-settings-navigation', __('Navigation Settings', 'calibrefx'), 'calibrefx_theme_settings_navigation_box', $_calibrefx_theme_settings_pagehook, 'main', 'high');
        
        if($calibrefx_user_ability === 'professor'){
            add_meta_box('calibrefx-theme-settings-content-archive', __('Content Archives', 'calibrefx'), 'calibrefx_theme_settings_content_archive_box', $_calibrefx_theme_settings_pagehook, 'side');
            add_meta_box('calibrefx-theme-settings-breadcrumb', __('Breadcrumbs', 'calibrefx'), 'calibrefx_theme_settings_breadcrumb_box', $_calibrefx_theme_settings_pagehook, 'side');
            add_meta_box('calibrefx-theme-settings-comment', __('Comment and Trackbacks', 'calibrefx'), 'calibrefx_theme_settings_comment_box', $_calibrefx_theme_settings_pagehook, 'side');
        }
    }
    
    if($calibrefx_section === "design"){
        
        add_meta_box('calibrefx-theme-settings-layout', __('Default Layout Settings', 'calibrefx'), 'calibrefx_theme_settings_layout_box', $_calibrefx_theme_settings_pagehook, 'main', 'high');
        
         if($calibrefx_user_ability === 'professor'){
            add_meta_box('calibrefx-theme-settings-custom-script', __('Themes Custom Script', 'calibrefx'), 'calibrefx_theme_settings_custom_script_box', $_calibrefx_theme_settings_pagehook, 'side');
         }
    }
    
    if($calibrefx_section === "social"){
        add_meta_box('calibrefx-theme-settings-feeds', __('Feeds Setting', 'calibrefx'), 'calibrefx_theme_settings_feeds_box', $_calibrefx_theme_settings_pagehook, 'main');
        if($calibrefx_user_ability === 'professor'){
            add_meta_box('calibrefx-theme-settings-socials', __('Social Settings', 'calibrefx'), 'calibrefx_theme_settings_socials_box', $_calibrefx_theme_settings_pagehook, 'side');
        }
    }
    
}

/**
 * This function will outout the settings layout to wordpress
 */
function calibrefx_theme_settings_admin() {
    global $_calibrefx_theme_settings_pagehook;
    global $calibrefx_section; 
    
    $section_header = array(
        'general' => __('General', 'calibrefx'),
        'design' => __('Design', 'calibrefx'),
        'social' => __('Social', 'calibrefx'),
    );
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
                    <a class="calibrefx-general" href="<?php echo admin_url("admin.php?page=calibrefx&ability=general&section=".$calibrefx_section);?>">General</a>
                    <a class="calibrefx-professor" href="<?php echo admin_url("admin.php?page=calibrefx&ability=professor&section=".$calibrefx_section);?>">Professor</a>
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
                            <li <?php if($calibrefx_section==="general") echo 'class="current"';?>>
                                <a href="admin.php?page=calibrefx&section=general">General</a><span></span>
                            </li>
                            <li <?php if($calibrefx_section==="design") echo 'class="current"';?>>
                                <a href="admin.php?page=calibrefx&section=design">Design</a><span></span>
                            </li>
                            <li <?php if($calibrefx_section==="social") echo 'class="current"';?>>
                                <a href="admin.php?page=calibrefx&section=social">Social</a><span></span>
                            </li>
                        </ul>
                        <div class="calibrefx-option">
                            <h2><?php echo $section_header[$calibrefx_section];?></h2>
                            <div class="postbox-container main-postbox">
                                <?php
                                do_meta_boxes($_calibrefx_theme_settings_pagehook, 'main', null);
                                ?>
                            </div>

                            <div class="postbox-container side-postbox">
                                <?php
                                do_meta_boxes($_calibrefx_theme_settings_pagehook, 'side', null);
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
            <input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[nav]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[nav]" value="1" <?php checked(1, calibrefx_get_option('nav')); ?> /> <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[nav]"><?php _e("Include Primary Navigation Menu?", 'calibrefx'); ?></label>
        </p>

        <hr class="div" />
    <?php endif; ?>

    <?php if (calibrefx_nav_menu_supported('secondary')) : ?>
        <h4><?php _e('Secondary Navigation', 'calibrefx'); ?></h4>
        <p>
            <input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[subnav]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[subnav]" value="1" <?php checked(1, calibrefx_get_option('subnav')); ?> /> <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[subnav]"><?php _e("Include Secondary Navigation Menu?", 'calibrefx'); ?></label>
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
    if($calibrefx_user_ability === 'professor'){
    ?>
    <p><label><?php _e('Enable Bootstrap', 'calibrefx'); ?></label>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_bootstrap]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_bootstrap]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_bootstrap]" value="1" <?php checked(1, calibrefx_get_option('enable_bootstrap')); ?> /></label>
        <span class="description"><?php printf(__('This option will use Twitter Bootstrap as css and javascript libraries.', 'calibrefx'), admin_url('nav-menus.php')); ?></span>
    </p>
    
    <hr class="div" />
	
	<p><label><?php _e('Enable Responsive Layout', 'calibrefx'); ?></label>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_responsive]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_responsive]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[enable_responsive]" value="1" <?php checked(1, calibrefx_get_option('enable_responsive')); ?> /></label>
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

    <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_home]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_home]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_home]" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_home')); ?> /> <?php _e("Front Page", 'calibrefx'); ?></label>
    <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_single]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_single]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_single]" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_single')); ?> /> <?php _e("Posts", 'calibrefx'); ?></label>
    <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_page]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_page]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_page]" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_page')); ?> /> <?php _e("Pages", 'calibrefx'); ?></label>
    <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_archive]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_archive]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_archive]" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_archive')); ?> /> <?php _e("Archives", 'calibrefx'); ?></label>
    <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_404]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_404]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[breadcrumb_404]" value="1" <?php checked(1, calibrefx_get_option('breadcrumb_404')); ?> /> <?php _e("404 Page", 'calibrefx'); ?></label>
    <?php
}

/**
 * Show breadcrumb box inside Theme Settings
 */
function calibrefx_theme_settings_comment_box() {
    ?>

    <p><label><?php _e('Enable Comments', 'calibrefx'); ?></label>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_posts]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_posts]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_posts]" value="1" <?php checked(1, calibrefx_get_option('comments_posts')); ?> /> <?php _e("on posts?", 'calibrefx'); ?></label>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_pages]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_pages]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[comments_pages]" value="1" <?php checked(1, calibrefx_get_option('comments_pages')); ?> /> <?php _e("on pages?", 'calibrefx'); ?></label>
    </p>

    <p><label><?php _e('Enable Trackbacks', 'calibrefx'); ?></label>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_posts]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_posts]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_posts]" value="1" <?php checked(1, calibrefx_get_option('trackbacks_posts')); ?> /> <?php _e("on posts?", 'calibrefx'); ?></label>
        <label for="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_pages]"><input type="checkbox" name="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_pages]" id="<?php echo CALIBREFX_SETTINGS_FIELD; ?>[trackbacks_pages]" value="1" <?php checked(1, calibrefx_get_option('trackbacks_pages')); ?> /> <?php _e("on pages?", 'calibrefx'); ?></label>
    </p>

    <p><span class="description"><?php _e("You can generally enabled/disabled comments and trackbacks per post/page.", 'calibrefx'); ?></span></p>
    <?php
}

/**
 * This function calibrefx_theme_settings_socials_box is to show feeds setting
 */
function calibrefx_theme_settings_feeds_box() { ?>
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