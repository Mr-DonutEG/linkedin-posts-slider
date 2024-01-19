<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function register_slider_widget($widgets_manager)
{
    //require_once(__DIR__ . '/widgets/slider-widget.php');
    require_once plugin_dir_path(__FILE__) . 'slider-widget.php';
    $widgets_manager->register(new \Elementor_Linkedin_Posts_Slider_Widget());
}

add_action('elementor/widgets/register', 'register_slider_widget');

/**
 * Register scripts and styles for Elementor test widgets.
 */
function elementor_lps_widget_dependencies()
{

    wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));
    wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);

    wp_register_style('linkedin-slider-style', plugins_url('../public/styles.css', __FILE__));
    // Localize the script with the AJAX URL
    wp_localize_script('swiper-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        // You can add more data here if needed
    ));
}
add_action('wp_enqueue_scripts', 'elementor_lps_widget_dependencies');
