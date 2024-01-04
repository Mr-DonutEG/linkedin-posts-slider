<?php
// Assume $settings is an array containing all the setting values and $total_posts, $synced_posts are calculated elsewhere in the plugin.
// This file is 'form.php' which is included in the 'display_scrapper_options_form' function.

if (function_exists('settings_errors')) {
    settings_errors('linkedin_scrapper_settings');
}

$last_update = get_option('linkedin_scrapper_last_update', '');
$status = get_option('linkedin_scrapper_status', '');

// Assume these variables are calculated correctly from the 'linkedin_posts' table.
$total_posts = get_option('linkedin_total_posts_count', 0);
$synced_posts = get_option('linkedin_synced_posts_count', 0);

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
                    <input type="text" id="linkedin_company_url" name="linkedin_company_url" value="<?php echo esc_attr(get_option('linkedin_company_url')); ?>" class="regular-text">
                </td>
            </tr>

            <!-- Post Links Behavior -->
            <tr>
                <th scope="row"><?php _e('Post Links Behavior:', 'linkedin-posts-slider'); ?></th>
                <td>
                    <fieldset>
                        <label for="linkedin_slider_open_link">
                            <input type="checkbox" id="linkedin_slider_open_link" name="linkedin_slider_open_link" value="1" <?php checked(1, get_option('linkedin_slider_open_link'), true); ?>>
                            <?php _e('Open post links in a new tab', 'linkedin-posts-slider'); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <!-- Update Frequency -->
            <tr>
                <th scope="row"><label for="linkedin_update_frequency"><?php _e('Update Frequency:', 'linkedin-posts-slider'); ?></label></th>
                <td>
                    <input type="number" id="linkedin_update_frequency" name="linkedin_update_frequency" value="<?php echo esc_attr(get_option('linkedin_update_frequency')); ?>" class="small-text">
                    <span><?php _e('seconds', 'linkedin-posts-slider'); ?></span>
                    <p class="description"><?php _e('The frequency at which the scrapper updates the posts.', 'linkedin-posts-slider'); ?></p>
                </td>
            </tr>

            <!-- Scrapper Endpoint -->
            <tr>
                <th scope="row"><label for="linkedin_scrapper_endpoint"><?php _e('Scrapper Endpoint:', 'linkedin-posts-slider'); ?></label></th>
                <td>
                    <input type="text" id="linkedin_scrapper_endpoint" name="linkedin_scrapper_endpoint" value="<?php echo esc_attr(get_option('linkedin_scrapper_endpoint')); ?>" class="regular-text">
                    <p class="description"><?php _e('The endpoint from which the scrapper fetches LinkedIn posts.', 'linkedin-posts-slider'); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'linkedin-posts-slider'); ?>">
        </p>
    </form>
</div>