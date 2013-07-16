<?php defined('CALIBREFX_URL') OR exit();
/**
 * CalibreFx Framework
 *
 * WordPress Themes Framework by CalibreFx Team
 *
 * @package     CalibreFx
 * @author      CalibreFx Team
 * @authorlink  http://www.calibrefx.com
 * @copyright   Copyright (c) 2012-2013, CalibreWorks. (http://www.calibreworks.com/)
 * @license     GNU GPL v2
 * @link        http://www.calibrefx.com
 * @filesource 
 *
 * WARNING: This file is part of the core CalibreFx framework. DO NOT edit
 * this file under any circumstances. 
 *
 * This define the framework constants
 *
 * @package CalibreFx
 */

abstract class CFX_Admin {

    /**
     * Hold pagehook value when menu is registered
     *
     * @var string
     */
    public $pagehook;

    /**
     * ID of the admin menu and settings page.
     *
     * @var string
     */
    public $page_id;

    /**
     * Name of the settings field in the options table.
     *
     * @var string
     */
    public $settings_field;

    /**
     * Hold default settings of the settings option
     *
     * @var string
     */
    public $default_settings;
    
    /**
     * Hold model object
     *
     * @var object
     */
    public $_model;


    public $_submit_url = 'options.php';

    /**
     * Initialize our admin area
     * 
     */
    public function initialize() {
        global $calibrefx;

        $this->settings_field = $this->_model->get_settings_field();
        
        //define our security filter
        $this->security_filters();
        
        add_action('calibrefx_hidden_fields', array($this,'hidden_fields'));
        add_action('admin_init', array($this, 'register_settings'), 5);
        add_action('admin_init', array($this, 'settings_init'), 20);

        add_action('admin_notices', array($this, 'notices'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        do_action('calibrefx_before_save_core');

        /** Add a sanitizer/validator */
        add_filter('pre_update_option_' . $this->settings_field, array(&$this, 'save'), 10, 2);

        do_action('calibrefx_after_save_core');
        
        //Removed by Fadhel
        //This will allow to cross save calibrefx themes settings
        /*if($this->settings_field != $calibrefx->theme_settings_m->get_settings_field()){
            do_action('calibrefx_before_save_core');
            $this->save_core();
            do_action('calibrefx_after_save_core');
            //add_filter('pre_update_option_' . $calibrefx->theme_settings_m->get_settings_field(), array(&$this, 'save_core'), 10, 2);            
        }*/
    }

    /**
     * Register Our Security Filters
     *
     * $return void
     */
    abstract public function security_filters();
    
    /**
     * Register our meta boxes
     *
     * $return null
     */
    abstract public function meta_boxes();

    /**
     * Register our meta sections
     *
     * $return null
     */
    abstract public function meta_sections();

    public function hidden_fields(){
        
    }

    /**
     * Save our settings option
     *
     * $return array
     */
    public function save($_newvalue, $_oldvalue) { 
        //We merge newvalue and oldvalue
        if (calibrefx_get_option('reset', $this->_model)) {
            return $_newvalue;
        }

        if( !empty($_POST['calibrefx_do_import']) ){
            return $_newvalue;
        }
        
        //Get the value from post settings
        $_newvalue = $_POST[$this->settings_field]; 
        
        //merge value from old settings
        if(!is_array($_oldvalue)) $_oldvalue = array();
        if(!is_array($_newvalue)) $_newvalue = array();
        
        $_newvalue = array_merge($_oldvalue, $_newvalue);
        
        //We merge with default value too
        $_newvalue = array_merge((array)$this->default_settings, $_newvalue);

        if(!empty($_newvalue)){
            //We sanitizing
            $CFX = & calibrefx_get_instance();
            $_newvalue = $CFX->security->sanitize_input($this->settings_field, $_newvalue);
        }
        
        return $_newvalue;
    }

    /**
     * Save our settings option
     *
     * $return array
     */
    public function save_core() {
        global $calibrefx;

        $calibrefx_settings_field = $calibrefx->theme_settings_m->get_settings_field(); 
        
        if(!isset($_POST[$calibrefx_settings_field])) return;

        //Get the value from post settings
        $_newvalue = $_POST[$calibrefx_settings_field];
        
        if(empty($_newvalue)) return;

        $_oldvalue = $calibrefx->theme_settings_m->get_all();
        
        //merge value from old settings
        $_newvalue = array_merge($_oldvalue, $_newvalue);
        
        //We merge with default value too
        $_newvalue = array_merge((array)$calibrefx->theme_settings->default_settings, $_newvalue);

        //@TODO: Need to sanitize before save
        return $calibrefx->theme_settings_m->save($_newvalue);
    }

    /**array_merge
     * Register the settings option in wp_options.
     *
     * @return null Returns early if not on the correct admin page.
     */
    public function register_settings() {

        /** If this page doesn't store settings, no need to register them */
        if (!$this->settings_field)
            return;

        register_setting($this->settings_field, $this->settings_field);
        add_option($this->settings_field, $this->default_settings);

        if (!isset($_REQUEST['page']) || $_REQUEST['page'] != $this->page_id)
            return;

        if (calibrefx_get_option('reset', $this->_model)) {
            if (update_option($this->settings_field, $this->default_settings))
                calibrefx_admin_redirect($this->page_id, array('reset' => 'true'));
            else
                calibrefx_admin_redirect($this->page_id, array('error' => 'true'));
            exit;
        }
    }

    /**
     * Display notices on the save or reset of settings.
     *
     * @return null Returns early if not on the correct admin page.
     */
    public function notices() {

        if (!isset($_REQUEST['page']) || $_REQUEST['page'] != $this->page_id)
            return;
        
        if (isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated'] == 'true')
            echo '<div id="message" class="updated"><p><strong>' . __('Settings saved.', 'calibrefx') . '</strong></p></div>';
        elseif (isset($_REQUEST['reset']) && 'true' == $_REQUEST['reset'])
            echo '<div id="message" class="updated"><p><strong>' . __('Settings reset.', 'calibrefx') . '</strong></p></div>';
        elseif (isset($_REQUEST['error']) && $_REQUEST['error'] == 'true')
            echo '<div id="message" class="updated"><p><strong>' . __('Settings not saved. Error occured.', 'calibrefx') . '</strong></p></div>';
        elseif (isset($_REQUEST['import']) && $_REQUEST['import'] == 'true')
            echo '<div id="message" class="updated"><p><strong>' . __('Import Settings Success.', 'calibrefx') . '</strong></p></div>';
    }

    public function settings_init() {
        add_action('load-' . $this->pagehook, array($this, 'scripts'));
        add_action('load-' . $this->pagehook, array($this, 'styles'));
        add_action('load-' . $this->pagehook, array($this, 'meta_sections'));
        add_action('load-' . $this->pagehook, array($this, 'meta_boxes'));
    }

    public function scripts() {
        wp_enqueue_script('common');
        wp_enqueue_script('wp-lists');
        wp_enqueue_script('postbox');
    }

    public function styles() {
        wp_enqueue_style('calibrefx_admin_css');
    }

    public function dashboard() {
        global $calibrefx_sections, $calibrefx_current_section, $calibrefx_user_ability;
        $this->_submit_url = apply_filters('calibrefx_'.$calibrefx_current_section.'_form_url', 'options.php');
        //$this->_submit_url = str_replace('php', '.php', $this->_submit_url);
        ?>
        <div id="<?php echo $this->settings_field;?>-page" class="wrap calibrefx-metaboxes <?php echo $calibrefx_current_section; ?>">
            <form method="post" action="<?php echo $this->_submit_url; ?>" enctype="multipart/form-data">
                <?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
                <?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
                <?php settings_fields($this->settings_field); // important! ?>
                <?php do_action('calibrefx_hidden_fields'); ?>

                <div class="calibrefx-header">
                    <div class="calibrefx-option-logo">
                        <a target="_blank" href="http://www.calibrefx.com" title="CalibreFx v<?php echo FRAMEWORK_VERSION; ?>">&nbsp;</a>
                    </div>
                    <div class="calibrefx-ability">
                        <span class="calibrefx-ability-label"><?php _e('Advanced Mode', 'calibrefx'); ?></span>
                        <?php
                            if($calibrefx_user_ability == 'professor'){
                                echo '<a href="'.admin_url("admin.php?page=".$this->page_id."&ability=basic&section=" . $calibrefx_current_section).'"><img src="'.CALIBREFX_IMAGES_URL.'/on-toggle.png" alt="advanced mode on" /></a>';
                            }else{
                                echo '<a href="'.admin_url("admin.php?page=".$this->page_id."&ability=professor&section=" . $calibrefx_current_section).'"><img src="'.CALIBREFX_IMAGES_URL.'/off-toggle.png" alt="advanced mode off" /></a>';
                            }
                        ?>
                    </div>
                </div>
                <div class="calibrefx-content">
                    <div class="calibrefx-submit-button">
                        <button type="submit" class="calibrefx-h2-button calibrefx-settings-submit-button"><i class="icon-save"></i><?php _e('Save Settings', 'calibrefx') ?></button>
                        <button type="submit" class="calibrefx-h2-button calibrefx-settings-reset-button" name="<?php echo $this->settings_field; ?>[reset]" onclick="return calibrefx_confirm('<?php echo esc_js(__('Are you sure you want to reset?', 'calibrefx')); ?>');"><i class="icon-reset"></i><?php _e('Reset Settings', 'calibrefx'); ?></button>
                    </div>
                    <div class="metabox-holder">
                        <div class="calibrefx-tab">
                            <ul class="calibrefx-tab-option">
                                <?php
                                foreach ($calibrefx_sections as $section) {
                                    $current_class = ($calibrefx_current_section === $section['slug']) ? 'class="current"' : '';
                                    $section_link = admin_url('admin.php?page=' . $this->page_id . '&section=' . $section['slug']);
                                    echo "<li $current_class><a href='$section_link'>" . $section['title'] . "</a></li>";
                                }
                                ?>
                            </ul>
                            <div class="calibrefx-option">
                                
                                <div class="postbox-container main-postbox">
                                    <?php
                                    calibrefx_do_meta_sections($calibrefx_current_section, $this->pagehook, 'main', null);
                                    ?>
                                </div>

                                <div class="postbox-container side-postbox">
                                    <?php
                                    calibrefx_do_meta_sections($calibrefx_current_section, $this->pagehook, 'side', null);
                                    ?>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="calibrefx-submit-button calibrefx-bottom">
                        <button type="submit" class="calibrefx-h2-button calibrefx-settings-submit-button"><i class="icon-save"></i><?php _e('Save Settings', 'calibrefx') ?></button>
                        <button type="submit" class="calibrefx-h2-button calibrefx-settings-reset-button" name="<?php echo $this->settings_field; ?>[reset]" onclick="return calibrefx_confirm('<?php echo esc_js(__('Are you sure you want to reset?', 'calibrefx')); ?>');"><i class="icon-reset"></i><?php _e('Reset Settings', 'calibrefx'); ?></button>
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
                postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
                            
                postboxes._mark_area = function() {
                    var visible = $('div.postbox:visible').length, side = $('#post-body #side-sortables');

                    $('#<?php echo $this->pagehook; ?> .meta-box-sortables:visible').each(function(n, el){
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

                // Detect the higher height
                //equalize_sidebar_height();
            });

            equalize_sidebar_height = function(){
                var option_list_height = jQuery('.calibrefx-tab-option'),
                    option_height = jQuery('.calibrefx-option');

                if(option_height.outerHeight() > option_list_height.outerHeight()){
                    option_list_height.height(option_height.outerHeight() + 13);
                }else{
                    option_height.height(option_list_height.outerHeight());
                }
            }
            //]]>
        </script>
        <?php
    }

}