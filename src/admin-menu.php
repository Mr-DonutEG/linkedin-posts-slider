<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function linkedin_posts_slider_add_admin_menu()
{
    add_menu_page(
        'Linkedin Slider Style Settings', // page title
        'Linkedin Slider Settings', // menu title
        'manage_options', // capability
        'linkedin_posts_slider', // menu slug
        'linkedin_posts_slider_options_page', // function to output the page content
        'dashicons-linkedin'
    );

    add_submenu_page(
        'linkedin_posts_slider', // parent slug
        'Linkedin Posts Table', // page title
        'Posts Table', // menu title
        'manage_options', // capability
        'linkedin_posts_slider_table', // menu slug
        'linkedin_posts_slider_admin_table_page' // function to output the page content
    );

    add_submenu_page(
        'linkedin_posts_slider', // parent slug
        'Linkedin Posts Scrapper Settings', // page title
        'Scrapper Settings', // menu title
        'manage_options', // capability
        'linkedin_scrapper_settings', // menu slug
        'linkedin_posts_scrapper_options_page' // function to output the page content
    );
}

add_action('admin_menu', 'linkedin_posts_slider_add_admin_menu');
