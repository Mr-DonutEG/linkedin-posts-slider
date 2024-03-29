<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin scrapping linkedin posts from specific company profile and registering an elementor widget to show a slider of the posts.
Version: 3.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

function enqueue_custom_scripts_and_styles()
{
  wp_enqueue_style('custom-style', plugins_url('style.css', __FILE__));
  wp_enqueue_script('custom-script', plugins_url('script.js', __FILE__), array('jquery', 'jquery-ui-sortable'), null, true);

  // Localize script with nonces
  wp_localize_script('custom-script', 'my_ajax_object', array(
    'ajax_url' => admin_url('admin-ajax.php'),
    'publish_unpublish_nonce' => wp_create_nonce('publish_unpublish_nonce'),
    'delete_post_nonce' => wp_create_nonce('delete_post_nonce')
  ));
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts_and_styles');

// Include the new files
require_once plugin_dir_path(__FILE__) . 'src/widget-registration.php';
//require_once plugin_dir_path(__FILE__) . 'src/options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/scrapper-options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/activation-deactivation.php';
require_once plugin_dir_path(__FILE__) . 'src/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'src/ajax-actions.php';
//require_once plugin_dir_path(__FILE__) . 'src/linkedin-posts-syncing.php';
require_once plugin_dir_path(__FILE__) . 'src/table-page.php';

// Pass the main file path to the activation and deactivation functions
linkedin_posts_slider_set_activation_hook(__FILE__);
linkedin_posts_slider_set_deactivation_hook(__FILE__);

function move_row()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'lps_synced_posts';

  // Check if necessary data is set in the AJAX request
  if (!isset($_POST['id'])) {
    wp_send_json_error('No data received');
    return;
  }

  // Sanitize and validate the data
  $id = intval($_POST['id']);
  $action = sanitize_text_field($_POST['action']);

  // Fetch current post_order and IDs
  $rows = $wpdb->get_results("SELECT id, post_order FROM $table_name ORDER BY post_order ASC");

  // Find the index of the row to be moved
  $index = array_search($id, array_column($rows, 'id'));

  if ($index !== false && $index >= 0 && $index < count($rows)) {
    if ($action === 'move_up' && $index > 0) {
      // Step 3: Swap post_order values for moving up
      $swap_index = $index - 1;
    } elseif ($action === 'move_down' && $index < count($rows) - 1) {
      // Step 3: Swap post_order values for moving down
      $swap_index = $index + 1;
    } else {
      wp_send_json_error('Invalid move');
      return;
    }

    // Step 4: Update the database
    $wpdb->query("START TRANSACTION");

    $wpdb->update(
      $table_name,
      array('post_order' => $rows[$swap_index]->post_order),
      array('id' => $id)
    );

    $wpdb->update(
      $table_name,
      array('post_order' => $rows[$index]->post_order),
      array('id' => $rows[$swap_index]->id)
    );

    $wpdb->query("COMMIT");

    wp_send_json_success('Row moved successfully');
  } else {
    wp_send_json_error('Invalid ID or index');
  }
}
add_action('wp_ajax_move_up', 'move_row');
add_action('wp_ajax_move_down', 'move_row');

//register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
//register_activation_hook(__FILE__, 'linkedin_posts_slider_activate');