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
 */```now 

 ## Request:
 check the functionality of the reorder buttons in the posts table page as they arent working 
 here is the related code file :
 ### src\table-page.php:
 ```
<?php

/**
 * Display the admin table page for managing LinkedIn posts.
 *
 * @return void
 */
function linkedin_posts_slider_admin_table_page()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'lps_synced_posts';

  // Fetch all rows
  $rows = $wpdb->get_results("SELECT * FROM $table_name ORDER BY post_order ASC");

  // Start output
  ob_start();
?>
  <script>

  </script>
  <div class="wrap">
    <h1 style="text-align:center;"><?php echo esc_html(get_admin_page_title()); ?></h1>
    <table class="widefat fixed custom-table" cellspacing="0">
      <thead>
        <tr>
          <th scope="col" class="manage-column column-id" hidden>ID</th>
          <th scope="col" class="manage-column">ID</th>
          <th scope="col" class="manage-column">Thumbnail</th>
          <th scope="col" class="manage-column">Age</th>
          <th scope="col" class="manage-column">Post Text</th>
          <th scope="col" class="manage-column">Actions</th>
          <th scope="col" class="manage-column">Order</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row) : ?>
          <tr class="table-row-class">
            <td class="column-id" hidden>
              <span class="row-id" hidden><?php echo esc_html($row->id); ?></span>
            </td>
            <td><?php echo esc_html($row->id); ?></td>
            <td class="thumbnail-cell">
              <?php
              // Unserialize the images data
              $images = maybe_unserialize($row->images);

              if (!empty($images) && is_array($images)) {
                // Assuming $images is an array of URLs
                echo '<img src="' . esc_url($images[0]) . '" alt="" width="100" height="100" />';
              } else {
                echo 'No image available';
              }
              ?>
            </td>
            <td><?php echo esc_html($row->age); ?></td>
            <td><?php echo wp_trim_words(esc_html($row->copy), 10, '...'); ?></td>
            <td>
              <div class="action-buttons">
                <!-- Remove the form and directly use a button for delete -->
                <button type="button" class="delete-button" data-id="<?php echo esc_attr($row->id); ?>">Delete</button>

                <!-- Publish/Unpublish Button -->
                <button class="publish-button" data-id="<?php echo esc_attr($row->id); ?>" data-published="<?php echo esc_attr($row->published); ?>">
                  <?php echo $row->published ? 'Published' : 'Unpublished'; ?>
                </button>
              </div>


            </td>
            </td>
            <td>
              <div class="up-down-wrapper">
                <button class="up-button">
                  <!-- Up arrow SVG -->
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5L5 12H19L12 5Z" fill="white" />
                  </svg>
                </button>
                <button class="down-button">
                  <!-- Down arrow SVG -->
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 19L5 12H19L12 19Z" fill="white" />
                  </svg>
                </button>
              </div>

            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php
  // Output buffer
  echo ob_get_clean();
}

 ```

 ### script.js:
 ```
 
jQuery(document).ready(function ($) {

    function handlePublishUnpublish(buttonElement) {
        let button = $(buttonElement);
        let id = button.data("id");
        let published = button.data("published");
        button.text('...').addClass('loading');

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'publish_unpublish',
                id: id,
                published: published,
                nonce: my_ajax_object.publish_unpublish_nonce // Added nonce for security
            },
            success: (response) => {
                if (response.success) {
                    button.text(published ? 'Publish' : 'Unpublish').removeClass('loading');
                    button.data("published", !published);
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
                button.text(published ? 'Unpublish' : 'Publish').removeClass('loading');
            }
        });
    }

    function handleDeleteButton(e) {
        e.preventDefault();
        let button = $(this);
        let postId = button.data('id');

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_post',
                id: postId,
                nonce: my_ajax_object.delete_post_nonce // Added nonce for security
            },
            success: (response) => {
                if (response.success) {
                    button.closest('tr').remove(); // Update the selector to remove the row
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }

    function handleUpDownButtonClick() {
        let button = $(this);
        let id = button.closest('tr').find('.row-id').text();
        let action = button.hasClass('up-button') ? 'up' : 'down';

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'move_post',
                id: id,
                direction: action,
                //nonce: my_ajax_object.move_post_nonce // Add nonce for security
            },
            success: (response) => {
                if (response.success) {
                    location.reload(); // Consider a more efficient way to update the order without reloading
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }

    // Event binding for the delete button
    $('.delete-button').on('click', handleDeleteButton);

    // Event binding for the publish/unpublish button
    $('.publish-button').on('click', function () {
        handlePublishUnpublish(this);
    });

    // Toggle button text on hover for publish/unpublish button
    $('.publish-button').hover(function () {
        let button = $(this);
        let published = button.data("published");
        if (published == 1) {
            button.text('Unpublish');
        } else {
            button.text('Publish');
        }
    }, function () {
        let button = $(this);
        let published = button.data("published");
        if (published == 1) {
            button.text('Published');
        } else {
            button.text('Unpublished');
        }
    });

    // Event binding for the up/down reorder buttons
    $('.up-button, .down-button').on('click', handleUpDownButtonClick);
});

 ```

 ### src\ajax-actions.php:
 ```
 <?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// TODO: linkedin_posts_slider_update_status
// TODO: linkedin_posts_slider_process_unsynced_posts


// Include WordPress functions
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Global settings object
$scrapper_settings = array();

// Load and deserialize the scrapper settings
add_action('init', 'load_scrapper_settings');
function load_scrapper_settings()
{
	global $scrapper_settings;
	$settings_string = get_option('lps_scrapper_settings', '');
	$scrapper_settings = maybe_unserialize($settings_string);
}




// Function to publish or unpublish a post
function publish_unpublish()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Check for nonce for security (optional enhancement)
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'publish_unpublish_nonce')) {
		wp_send_json_error('Nonce verification failed');
		return;
	}

	// Check if necessary data is set in the AJAX request
	if (!isset($_POST['id']) || !isset($_POST['published'])) {
		wp_send_json_error('No data received');
		return;
	}

	// Sanitize and validate the data
	$id = intval($_POST['id']);
	$published = boolval($_POST['published']);

	// Update the row in the database
	$result = $wpdb->update(
		$table_name,
		array('published' => !$published),
		array('id' => $id),
		array('%d'),
		array('%d')
	);

	if ($result === false) {
		wp_send_json_error('Failed to update row');
	} else {
		wp_send_json_success('Row updated successfully');
	}
}
add_action('wp_ajax_publish_unpublish', 'publish_unpublish');


// Function to retrieve LinkedIn posts
function get_linkedin_posts()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Set the synced and published values to true
	$synced = true;
	$published = true;

	// Fetch rows from the lps_synced_posts table
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE synced = %d AND published = %d ORDER BY post_order ASC",
			$synced,
			$published
		),
		ARRAY_A
	);

	// Process each row to format the images data correctly
	foreach ($rows as &$row) {
		if (!empty($row['images'])) {
			// Decode the JSON-encoded images array
			$decoded_images = json_decode($row['images'], true);

			// Check if the decoding was successful
			if ($decoded_images !== null) {
				$row['images'] = $decoded_images;
			} else {
				// In case of json_decode failure, assume it's serialized
				$row['images'] = maybe_unserialize($row['images']);
			}
		} else {
			// Assign an empty array if no images
			$row['images'] = [];
		}
	}

	// Send the data back to the frontend
	wp_send_json_success($rows);
}
add_action('wp_ajax_get_linkedin_posts', 'get_linkedin_posts');
add_action('wp_ajax_nopriv_get_linkedin_posts', 'get_linkedin_posts');


// Function to delete a post
function delete_post()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Check for nonce for security (optional enhancement)
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'delete_post_nonce')) {
		wp_send_json_error('Nonce verification failed');
		return;
	}

	// Check if necessary data is set in the AJAX request
	if (!isset($_POST['id'])) {
		wp_send_json_error('No data received');
		return;
	}

	// Sanitize and validate the data
	$id = intval($_POST['id']);

	// Delete the row from the database
	$result = $wpdb->delete(
		$table_name,
		array('id' => $id),
		array('%d')
	);

	if ($result === false) {
		wp_send_json_error('Failed to delete row');
	} else {
		wp_send_json_success('Row deleted successfully');
	}
}
add_action('wp_ajax_delete_post', 'delete_post');

function move_post()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Check for nonce for security
	//if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'move_post_nonce')) {
	//	wp_send_json_error('Nonce verification failed');
		return;
	//}

	// Check if necessary data is set in the AJAX request
	if (!isset($_POST['id']) || !isset($_POST['direction'])) {
		wp_send_json_error('No data received');
		return;
	}

	// Sanitize and validate the data
	$id = intval($_POST['id']);
	$direction = sanitize_text_field($_POST['direction']);

	// Fetch current post_order and IDs
	$rows = $wpdb->get_results("SELECT id, post_order FROM $table_name ORDER BY post_order ASC", ARRAY_A);
	$order_map = wp_list_pluck($rows, 'post_order', 'id');
	$current_order = $order_map[$id];

	// Determine new order
	$new_order = ($direction === 'up') ? $current_order - 1 : $current_order + 1;

	// Swap order values in the database
	$wpdb->query("START TRANSACTION");
	$wpdb->update($table_name, array('post_order' => $current_order), array('post_order' => $new_order));
	$wpdb->update($table_name, array('post_order' => $new_order), array('id' => $id));
	$wpdb->query("COMMIT");

	wp_send_json_success('Post order updated successfully');
}
add_action('wp_ajax_move_post', 'move_post');


// Function for the cron job to update LinkedIn posts
function linkedin_posts_slider_update_posts()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Make request to LinkedIn Scrapper Endpoint
	$response = linkedin_posts_slider_make_request();
	if (is_wp_error($response) || !isset($response['results'])) {
		error_log('LinkedIn Posts Slider - Error in request or no results.');
		return;
	}

	foreach ($response['results'] as $post_data) {
		// Validation steps (1, 2)
		if (empty($post_data['images']) || count($post_data['URN']) !== 1) {
			continue;
		}

		// Common sanitized fields
		$urn = sanitize_text_field($post_data['URN'][0]);
		$copy = sanitize_text_field($post_data['copy'][0] ?? '');

		// Check for existing post
		$existing_post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE urn = %s", $urn), ARRAY_A);

		if ($existing_post) {
			// Existing post scenario
			linkedin_posts_slider_update_existing_post($existing_post, $post_data);
		} else {
			// New post scenario
			linkedin_posts_slider_insert_new_post($post_data);
		}
	}

	// Update scrapper status and last update
	update_option('linkedin_scrapper_last_update', current_time('mysql'));
	update_option('linkedin_scrapper_status', 'OK');
}

// Function to update existing post
function linkedin_posts_slider_update_existing_post($existing_post, $post_data)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Prepare update data
	$update_data = [];
	$update_data['age'] = isset($post_data['age']) ? sanitize_text_field($post_data['age'][0]) : $existing_post['age'];
	$update_data['profilePicture'] = isset($post_data['profilePicture']) ? esc_url_raw($post_data['profilePicture'][0]) : $existing_post['profilePicture'];
	$update_data['username'] = isset($post_data['username']) ? sanitize_text_field($post_data['username'][0]) : $existing_post['username'];

	if (isset($post_data['reactions'])) {
		$update_data['reactions'] = intval($post_data['reactions'][0]);
	}

	if (isset($post_data['comments'])) {
		$update_data['comments'] = sanitize_text_field($post_data['comments'][0]);
	}

	if (!empty($post_data['images'])) {
		$images = array_filter($post_data['images'], function ($value) {
			return !empty($value);
		});
		if (count($images) >= count(maybe_unserialize($existing_post['images']))) {
			$update_data['images'] = maybe_serialize($images);
		}
	}

	// Update the post
	$wpdb->update($table_name, $update_data, array('id' => $existing_post['id']));
}

// Function to insert a new post
function linkedin_posts_slider_insert_new_post($post_data)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'lps_synced_posts';

	// Prepare insert data
	$insert_data = [
		'urn' => sanitize_text_field($post_data['URN'][0]),
		'author' => sanitize_text_field($post_data['author'][0] ?? 'Alpine Laser'),
		'username' => sanitize_text_field($post_data['username'][0] ?? 'alpine-laser'),
		'age' => sanitize_text_field($post_data['age'][0] ?? '1mo'),
		'profilePicture' => esc_url_raw($post_data['profilePicture'][0] ?? 'https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4'),
		'copy' => sanitize_text_field($post_data['copy'][0] ?? ''),
		'images' => maybe_serialize(array_filter($post_data['images'], function ($value) {
			return !empty($value);
		})),
		'reactions' => intval($post_data['reactions'][0] ?? 0),
		'comments' => sanitize_text_field($post_data['comments'][0] ?? '0 Comments'),
		'synced' => 1,
		'published' => 0,
		'post_order' => 0
	];

	// Insert the post
	$wpdb->insert($table_name, $insert_data);
}

// Function to make request to LinkedIn Scrapper Endpoint
function linkedin_posts_slider_make_request()
{
	global $scrapper_settings;

	$data = array(
		'secret_key' => 'test', // Use the appropriate secret key
		'url' => $scrapper_settings['linkedin_company_url'],
		'postSelector' => $scrapper_settings['linkedin_scrapper_full_post_selector'],
		'selectorsArray' => $scrapper_settings['linkedin_scrapper_full_selectors_array'],
		'attributesArray' => $scrapper_settings['linkedin_scrapper_full_attributes_array'],
		'namesArray' => $scrapper_settings['linkedin_scrapper_full_names_array']
	);

	$args = array(
		'body' => json_encode($data),
		'timeout' => 120, // 2 minutes
		'headers' => array('Content-Type' => 'application/json')
	);

	$response = wp_remote_post($scrapper_settings['linkedin_scrapper_endpoint'], $args);

	if (is_wp_error($response)) {
		error_log('LinkedIn Posts Slider - Error in scrapper request: ' . $response->get_error_message());
		return null;
	}

	return json_decode(wp_remote_retrieve_body($response), true);
}

 ```