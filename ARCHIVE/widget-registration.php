<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function register_slider_widget($widgets_manager)
{
    //require_once(__DIR__ . '/widgets/slider-widget.php');
    require_once plugin_dir_path(__FILE__) . 'slider-widget.php';
    $widgets_manager->register(new \Elementor_Slider_Widget());
}

add_action('elementor/widgets/register', 'register_slider_widget');
