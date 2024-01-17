<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the LinkedIn Posts Slider widget
 */
function register_linkedin_posts_slider_widget()
{
    if (did_action('elementor/loaded')) {
        require_once plugin_dir_path(__FILE__) . 'slider-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor_Linkedin_Posts_Slider_Widget());
    }
}
add_action('elementor/widgets/widgets_registered', 'register_linkedin_posts_slider_widget');