<?php

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

	// Retrieve serialized settings
	$serialized_settings = get_option('lps_scrapper_settings', '');
	$scrapper_settings = maybe_unserialize($serialized_settings);

	// Fetch total number of posts and synced posts from the database
	$posts_table = $wpdb->prefix . 'lps_synced_posts';
	$total_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$posts_table}");
	$synced_posts = $wpdb->get_var("SELECT COUNT(*) FROM {$posts_table} WHERE synced = 1");

	// Retrieve settings values for form population
	$last_update = $scrapper_settings[ 'linkedin_scrapper_last_update'] ?? 'Never';
	$status = $scrapper_settings[ 'linkedin_scrapper_status'] ?? 'OK';
	// New settings retrieval
	$full_post_selector = $scrapper_settings['linkedin_scrapper_full_post_selector'] ?? '';
	$full_selectors_array = $scrapper_settings['linkedin_scrapper_full_selectors_array'] ?? '';
	$full_attributes_array = $scrapper_settings['linkedin_scrapper_full_attributes_array'] ?? '';
	$full_names_array = $scrapper_settings['linkedin_scrapper_full_names_array'] ?? '';
	$linkedin_company_url = $scrapper_settings['linkedin_company_url'] ?? '';
	$linkedin_scrapper_endpoint = $scrapper_settings['linkedin_scrapper_endpoint'] ?? '';


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