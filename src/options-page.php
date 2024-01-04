<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

function enqueue_preview_styles()
{
	wp_enqueue_style('preview-styles', plugin_dir_url(dirname(__FILE__)) . 'preview.css');
	wp_enqueue_style('swiper-style');
}
add_action('admin_enqueue_scripts', 'enqueue_preview_styles');

function enqueue_preview_scripts()
{
	wp_enqueue_script('preview-scripts', plugin_dir_url(dirname(__FILE__)) . 'preview.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_preview_scripts');

function process_style_settings_form()
{
	if (isset($_POST['style_settings_form'])) {
		// Process form and update options
		// Company Name Section
		update_option('section-company-color', sanitize_text_field($_POST['section-company-color']));
		update_option('section-company-font-size', sanitize_text_field($_POST['section-company-font-size']));
		update_option('section-company-font-weight', sanitize_text_field($_POST['section-company-font-weight']));
		update_option('section-company-line-height', sanitize_text_field($_POST['section-company-line-height']));
		update_option('section-company-font-family', sanitize_text_field($_POST['section-company-font-family']));

		// Username and Post Date Section
		update_option('section-author-date-color', sanitize_text_field($_POST['section-author-date-color']));
		update_option('section-author-date-font-size', sanitize_text_field($_POST['section-author-date-font-size']));
		update_option('section-author-date-font-weight', sanitize_text_field($_POST['section-author-date-font-weight']));
		update_option('section-author-date-line-height', sanitize_text_field($_POST['section-author-date-line-height']));
		update_option('section-author-date-font-family', sanitize_text_field($_POST['section-author-date-font-family']));

		// Post Text Section
		update_option('section-body-color', sanitize_text_field($_POST['section-body-color']));
		update_option('section-body-font-size', sanitize_text_field($_POST['section-body-font-size']));
		update_option('section-body-webkit-line-clamp', sanitize_text_field($_POST['section-body-webkit-line-clamp']));
		update_option('section-body-font-family', sanitize_text_field($_POST['section-body-font-family']));

		// Post Interactions and Comments Section
		update_option('section-interactions-color', sanitize_text_field($_POST['section-interactions-color']));
		update_option('section-interactions-font-size', sanitize_text_field($_POST['section-interactions-font-size']));
		update_option('section-interactions-font-weight', sanitize_text_field($_POST['section-interactions-font-weight']));
		update_option('section-interactions-line-height', sanitize_text_field($_POST['section-interactions-line-height']));
		update_option('section-interactions-font-family', sanitize_text_field($_POST['section-interactions-font-family']));

		// Redirect back to settings page with a message
		$redirect_url = add_query_arg('settings-updated', 'true', $_SERVER['REQUEST_URI']);
		wp_safe_redirect($redirect_url);
		exit;
	}
}
add_action('admin_init', 'process_style_settings_form');

function linkedin_posts_slider_options_page()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

	// Check if the user has submitted the form
	if (isset($_GET['settings-updated'])) {
		add_settings_error('linkedin_posts_slider_messages', 'linkedin_posts_slider_message', __('Settings Saved', 'linkedin-posts-slider'), 'updated');
	}

	// Show error/update messages
	settings_errors('linkedin_posts_slider_messages');

?>

	<!-- Main container to hold the form and preview pane -->
	<div style="display: flex;">

		<!-- Left-half: Form -->
		<div style="flex: 1;">
			<h1>Style Live Editor</h1>

			<!-- TODO: Add form fields here -->
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="hidden" name="style_settings_form" value="1">

				<!-- Section: Company Name -->
				<div class="wp-ui-form-section">
					<h2 class="wp-ui-form-section-title">Company Name</h2>
					<div class="wp-ui-form-subsection">
						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-company-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-company-color" name="section-company-color" value="<?php echo esc_attr(get_option('section-company-color', '#454545')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-company-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-company-font-size" name="section-company-font-size" value="<?php echo esc_attr(get_option('section-company-font-size', '16')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Weight -->
						<div class="wp-ui-form-group">
							<label for="section-company-font-weight" class="wp-ui-form-label">Weight:</label>
							<input type="number" id="section-company-font-weight" name="section-company-font-weight" value="<?php echo esc_attr(get_option('section-company-font-weight', '400')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Line Height -->
						<div class="wp-ui-form-group">
							<label for="section-company-line-height" class="wp-ui-form-label">Line Height:</label>
							<input type="number" id="section-company-line-height" name="section-company-line-height" value="<?php echo esc_attr(get_option('section-company-line-height', '18')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-company-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-company-font-family" name="section-company-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-company-font-family', 'Titillium Web');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								foreach ($allowed_fonts as $font) {
									echo "<option value='{$font}'" . selected($current_font, $font, false) . ">{$font}</option>";
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<!-- END: Company Name -->
				<!-- Section: Username and Post Date -->
				<div class="wp-ui-form-section">
					<h2 class="wp-ui-form-section-title">Username and Post Date</h2>
					<div class="wp-ui-form-subsection">
						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-author-date-color" name="section-author-date-color" value="<?php echo esc_attr(get_option('section-author-date-color', '#454545')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-author-date-font-size" name="section-author-date-font-size" value="<?php echo esc_attr(get_option('section-author-date-font-size', '14')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Weight -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-font-weight" class="wp-ui-form-label">Weight:</label>
							<input type="number" id="section-author-date-font-weight" name="section-author-date-font-weight" value="<?php echo esc_attr(get_option('section-author-date-font-weight', '300')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Line Height -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-line-height" class="wp-ui-form-label">Line Height:</label>
							<input type="number" id="section-author-date-line-height" name="section-author-date-line-height" value="<?php echo esc_attr(get_option('section-author-date-line-height', '18')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-author-date-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-author-date-font-family" name="section-author-date-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-author-date-font-family', 'Titillium Web');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								foreach ($allowed_fonts as $font) {
									echo "<option value='{$font}'" . selected($current_font, $font, false) . ">{$font}</option>";
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<!-- END: Username and Post Date -->
				<!-- Section: Post Text -->
				<div class="wp-ui-form-section">
					<h2 class="wp-ui-form-section-title">Post Text</h2>
					<div class="wp-ui-form-subsection">
						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-body-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-body-color" name="section-body-color" value="<?php echo esc_attr(get_option('section-body-color', '#adb5bd')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-body-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-body-font-size" name="section-body-font-size" value="<?php echo esc_attr(get_option('section-body-font-size', '16')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Max Number of Lines -->
						<div class="wp-ui-form-group">
							<label for="section-body-webkit-line-clamp" class="wp-ui-form-label">Max Number of Lines:</label>
							<input type="number" id="section-body-webkit-line-clamp" name="section-body-webkit-line-clamp" value="<?php echo esc_attr(get_option('section-body-webkit-line-clamp', '5')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-body-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-body-font-family" name="section-body-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-body-font-family', 'Titillium Web');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								foreach ($allowed_fonts as $font) {
									echo "<option value='{$font}'" . selected($current_font, $font, false) . ">{$font}</option>";
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<!-- END: Post Text -->
				<!-- Section: Post Interactions and Comments -->
				<div class="wp-ui-form-section">
					<h2 class="wp-ui-form-section-title">Post Interactions and Comments</h2>
					<div class="wp-ui-form-subsection">
						<!-- Field: Color -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-color" class="wp-ui-form-label">Color:</label>
							<input type="color" id="section-interactions-color" name="section-interactions-color" value="<?php echo esc_attr(get_option('section-interactions-color', '#454545')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Size -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-font-size" class="wp-ui-form-label">Size:</label>
							<input type="number" id="section-interactions-font-size" name="section-interactions-font-size" value="<?php echo esc_attr(get_option('section-interactions-font-size', '14')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Weight -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-font-weight" class="wp-ui-form-label">Weight:</label>
							<input type="number" id="section-interactions-font-weight" name="section-interactions-font-weight" value="<?php echo esc_attr(get_option('section-interactions-font-weight', '300')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Line Height -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-line-height" class="wp-ui-form-label">Line Height:</label>
							<input type="number" id="section-interactions-line-height" name="section-interactions-line-height" value="<?php echo esc_attr(get_option('section-interactions-line-height', '18')); ?>" class="wp-ui-form-input">
						</div>

						<!-- Field: Font Family -->
						<div class="wp-ui-form-group">
							<label for="section-interactions-font-family" class="wp-ui-form-label">Font Family:</label>
							<select id="section-interactions-font-family" name="section-interactions-font-family" class="wp-ui-form-input">
								<?php
								$current_font = get_option('section-interactions-font-family', 'Titillium Web');
								$allowed_fonts = array(
									'Arial', 'Verdana', 'Times New Roman', 'Titillium Web',
									'Georgia', 'Palatino Linotype', 'Tahoma', 'Courier New',
									'Comic Sans MS', 'Trebuchet MS', 'Lucida Console', 'Impact'
								);
								foreach ($allowed_fonts as $font) {
									echo "<option value='{$font}'" . selected($current_font, $font, false) . ">{$font}</option>";
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<!-- END: Post Interactions and Comments -->

				<!-- Your submit button -->
				<button type="submit">Update Settings</button>
			</form>

		</div>

		<!-- Right-half: Preview Pane -->
		<div style="flex: 1; display: flex; justify-content: center; align-items: center;">

			<!-- TODO: Add the preview pane here -->
			<div id="post-preview" style="text-align: center;">
				<!-- Your preview post goes here. For example: -->
				<h2>Post Preview</h2>
				<div id="post-preview" class="post-preview-class">
					<div class="swiper-slide">
						<div class="li-icon-white">
							<svg style="
				width: 30px;
				height: 30px;
				overflow: visible;
				fill: rgb(255, 255, 255);
				" viewBox="0 0 448 512">
								<path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path>
							</svg>
						</div>
						<div class="img-container">
							<div class="li-single-img" style="
				background-image: url('https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4');
				"></div>
						</div>

						<div class="info-container">
							<div class="li-author-img" style="
				background-image: url('https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1705536000&v=beta&t=9PVwxirZIj7Pgh68ihS7bA_UscLia5XiJy9llH9Q_PA');
				"></div>
							<div class="section-company section-company">Alpine Laser</div>
							<div class="section-author-date">
								<span class="li-author-username">@alpine-laser . </span>
								<span class="li-post-age">3w ago</span>
							</div>
							<p class="section-body">
								Come see a live demo of femtosecond tube cutting today and tomorrow at MDM
								in booth 2803!
								Come see a live demo of femtosecond tube cutting today and tomorrow at MDM
								in booth 2803!
							</p>
							<div class="section-interactions">
								<span><svg style="
					width: 24px;
					height: 24px;
					overflow: visible;
					fill: rgb(0, 122, 255);
					" viewBox="0 0 24 24">
										<path fill="none" d="M0 0h24v24H0z"></path>
										<path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path>
										<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
										<path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path>
									</svg></span>
								<span class="li-post-reactions">74 . </span>
								<span class="li-post-comments">84 comments</span>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>

	</div>
<?php
}
