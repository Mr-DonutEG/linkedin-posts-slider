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
