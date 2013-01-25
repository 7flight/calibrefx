<?php
/**
 * CalibreFx
 *
 * WordPress Themes Framework by CalibreFx Team
 *
 * @package		CalibreFx
 * @author		CalibreFx Team
 * @copyright	Copyright (c) 2012, Suntech Inti Perkasa.
 * @license		Commercial
 * @link		http://www.calibrefx.com
 * @since		Version 1.0
 * @filesource 
 *
 * WARNING: This file is part of the core CalibreFx framework. DO NOT edit
 * this file under any circumstances. 
 *
 * Contain facebook comment widgets class
 * extend from WP_Widget Class
 *
 * @package CalibreFx
 */
class CFX_Facebook_Comment_Widget extends WP_Widget {

    protected $defaults;

    /**
     * Constructor
     */
    function __construct() {

        $this->defaults = array(
            'title' => '',
            'facebook_url' => '',
            'facebook_width' => 470,
            'facebook_number_posts' => 2,
        );

        $widget_ops = array(
            'classname' => 'facebook-comment-widget',
            'description' => __('Display facebook comment box', 'calibrefx'),
        );

        $control_ops = array(
            'id_base' => 'facebook-comment',
            'width' => 200,
            'height' => 250,
        );

        $this->WP_Widget('facebook-comment', __('CalibreFx - Facebook Comment Box', 'calibrefx'), $widget_ops, $control_ops);
    }

    /**
     * Display widget content.
     *
     * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget
     */
    function widget($args, $instance) {
        extract($args);
        $instance = wp_parse_args((array) $instance, $this->defaults);

        $featured_page = new WP_Query(array('page_id' => $instance['page_id']));

        echo $before_widget . '<div class="facebook-comment">';

        if (!empty($instance['title']))
            echo $before_title . apply_filters('widget_title', $instance['title'], $instance, $this->id_base) . $after_title;

        //Widget Body Start
        ?>
        <div class="fb-comments" data-href="<?php echo $instance['facebook_url']; ?>" 
             data-num-posts="<?php echo $instance['facebook_number_posts']; ?>" 
             data-width="<?php echo $instance['facebook_width']; ?>"></div>
        <?php
        //Widget Body Stop

        echo '</div>' . $after_widget;
    }

    /**
     * Update a particular instance.
     */
    function update($new_instance, $old_instance) {

        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    /**
     * Display the settings update form.
     */
    function form($instance) {
        $instance = wp_parse_args((array) $instance, $this->defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'calibrefx'); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" class="widefat" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('facebook_url'); ?>"><?php _e('Facebook Url', 'calibrefx'); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id('facebook_url'); ?>" name="<?php echo $this->get_field_name('facebook_url'); ?>" value="<?php echo esc_attr($instance['facebook_url']); ?>" class="widefat" />
        </p>

        <hr class="div" />

        <p>
            <label for="<?php echo $this->get_field_id('facebook_width'); ?>"><?php _e('Width', 'calibrefx'); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id('facebook_width'); ?>" name="<?php echo $this->get_field_name('facebook_width'); ?>" value="<?php echo esc_attr($instance['facebook_width']); ?>" class="widefat" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('facebook_number_posts'); ?>"><?php _e('Number Of Posts', 'calibrefx'); ?>:</label>
            <input type="text" id="<?php echo $this->get_field_id('facebook_number_posts'); ?>" name="<?php echo $this->get_field_name('facebook_number_posts'); ?>" value="<?php echo esc_attr($instance['facebook_number_posts']); ?>" class="widefat" />
        </p>

        <?php
    }

}