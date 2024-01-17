## Context :
for a wordpress plugin called linkedin posts slider that registers an elementor widget of a posts slider showing linkedin posts of certain company profile and getting this data from an endpoint that is managed alongside the request data and the update intervals using the scrapper settings page in our admin options page and gives the admin the ability to reorder, publish/unpublish and delete posts from a page showing a table of synced posts, the posts are stored in a custom table called `lps_synced_posts`
### file structure:
- `linkedin-posts-slider.php`: the main file of the plugin
- `script.js`: handles functions for controls of the posts in the posts table admin options page 
- `style.css`: have the style of the admin pages 
- `src\admin-menu.php`: this file creates the menu links for the wp admin panel 
- `src\ajax-actions.php`: this file have the ajax functions
- `src\activation-deactivation.php`: this file responsible for creating the `lps_synced_posts` table and prepopulating it and creating the scrapper settings option and setting its default value
- `src\linkedin-posts-slider-admin.css`: this has css used in the style of the scrapper settings page 
- `src\scrapper-options-page.php`: this creates the admin options page of the scrapper settings that has the form 
- `src\slider-widget.php`: this file registers the elementor widget and including the frontend code of the slider and the code for the live preview in the editor
- `src\table-page.php`: this file creates the admin options page of the table of posts 
- `public\styles.css`: this has the style of the slider 
- `public\swiperjs\`: this folder contains the js and css of swiperjs which the slider is built on 

### linkedin-posts-slider.php:
```
<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
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
require_once plugin_dir_path(__FILE__) . 'src/slider-widget.php';
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

register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
register_activation_hook(__FILE__, 'linkedin_posts_slider_activate');


```

## Instructions:
1. for each request consider the whole project while focusing in the reply on the requested edits providing full line by line code changes, mention where are those changes supposed to be and if any new files should be created mention where it should be in the project 
2. for each request if the code changes made will require any other changes in any other file in the project mention it with the exact change 
3. give any suggestions and improvements in a short comment at the end of your response 
4. think step by step 
5. always provide the simplest code and well commented and structured and ensure the clean and working code 
6. avoid using placeholders or uncomplete code parts like this: ``` // ... [Include other functions as required, following the same structure]

/**
 * [Other function definitions as needed]
 */```