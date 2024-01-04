<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}



function publish_unpublish()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

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
function get_linkedin_posts()
{
	global $wpdb;

	// Set the synced and published values to true
	$synced = true;
	$published = true;

	// Fetch rows from the linkedin_posts table
	$table_name = $wpdb->prefix . 'linkedin_posts';
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE synced = %d AND published = %d ORDER BY post_order ASC",
			$synced,
			$published
		),
		ARRAY_A
	);

	// Decode the JSON-encoded images array
	// Decode the JSON-encoded (or serialized) images array
	foreach ($rows as &$row) {
		if (!empty($row['images'])) {
			$decoded_images = maybe_unserialize($row['images']);
			$row['images'] = $decoded_images;
		} else {
			$row['images'] = []; // Assign an empty array if no images
		}
	}


	// Send the data back to the frontend
	wp_send_json_success($rows);
}


add_action('wp_ajax_get_linkedin_posts', 'get_linkedin_posts');
add_action('wp_ajax_nopriv_get_linkedin_posts', 'get_linkedin_posts');



function delete_post()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

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


// Include WordPress functions
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Schedule a cron job
if (!wp_next_scheduled('linkedin_posts_slider_cron_job')) {
	wp_schedule_event(time(), 'custom', 'linkedin_posts_slider_cron_job');
}

// Add a custom interval based on the 'linkedin_update_frequency' setting
add_filter('cron_schedules', 'linkedin_posts_slider_add_cron_interval');
function linkedin_posts_slider_add_cron_interval($schedules)
{
	$interval = get_option('linkedin_update_frequency', 3600); // Default to 1 hour if not set
	$schedules['custom'] = array(
		'interval' => $interval,
		'display' => __('Custom Interval', 'linkedin-posts-slider'),
	);
	return $schedules;
}

// Cron job action
add_action('linkedin_posts_slider_cron_job', 'linkedin_posts_slider_update_posts');
function linkedin_posts_slider_update_posts()
{
	// Make request to LinkedIn Scrapper Endpoint
	$response = linkedin_posts_slider_make_request();
	if ($response && isset($response['results'])) {
		linkedin_posts_slider_process_posts($response['results']);
	}

	// Update the status and last update time
	linkedin_posts_slider_update_status($response !== null);
}

// Function to make the request to the LinkedIn Scrapper Endpoint
function linkedin_posts_slider_make_request()
{
	$endpoint = get_option('linkedin_scrapper_endpoint', '');
	$data = array(
		"secret_key" => "test",
		"url" => get_option('linkedin_company_url', 'https://www.linkedin.com/company/alpine-laser/'),
		"postSelector" => get_option('linkedin_scrapper_full_post_selector', ''),
		"selectorsArray" => get_option('linkedin_scrapper_full_selectors_array', ''),
		"attributesArray" => get_option('linkedin_scrapper_full_attributes_array', ''),
		"namesArray" => get_option('linkedin_scrapper_full_names_array', '')
	);

	$args = array(
		'body' => json_encode($data),
		'timeout' => '180', // 3 minutes
		'headers' => array('Content-Type' => 'application/json')
	);

	$response = wp_remote_post($endpoint, $args);

	if (is_wp_error($response)) {
		error_log('LinkedIn Posts Slider - Error in request: ' . $response->get_error_message());
		return null;
	}

	return json_decode(wp_remote_retrieve_body($response), true);
}

// Function to process the posts
function linkedin_posts_slider_process_posts($posts)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	foreach ($posts as $post) {
		// Check if post has required data
		if (!isset($post['URN'], $post['company_name'], $post['age'], $post['reactions'])) {
			continue;
		}

		// Check company name
		if ($post['company_name'][0] !== 'Alpine Laser') {
			continue;
		}

		$urn = sanitize_text_field($post['URN'][0]);
		$age = sanitize_text_field($post['age'][0]);
		$reactions = intval($post['reactions'][0]);
		$comments = isset($post['comments']) ? sanitize_text_field($post['comments'][0]) : '';

		// Check for existing post
		$existing_post = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE urn = %s", $urn));

		if ($existing_post) {
			// Update existing post
			$wpdb->update(
				$table_name,
				array(
					'age' => $age,
					'reactions' => $reactions,
					'comments' => $comments,
					'synced' => 0 // Mark as unsynced for further update
				),
				array('urn' => $urn)
			);
		} else {
			// Insert new post
			$wpdb->insert(
				$table_name,
				array(
					'urn' => $urn,
					'author' => 'Placeholder', // Placeholder values
					'username' => 'Placeholder',
					'age' => $age,
					'profilePicture' => 'Placeholder',
					'copy' => 'Placeholder',
					'images' => 'Placeholder',
					'reactions' => $reactions,
					'comments' => $comments,
					'synced' => 0, // Not synced yet
					'published' => 0,
					'post_order' => 0
				)
			);
		}
	}

	// Process each unsynced post
	linkedin_posts_slider_process_unsynced_posts();
}

// Function to process unsynced posts
function linkedin_posts_slider_process_unsynced_posts()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Get unsynced posts
	$unsynced_posts = $wpdb->get_results("SELECT * FROM $table_name WHERE synced = 0");

	foreach ($unsynced_posts as $post) {
		// Make request for each unsynced post
		$response = linkedin_posts_slider_fetch_single_post($post->urn);
		if ($response && isset($response['results'][0])) {
			// Update post with new data
			linkedin_posts_slider_update_single_post($post->id, $response['results'][0]);
		}
	}
}

// Function to fetch single post data
function linkedin_posts_slider_fetch_single_post($urn)
{
	$endpoint = get_option('linkedin_scrapper_endpoint', '');
	$post_url = 'https://www.linkedin.com/feed/update/' . $urn;

	$data = array(
		"secret_key" => "test",
		"url" => $post_url,
		"postSelector" => get_option('linkedin_scrapper_single_post_selector', ''),
		"selectorsArray" => get_option('linkedin_scrapper_single_selectors_array', ''),
		"attributesArray" => get_option('linkedin_scrapper_single_attributes_array', ''),
		"namesArray" => get_option('linkedin_scrapper_single_names_array', '')
	);

	$args = array(
		'body' => json_encode($data),
		'timeout' => '120', // 2 minutes
		'headers' => array('Content-Type' => 'application/json')
	);

	$response = wp_remote_post($endpoint, $args);

	if (is_wp_error($response)) {
		error_log('LinkedIn Posts Slider - Error in single post request: ' . $response->get_error_message());
		return null;
	}

	return json_decode(wp_remote_retrieve_body($response), true);
}

// Function to update single post data
function linkedin_posts_slider_update_single_post($post_id, $post_data)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'linkedin_posts';

	// Prepare data for update
	$update_data = array(
		'author' => sanitize_text_field($post_data['author'][0] ?? 'Unknown'),
		'username' => sanitize_text_field($post_data['username'][0] ?? 'Unknown'),
		'profilePicture' => esc_url_raw($post_data['profilePicture'][0] ?? ''),
		'copy' => sanitize_text_field($post_data['copy'][0] ?? ''),
		'images' => maybe_serialize($post_data['images'] ?? array()),
		'synced' => 1
	);

	// Update the post
	$wpdb->update($table_name, $update_data, array('id' => $post_id));
}

// Function to update status and last update time
function linkedin_posts_slider_update_status($success)
{
	if ($success) {
		update_option('linkedin_scrapper_last_update', current_time('mysql'));
		update_option('linkedin_scrapper_status', 'OK');
	} else {
		update_option('linkedin_scrapper_status', 'ERROR');
	}
}

// Activate and deactivate hooks for cron job
register_activation_hook(__FILE__, 'linkedin_posts_slider_activation');
function linkedin_posts_slider_activation()
{
	if (!wp_next_scheduled('linkedin_posts_slider_cron_job')) {
		wp_schedule_event(time(), 'custom', 'linkedin_posts_slider_cron_job');
	}
}

register_deactivation_hook(__FILE__, 'linkedin_posts_slider_deactivation');
function linkedin_posts_slider_deactivation()
{
	wp_clear_scheduled_hook('linkedin_posts_slider_cron_job');
}
