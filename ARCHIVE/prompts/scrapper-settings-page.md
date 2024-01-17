## Context: 
after we made changes to the plugin code the final step is to apply the remaining changes to the scrapper settings page which is a wordpress admin options page haning a status bar showing the scrapper status and no. of posts etc and a form for adjusting the scrapper settings used in the requests to sync posts
### the changes to be made :
1. remove the fields that are no longer used from the form 
2. edit the code to use the newly created serialized settings option we created containing all the options instead of creating individual option for each setting 
3. make the ui more beautiful and elegant and professional 

### instructions:
provide the full list of modifications to be made in the provided code to make one change at a time 
1. provide full code snippets for each change 
2. provide the exact parts to remove and exact snippets to add for each changed file in an easy structured way 

## editable files :
### src\scrapper-options-page.php:
```
<?php
/*
Plugin Name: LinkedIn Posts Slider
Description: A WordPress plugin to display LinkedIn posts in a slider with admin options.
*/

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
	exit;
}

// Enqueue styles for the admin page
function linkedin_posts_slider_enqueue_styles()
{
	wp_enqueue_style('linkedin-posts-slider-admin', plugins_url('linkedin-posts-slider-admin.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'linkedin_posts_slider_enqueue_styles');

// Handle form submission
function handle_scrapper_settings_form_submission()
{
	if (isset($_POST['action']) && $_POST['action'] === 'update_scrapper_settings') {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		check_admin_referer('update_scrapper_settings');

		$serialized_settings = array(
			'linkedin_company_url' => sanitize_text_field($_POST['linkedin_company_url']),
			'linkedin_scrapper_endpoint' => sanitize_text_field($_POST['linkedin_scrapper_endpoint']),

			// New settings
			'linkedin_scrapper_full_post_selector' => sanitize_text_field($_POST['linkedin_scrapper_full_post_selector']),
			'linkedin_scrapper_full_selectors_array' => sanitize_text_field($_POST['linkedin_scrapper_full_selectors_array']),
			'linkedin_scrapper_full_attributes_array' => sanitize_text_field($_POST['linkedin_scrapper_full_attributes_array']),
			'linkedin_scrapper_full_names_array' => sanitize_text_field($_POST['linkedin_scrapper_full_names_array']),
		);

		update_option('lps_scrapper_settings', maybe_serialize($serialized_settings));


		add_settings_error('lps_scrapper_settings', 'settings_updated', __('Settings updated successfully'), 'updated');
		set_transient('settings_errors', get_settings_errors(), 30);

		// Redirect back to settings page with a message
		$redirect_url = add_query_arg('settings-updated', 'true', wp_get_referer());
		wp_safe_redirect($redirect_url);
		exit;
	}
}
add_action('admin_post_update_scrapper_settings', 'handle_scrapper_settings_form_submission');

// Function to display the scrapper options page
function linkedin_posts_scrapper_options_page()
{
	global $wpdb;
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

	// Show any stored admin notices.
	settings_errors('lps_scrapper_settings');

	// Fetch total number of posts and synced posts from the database
	$posts_table = $wpdb->prefix . 'lps_synced_posts';
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$posts_table}");
	$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$posts_table} WHERE synced = 1");

	// Retrieve settings values for form population
	$last_update = get_option('linkedin_scrapper_last_update', 'Never');
	$status = get_option('linkedin_scrapper_status', 'Unknown');
	// New settings retrieval
	$full_post_selector = get_option('linkedin_scrapper_full_post_selector', '');
	$full_selectors_array = get_option('linkedin_scrapper_full_selectors_array', '');
	$full_attributes_array = get_option('linkedin_scrapper_full_attributes_array', '');
	$full_names_array = get_option('linkedin_scrapper_full_names_array', '');
	$linkedin_company_url = get_option('linkedin_company_url', '');
	$linkedin_scrapper_endpoint = get_option('linkedin_scrapper_endpoint', '');


	// Retrieve settings values for form population
	$settings = array();

	// Start the settings form
?>
	<div class="wrap">
		<h1><?php echo esc_html('LinkedIn Scrapper Options'); ?></h1>

		<!-- Stats -->
		<div class="stats-wrapper">
			<div class="stat-card">
				<span class="stat-title"><?php _e('Last Update:', 'linkedin-posts-slider'); ?></span>
				<span class="stat-value"><?php echo esc_html($last_update); ?></span>
			</div>

			<div class="stat-card">
				<span class="stat-title"><?php _e('Total Posts:', 'linkedin-posts-slider'); ?></span>
				<span class="stat-value"><?php echo intval($total_posts); ?></span>
			</div>

			<div class="stat-card">
				<span class="stat-title"><?php _e('Synced:', 'linkedin-posts-slider'); ?></span>
				<span class="stat-value"><?php echo intval($synced_posts); ?></span>
			</div>

			<div class="stat-card">
				<span class="stat-title"><?php _e('Status:', 'linkedin-posts-slider'); ?></span>
				<span class="stat-value status-<?php echo esc_attr($status); ?>"><?php echo esc_html($status); ?></span>
			</div>
		</div>

		<!-- Settings Form -->
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="update_scrapper_settings">
			<?php wp_nonce_field('update_scrapper_settings'); ?>

			<table class="form-table">
				<!-- Company Profile URL -->
				<tr>
					<th scope="row"><label for="linkedin_company_url"><?php _e('Company Profile URL:', 'linkedin-posts-slider'); ?></label></th>
					<td>
						<input type="text" id="linkedin_company_url" name="linkedin_company_url" value="<?php echo esc_attr($linkedin_company_url); ?>" class="regular-text">
					</td>
				</tr>

			
				<!-- Scrapper Endpoint -->
				<tr>
					<th scope="row"><label for="linkedin_scrapper_endpoint"><?php _e('Scrapper Endpoint:', 'linkedin-posts-slider'); ?></label></th>
					<td>
						<input type="text" id="linkedin_scrapper_endpoint" name="linkedin_scrapper_endpoint" value="<?php echo esc_attr($linkedin_scrapper_endpoint); ?>" class="regular-text">
						<p class="description"><?php _e('The endpoint from which the scrapper fetches LinkedIn posts.', 'linkedin-posts-slider'); ?></p>
					</td>
				</tr>
				<!-- New Section: Full Profile Update Settings -->
				<tr>
					<th colspan="2">
						<h2><?php _e('Full Profile Update Settings', 'linkedin-posts-slider'); ?></h2>
					</th>
				</tr>
				<tr>
					<th scope="row"><label for="linkedin_scrapper_full_post_selector"><?php _e('Full Post Selector:', 'linkedin-posts-slider'); ?></label></th>
					<td>
						<input type="text" id="linkedin_scrapper_full_post_selector" name="linkedin_scrapper_full_post_selector" value="<?php echo esc_attr($full_post_selector); ?>" class="regular-text">
					</td>
				</tr>
				<!-- Full Selectors Array -->
				<tr>
					<th scope="row"><label for="linkedin_scrapper_full_selectors_array"><?php _e('Full Selectors Array:', 'linkedin-posts-slider'); ?></label></th>
					<td>
						<input type="text" id="linkedin_scrapper_full_selectors_array" name="linkedin_scrapper_full_selectors_array" value="<?php echo esc_attr($full_selectors_array); ?>" class="regular-text">
					</td>
				</tr>

				<!-- Full Attributes Array -->
				<tr>
					<th scope="row"><label for="linkedin_scrapper_full_attributes_array"><?php _e('Full Attributes Array:', 'linkedin-posts-slider'); ?></label></th>
					<td>
						<input type="text" id="linkedin_scrapper_full_attributes_array" name="linkedin_scrapper_full_attributes_array" value="<?php echo esc_attr($full_attributes_array); ?>" class="regular-text">
					</td>
				</tr>

				<!-- Full Names Array -->
				<tr>
					<th scope="row"><label for="linkedin_scrapper_full_names_array"><?php _e('Full Names Array:', 'linkedin-posts-slider'); ?></label></th>
					<td>
						<input type="text" id="linkedin_scrapper_full_names_array" name="linkedin_scrapper_full_names_array" value="<?php echo esc_attr($full_names_array); ?>" class="regular-text">
					</td>
				</tr>


				
			</table>

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'linkedin-posts-slider'); ?>">
			</p>
		</form>
	</div>
<?php
}


?>
```
### src\linkedin-posts-slider-admin.css:
```
/* Place this in linkedin-posts-slider-admin.css */

.stats-wrapper {
    display: flex;
    justify-content: space-around;
    margin-bottom: 20px;
    padding: 10px;
    background: #f1f1f1;
    border: 1px solid #ccc;
    border-radius: 10px;
}

.stat-card {
    text-align: center;
}

.stat-title {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.stat-value.status-ok {
    color: green;
    font-family: monospace;
    font-weight: bold;
}

.stat-value.status-error {
    color: red;
    font-family: monospace;
    font-weight: bold;
}
```
## related code files:

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

### src\activation-deactivation.php:
```
<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Function to create and prepopulate the custom table
function linkedin_posts_slider_activate()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'lps_synced_posts';
    $charset_collate = $wpdb->get_charset_collate();

    // Step 1: Create custom WordPress table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    urn text NOT NULL,
    author text NOT NULL,
    username text NOT NULL,
    age text NOT NULL,
    profilePicture text NOT NULL,
    copy text NOT NULL,
    images text NOT NULL,
    reactions int NOT NULL,
    comments text NOT NULL,
    synced boolean NOT NULL,
    published boolean NOT NULL,
    post_order int NOT NULL,
    PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Check if the table was just created and prepopulate it
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        // Your JSON data for prepopulating the table
        $jsonData = '{
            "results": [
    {
      "URN": [
        "urn:li:activity:7117516266000498688"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "3mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "Come see a live demo of femtosecond tube cutting today and tomorrow at MDM in booth 2803!"
      ],
      "reactions": [
        "25"
      ],
      "comments": [
        "2 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_2048_1536/0/1696948113674?e=2147483647&v=beta&t=pUJztCmDCuirwcqkXm-eocdA4vDRh3ui20rHkAb44JQ"
      ]
    },
    {
      "URN": [
        "urn:li:activity:7110664133217288192"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "3mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "Announcing the MediSCAN Pro - Alpine Laser\'s latest high performance laser processing workstation optimized for medical device manufacturing! \n\nThe configuration shown here features programmable XYZ motion coupled with a Scanlab precSYS 5-axis #micromachining galvo and a TRUMPF 2230 ultra short pulsed 515nm green #laser source and coaxial vision.  \n\nThis machine was designed to process very fine features and complex holes in hard to machine polymer materials. (Shown are 0.25mm holes in a 1mm Pellethane tube)\n\nOther configurations of this workstation can be optimized for flat sheet cutting, traditional 2D galvo applications, marking, complex ablation, to name a few.\n\nContact sales@alpinelaser.com for more information.\n\nSCANLAB GmbH\nTRUMPF North America\n#medicaldevicemanufacturing"
      ],
      "reactions": [
        "119"
      ],
      "comments": [
        "8 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_2048_1536/0/1695314437358?e=2147483647&v=beta&t=g53yY2_Unlp0wnDGnS_TTEDTrW0NGHcRX0S1CIklHFo",
        "https://media.licdn.com/dms/image/D5622AQGu92JK888ZUw/feedshare-shrink_2048_1536/0/1695314437404?e=2147483647&v=beta&t=-b88XXslHvHLphceYz5JlbzeG7YYVmINZXIqThqWBWc",
        "https://media.licdn.com/dms/image/D5622AQFevdEZ-d2RfQ/feedshare-shrink_2048_1536/0/1695314436890?e=2147483647&v=beta&t=ih0r1LCV_ZB-sXDRP1AOVc5loPEWN2jJEKXZbFeWEMw",
        "https://media.licdn.com/dms/image/D5622AQGfdzbosfaiPw/feedshare-shrink_2048_1536/0/1695314437501?e=2147483647&v=beta&t=6IKGVD8z2EC-m8uVPJOZHu2x54fTs47_EL2QBoIIAPU",
        "https://media.licdn.com/dms/image/D5622AQE9oTsaKKVG9A/feedshare-shrink_2048_1536/0/1695314437783?e=2147483647&v=beta&t=yIfktpvMC9WXX7mGKnJYtxwu5ayWztFyqWDEjIBNFRE"
      ]
    },
    {
      "URN": [
        "urn:li:activity:7092583182209875968"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "5mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "Laser cutting catheter shafts allows for continuously variable bending stiffness, torsion, and compression to open up a new realm of possibilities for medical device design engineers. \n\nShown below is a 0.027\" OD 0.0025\" wall (0.686mm OD .064mm wall) microcatheter shaft cut on the Medicut Pro Fiber Laser.\n\nContact us at sales@alpinelaser.com for more info.\n\nThank you TRUMPF for the photo."
      ],
      "reactions": [
        "30"
      ],
      "comments": [
        "0 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/D5622AQElkuOrteJbWg/feedshare-shrink_2048_1536/0/1691003603743?e=2147483647&v=beta&t=uzUu8eXOZj-hV1obM_xqJ_1EpIzgsr8ndXA-jajzBKw"
      ]
    },
    {
      "URN": [
        "urn:li:activity:7090069626461532160"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "5mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "Laser cutting highly reflective material & precious metal alloys can prove challenging for traditional laser workstations. \n\nWe had a recent application inquiry to cut copper and ran the parts on our standard Medicut Pro system equipped with a Fiber Laser. The same success has been demonstrated with precious metal alloys such as Pt, PtIr, PtW, Au, Ag, etc.\n\nPart Description: Copper Tube 2mm OD with a 0.3mm wall\n\nContact us at sales@alpinelaser.com for more info."
      ],
      "reactions": [
        "85"
      ],
      "comments": [
        "4 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/D4E22AQH9L9hhXmwLhg/feedshare-shrink_2048_1536/0/1690404325095?e=2147483647&v=beta&t=1leGT7yzfLGC_qx0XcdwO4Uvg_DfL_Dl6lazWFU97lA"
      ]
    },
    {
      "URN": [
        "urn:li:activity:7085263372841041920"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "6mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "Need cuts with no heat affected zone and very clean edges? Take a look at these sample parts cut with the Alpine Laser Medicut Pro workstation utilizing a top of the line ultra short pulse femtosecond laser from TRUMPF. "
      ],
      "reactions": [
        "119"
      ],
      "comments": [
        "6 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/D4D22AQGqLOmYU5zQJQ/feedshare-shrink_800/0/1689258424335?e=2147483647&v=beta&t=-6rp9Xf0Dcc19OP2pS9WSY1ZPvpR2byC4o3LTNJOsB0",
        "https://media.licdn.com/dms/image/D4D22AQFjeXMtn0ZgcQ/feedshare-shrink_800/0/1689258424269?e=2147483647&v=beta&t=ulWCIS13Zekx0uRavaizBfl0bhN3KbcH-_E0hUx9Abo",
        "https://media.licdn.com/dms/image/D4D22AQECZgYGzGDO6g/feedshare-shrink_800/0/1689258424307?e=2147483647&v=beta&t=nHve-1OHtoYZYZERG1sUN98Lo8W9xILLjzSsY30ZhOM",
        "https://media.licdn.com/dms/image/D4D22AQEMYp-_RwB6hA/feedshare-shrink_800/0/1689258424267?e=2147483647&v=beta&t=TY1moYnXxNF9VEEiZ3qGfZ2pEyoa9eQJAT4xgOyAU4w"
      ]
    },
    {
      "URN": [
        "urn:li:activity:7084633761740423169"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "6mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "Just completed the installation of two femtosecond laser tube cutting workstations paired with bar feeders and custom Alpine automated part extractors enabling this customer to run catheter shaft production lights out.\n\nContact the team at Alpine Laser today to see how we can help you transform your laser cutting operation. \n\nsales@alpinelaser.com"
      ],
      "reactions": [
        "108"
      ],
      "comments": [
        "5 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/D5622AQE0uiOv1X59Og/feedshare-shrink_800/0/1689108312570?e=2147483647&v=beta&t=MhKywaOzFT-SfrK2DcERSUPNjXYukm46axKvQu96SBw",
        "https://media.licdn.com/dms/image/D5622AQEDvNoAXKgCkA/feedshare-shrink_800/0/1689108308231?e=2147483647&v=beta&t=Dl1rGOTpPop83hp06wo5UIduYNMOoYFT_lZsBAl8iVs",
        "https://media.licdn.com/dms/image/D5622AQGuLM3G0lYTmQ/feedshare-shrink_800/0/1689108310054?e=2147483647&v=beta&t=OrMIghhybZy-O4eSc7ij9zpqeROskH_ny_Af4mBl_Lg",
        "https://media.licdn.com/dms/image/D5622AQEs3FWPkEZ4fg/feedshare-shrink_800/0/1689108313262?e=2147483647&v=beta&t=gz73kyrnFVcsz7-JgvO0Q8gE0F1OfTlMVJw6lncpG0I",
        "https://media.licdn.com/dms/image/D5622AQGwIi2isOxGuQ/feedshare-shrink_800/0/1689108311592?e=2147483647&v=beta&t=aepOpoDEmiQz4zPfmX46gcfPtZwnKjvK8ramekwurxE"
      ]
    },
    {
      "URN": [
        "urn:li:activity:7023741456741777408"
      ],
      "profilePicture": [
        "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"
      ],
      "age": [
        "11mo"
      ],
      "author": [
        "Alpine Laser"
      ],
      "username": [
        "alpine-laser"
      ],
      "copy": [
        "* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management \n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com "
      ],
      "reactions": [
        "28"
      ],
      "comments": [
        "0 Comments"
      ],
      "images": [
        "https://media.licdn.com/dms/image/C5622AQG3G4m1HdBRTQ/feedshare-shrink_800/0/1674590456558?e=2147483647&v=beta&t=jpm_qxl2hxtHLU2nIaHONFKdHT7oF_2CrLpR38FeQtk",
        "https://media.licdn.com/dms/image/C5622AQHltS4_M21yfQ/feedshare-shrink_800/0/1674590456620?e=2147483647&v=beta&t=E83UT1nFkyqx001U2UcoWxivMH8zkX4snlVv-uEjr-U",
        "https://media.licdn.com/dms/image/C5622AQGKlqfHEc9TVA/feedshare-shrink_800/0/1674590456761?e=2147483647&v=beta&t=CHr8prYsGvekN8iqV51sasSkCDILAjg-pSssm3THvoY",
        "https://media.licdn.com/dms/image/C5622AQEw2Fhe4KSHUA/feedshare-shrink_800/0/1674590456630?e=2147483647&v=beta&t=o6OCUI3Nk29QRqA3Qvm53A8oQmxzTqRGEW2zz_nzpVo"
      ]
    }
  ]
        }';

        // Decode the JSON data
        $decodedData = json_decode($jsonData, true);

        // Loop through each item in the JSON data
        foreach ($decodedData['results'] as $item) {
            // Prepare data for insertion
            $insertData = array(
                'urn' => $item['URN'][0],
                'author' => $item['author'][0],
                'username' => $item['username'][0],
                'age' => $item['age'][0],
                'profilePicture' => $item['profilePicture'][0],
                'copy' => $item['copy'][0],
                'images' => maybe_serialize($item['images']),
                'reactions' => intval($item['reactions'][0]),
                'comments' => $item['comments'][0],
                'synced' => 1, // Assuming the posts are initially marked as synced
                'published' => 1, // Assuming the posts are initially published
                'post_order' => 0 // Default post order
            );

            // Insert data into the database
            $wpdb->insert($table_name, $insertData);
        }
    }

    // Step 2: Create and set default scrapper settings
    $default_settings =
        array(
            'linkedin_company_url' => 'https://www.linkedin.com/company/alpine-laser/',
            'linkedin_scrapper_status' => 'OK',
            'linkedin_scrapper_last_update' => 'Not available',
            'linkedin_scrapper_endpoint' => 'https://scrape-js.onrender.com/scrape',
            'linkedin_scrapper_full_post_selector' => 'li.mb-1',
            'linkedin_scrapper_full_selectors_array' => [
                'article[data-activity-urn]',
                'article[data-activity-urn]',
                'a[data-tracking-control-name="organization_guest_main-feed-card_feed-actor-image"] img',
                'time',
                'a[data-tracking-control-name="organization_guest_main-feed-card_feed-actor-name"]',
                'p[data-test-id="main-feed-activity-card__commentary"]',
                'a[data-tracking-control-name="organization_guest_main-feed-card_social-actions-reactions"]',
                'a[data-tracking-control-name="organization_guest_main-feed-card_social-actions-comments"]',
                'ul[data-test-id="feed-images-content"] img'
            ],
            'linkedin_scrapper_full_attributes_array' => '["data-activity-urn", "src", "innerText", "innerText", "innerText", "innerText", "innerText", "src"]',
            'linkedin_scrapper_full_names_array' => '["URN", "profilePicture", "age", "author", "copy", "reactions", "comments", "images"]'
            // Add other settings as needed
        );
    foreach ($default_settings as $setting_name => $default_value) {
        if (false === get_option($setting_name)) {
            add_option($setting_name, $default_value);
        }
    }
    if (!wp_next_scheduled('linkedin_posts_slider_cron_job')) {
        wp_schedule_event(time(), 'daily', 'linkedin_posts_slider_cron_job');
    }
}

// Function to handle deactivation
function linkedin_posts_slider_deactivate()
{
    // Your deactivation code here (if needed)
    // For example: wp_clear_scheduled_hook('your_scheduled_event');
    // Clear the scheduled hook upon deactivation
    wp_clear_scheduled_hook('linkedin_posts_slider_cron_job');
}

// Function to set the activation hook
function linkedin_posts_slider_set_activation_hook($main_file_path)
{
    register_activation_hook($main_file_path, 'linkedin_posts_slider_activate');
}

// Function to set the deactivation hook
function linkedin_posts_slider_set_deactivation_hook($main_file_path)
{
    register_deactivation_hook($main_file_path, 'linkedin_posts_slider_deactivate');
}

```