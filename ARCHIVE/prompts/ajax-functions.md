## current version of ajax-actions.php:
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

function linkedin_posts_slider_get_post($post_id)
{
global $wpdb;
global $scrapper_settings;

// Ensure scrapper settings and post ID are available
if (empty($scrapper_settings) || empty($post_id)) {
error_log('LinkedIn Posts Slider - Missing scrapper settings or post ID.');
return;
}

// Fetch post data
$post = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}lps_synced_posts WHERE id = %d", $post_id), ARRAY_A);
if (!$post) {
error_log('LinkedIn Posts Slider - Post not found.');
return;
}

// Prepare the data for the scrapper endpoint
$data = array(
"secret_key" => "test", // Assuming a secret key is needed
"url" => 'https://www.linkedin.com/feed/update/' . $post['urn'],
"postSelector" => $scrapper_settings['linkedin_scrapper_single_post_selector'],
"selectorsArray" => $scrapper_settings['linkedin_scrapper_single_selectors_array'],
"attributesArray" => $scrapper_settings['linkedin_scrapper_single_attributes_array'],
"namesArray" => $scrapper_settings['linkedin_scrapper_single_names_array']
);

$args = array(
'body' => json_encode($data),
'timeout' => 120, // 2 minutes
'headers' => array('Content-Type' => 'application/json')
);

// Send request to LinkedIn Scrapper Endpoint
$response = wp_remote_post($scrapper_settings['linkedin_scrapper_endpoint'], $args);

if (is_wp_error($response)) {
error_log('LinkedIn Posts Slider - Error in scrapper request: ' . $response->get_error_message());
return;
}

$post_data = json_decode(wp_remote_retrieve_body($response), true);

// Validate the response
if (isset($post_data['results'][0])) {
$data = $post_data['results'][0];

// Update the post details
$update_data = array(
'author' => sanitize_text_field($data['author'][0] ?? 'Unknown'),
'username' => sanitize_text_field($data['username'][0] ?? 'Unknown'),
'age' => sanitize_text_field($data['age'][0] ?? ''),
'profilePicture' => esc_url_raw($data['profilePicture'][0] ?? ''),
'copy' => sanitize_text_field($data['copy'][0] ?? ''),
'images' => maybe_serialize(array_filter($data['images'] ?? [])), // Filter out empty strings
'reactions' => intval(preg_replace('/\D/', '', $data['reactions'][0] ?? '0')), // Extract numbers
'comments' => intval(preg_replace('/\D/', '', $data['comments'][0] ?? '0')), // Extract numbers
'synced' => 1
);

// Perform the update
$wpdb->update("{$wpdb->prefix}lps_synced_posts", $update_data, array('id' => $post_id));
} else {
error_log('LinkedIn Posts Slider - Invalid response from scrapper.');
}
}


function linkedin_posts_slider_process_unsynced_posts()
{
global $wpdb;

// Fetch all posts from the lps_synced_posts table
$table_name = $wpdb->prefix . 'lps_synced_posts';
$posts = $wpdb->get_results("SELECT id FROM {$table_name}", ARRAY_A);

if ($posts) {
foreach ($posts as $post) {
// Call function to sync and update each post
linkedin_posts_slider_get_post($post['id']);
}
} else {
error_log('LinkedIn Posts Slider - No posts found to process.');
}
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
global $scrapper_settings;

// Ensure scrapper settings are available
if (empty($scrapper_settings)) {
error_log('LinkedIn Posts Slider - Scrapper settings not available.');
return;
}

// Prepare the data for the scrapper endpoint
$data = array(
"secret_key" => "test", // Assuming a secret key is needed
"url" => $scrapper_settings['linkedin_company_url'],
"postSelector" => $scrapper_settings['linkedin_scrapper_full_post_selector'],
// Only requesting the URN for each post
"selectorsArray" => $scrapper_settings['linkedin_scrapper_full_selectors_array'],
"attributesArray" => $scrapper_settings['linkedin_scrapper_full_attributes_array'],
"namesArray" => $scrapper_settings['linkedin_scrapper_full_names_array']
);

$args = array(
'body' => json_encode($data),
'timeout' => 180, // 3 minutes
'headers' => array('Content-Type' => 'application/json')
);

// Send request to LinkedIn Scrapper Endpoint
$response = wp_remote_post($scrapper_settings['linkedin_scrapper_endpoint'], $args);

if (is_wp_error($response)) {
error_log('LinkedIn Posts Slider - Error in scrapper request: ' . $response->get_error_message());
return;
}

$posts = json_decode(wp_remote_retrieve_body($response), true);
if (isset($posts['results'])) {
foreach ($posts['results'] as $post) {
// Check if URN exists
if (isset($post['URN'][0])) {
$urn = sanitize_text_field($post['URN'][0]);

// Check if post already exists
$existing_post = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}lps_synced_posts WHERE urn = %s", $urn));
if (!$existing_post) {
// Insert new post with URN and set synced and published to false
$wpdb->insert(
"{$wpdb->prefix}lps_synced_posts",
array(
'urn' => $urn,
'synced' => 0,
'published' => 0
),
array('%s', '%d', '%d')
);
}
}
}
}

// Trigger process for unsynced posts
linkedin_posts_slider_process_unsynced_posts();
}
add_action('linkedin_posts_slider_cron_job', 'linkedin_posts_slider_update_posts');

// Schedule the cron job upon activation
register_activation_hook(__FILE__, 'linkedin_posts_slider_activation');
function linkedin_posts_slider_activation()
{
if (!wp_next_scheduled('linkedin_posts_slider_cron_job')) {
wp_schedule_event(time(), 'daily', 'linkedin_posts_slider_cron_job');
}
}



// Clear the scheduled hook upon deactivation
register_deactivation_hook(__FILE__, 'linkedin_posts_slider_deactivation');
function linkedin_posts_slider_deactivation()
{
wp_clear_scheduled_hook('linkedin_posts_slider_cron_job');
}

```
## Request 
assuming that the scrapper settings option has the following variables when deserialized : 
- linkedin_company_url
- linkedin_scrapper_status
- linkedin_scrapper_last_update
- linkedin_scrapper_endpoint
- linkedin_scrapper_full_post_selector
- linkedin_scrapper_full_selectors_array
- linkedin_scrapper_full_attributes_array
- linkedin_scrapper_full_names_array


now lets rewrite the the functions related to the post fetching from the api changing to calling the endpoint once daily recieving an array of all posts as shown below and the taking each post and perform validation as i will mention then the matching posts get compared with the posts in the lps_synced_posts table if it exists update the row as i will describe if not add new row and add the values

### validation steps:
1. first ignore the the posts that dont have images or have an empty array of images
2. ignore posts that have more than one urn 
3. check if the post exists in the table by urn applying different scenario in each case:
        * for new posts:
                1. if there is no author asign its value to `Alpine Laser`
                2. if there is no reactions assign its value to `0`
                3. if there is no comments assign its value to `0 Comments`
                4. if there is no username assign its value to `alpine-laser`
                5. remove any empty strings from the images array 
                6. if there is no age assign its value to `1mo`
                7. if there is no profilePicture assign its value to `https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4`
        * for existing posts:
                1. dont update author
                2. if there is no reactions in the response dont update its value 
                3. if there is no comments  in the response dont update its value 
                4. if there is no username  in the response dont update its value 
                5. remove any empty strings from the images array then if the number of recieved images is equal or more than the number of existing images update the value with the recived ones
                6. if there is no age in the response dont update its value
                7. if there is no profilePicture in the response dont update its value


### lps_synced_posts table schema :
```
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
```

### example response:
```
{"results":[{"URN":["urn:li:activity:7117516266000498688"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["3mo"],"author":["Alpine Laser"],"copy":["Come see a live demo of femtosecond tube cutting today and tomorrow at MDM in booth 2803!"],"reactions":["25"],"comments":["2 Comments"],"images":["https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_2048_1536/0/1696948113674?e=2147483647&v=beta&t=pUJztCmDCuirwcqkXm-eocdA4vDRh3ui20rHkAb44JQ"]},{"URN":["urn:li:activity:7110664133217288192"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["3mo"],"author":["Alpine Laser"],"copy":["Announcing the MediSCAN Pro - Alpine Laser's latest high performance laser processing workstation optimized for medical device manufacturing! \n\nThe configuration shown here features programmable XYZ motion coupled with a Scanlab precSYS 5-axis #micromachining galvo and a TRUMPF 2230 ultra short pulsed 515nm green #laser source and coaxial vision.  \n\nThis machine was designed to process very fine features and complex holes in hard to machine polymer materials. (Shown are 0.25mm holes in a 1mm Pellethane tube)\n\nOther configurations of this workstation can be optimized for flat sheet cutting, traditional 2D galvo applications, marking, complex ablation, to name a few.\n\nContact sales@alpinelaser.com for more information.\n\nSCANLAB GmbH\nTRUMPF North America\n#medicaldevicemanufacturing"],"reactions":["119"],"comments":["8 Comments"],"images":["https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_2048_1536/0/1695314437358?e=2147483647&v=beta&t=g53yY2_Unlp0wnDGnS_TTEDTrW0NGHcRX0S1CIklHFo","https://media.licdn.com/dms/image/D5622AQGu92JK888ZUw/feedshare-shrink_2048_1536/0/1695314437404?e=2147483647&v=beta&t=-b88XXslHvHLphceYz5JlbzeG7YYVmINZXIqThqWBWc","https://media.licdn.com/dms/image/D5622AQFevdEZ-d2RfQ/feedshare-shrink_2048_1536/0/1695314436890?e=2147483647&v=beta&t=ih0r1LCV_ZB-sXDRP1AOVc5loPEWN2jJEKXZbFeWEMw","https://media.licdn.com/dms/image/D5622AQGfdzbosfaiPw/feedshare-shrink_2048_1536/0/1695314437501?e=2147483647&v=beta&t=6IKGVD8z2EC-m8uVPJOZHu2x54fTs47_EL2QBoIIAPU","https://media.licdn.com/dms/image/D5622AQE9oTsaKKVG9A/feedshare-shrink_2048_1536/0/1695314437783?e=2147483647&v=beta&t=yIfktpvMC9WXX7mGKnJYtxwu5ayWztFyqWDEjIBNFRE"]},{"URN":["urn:li:activity:7105606901618417664"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["4mo"],"author":["Alpine Laser"],"copy":["We're #hiring a Senior Applications Engineer to join us in delivering the most advanced laser processing workstations for medical device manufacturing!\n\nInterested applicants please contact us at admin@alpinelaser.com. \n\nPlease direct applicants only, no recruiters or staffing agencies at this time."],"reactions":["14"]},{"URN":["urn:li:activity:7092583182209875968"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["5mo"],"author":["Alpine Laser"],"copy":["Laser cutting catheter shafts allows for continuously variable bending stiffness, torsion, and compression to open up a new realm of possibilities for medical device design engineers. \n\nShown below is a 0.027\" OD 0.0025\" wall (0.686mm OD .064mm wall) microcatheter shaft cut on the Medicut Pro Fiber Laser.\n\nContact us at sales@alpinelaser.com for more info.\n\nThank you TRUMPF for the photo."],"reactions":["30"],"images":["https://media.licdn.com/dms/image/D5622AQElkuOrteJbWg/feedshare-shrink_2048_1536/0/1691003603743?e=2147483647&v=beta&t=uzUu8eXOZj-hV1obM_xqJ_1EpIzgsr8ndXA-jajzBKw"]},{"URN":["urn:li:activity:7090069626461532160"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["5mo"],"author":["Alpine Laser"],"copy":["Laser cutting highly reflective material & precious metal alloys can prove challenging for traditional laser workstations. \n\nWe had a recent application inquiry to cut copper and ran the parts on our standard Medicut Pro system equipped with a Fiber Laser. The same success has been demonstrated with precious metal alloys such as Pt, PtIr, PtW, Au, Ag, etc.\n\nPart Description: Copper Tube 2mm OD with a 0.3mm wall\n\nContact us at sales@alpinelaser.com for more info."],"reactions":["85"],"comments":["4 Comments"],"images":["https://media.licdn.com/dms/image/D4E22AQH9L9hhXmwLhg/feedshare-shrink_2048_1536/0/1690404325095?e=2147483647&v=beta&t=1leGT7yzfLGC_qx0XcdwO4Uvg_DfL_Dl6lazWFU97lA"]},{"URN":["urn:li:activity:7085263372841041920"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["6mo"],"author":["Alpine Laser"],"copy":["Need cuts with no heat affected zone and very clean edges? Take a look at these sample parts cut with the Alpine Laser Medicut Pro workstation utilizing a top of the line ultra short pulse femtosecond laser from TRUMPF. "],"reactions":["119"],"comments":["6 Comments"],"images":["https://media.licdn.com/dms/image/D4D22AQGqLOmYU5zQJQ/feedshare-shrink_800/0/1689258424335?e=2147483647&v=beta&t=-6rp9Xf0Dcc19OP2pS9WSY1ZPvpR2byC4o3LTNJOsB0","https://media.licdn.com/dms/image/D4D22AQFjeXMtn0ZgcQ/feedshare-shrink_800/0/1689258424269?e=2147483647&v=beta&t=ulWCIS13Zekx0uRavaizBfl0bhN3KbcH-_E0hUx9Abo","https://media.licdn.com/dms/image/D4D22AQECZgYGzGDO6g/feedshare-shrink_800/0/1689258424307?e=2147483647&v=beta&t=nHve-1OHtoYZYZERG1sUN98Lo8W9xILLjzSsY30ZhOM","https://media.licdn.com/dms/image/D4D22AQEMYp-_RwB6hA/feedshare-shrink_800/0/1689258424267?e=2147483647&v=beta&t=TY1moYnXxNF9VEEiZ3qGfZ2pEyoa9eQJAT4xgOyAU4w"]},{"URN":["urn:li:activity:7084633761740423169"],"profilePicture":["https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153587/alpine_laser_logo?e=2147483647&v=beta&t=DhJSvH9xZJ6CHrzjOGQ6o9fJSwOCQKiM0fkw874hsv4"],"age":["6mo"],"author":["Alpine Laser"],"copy":["Just completed the installation of two femtosecond laser tube cutting workstations paired with bar feeders and custom Alpine automated part extractors enabling this customer to run catheter shaft production lights out.\n\nContact the team at Alpine Laser today to see how we can help you transform your laser cutting operation. \n\nsales@alpinelaser.com"],"reactions":["108"],"comments":["5 Comments"],"images":["https://media.licdn.com/dms/image/D5622AQE0uiOv1X59Og/feedshare-shrink_800/0/1689108312570?e=2147483647&v=beta&t=MhKywaOzFT-SfrK2DcERSUPNjXYukm46axKvQu96SBw","https://media.licdn.com/dms/image/D5622AQEDvNoAXKgCkA/feedshare-shrink_800/0/1689108308231?e=2147483647&v=beta&t=Dl1rGOTpPop83hp06wo5UIduYNMOoYFT_lZsBAl8iVs","https://media.licdn.com/dms/image/D5622AQGuLM3G0lYTmQ/feedshare-shrink_800/0/1689108310054?e=2147483647&v=beta&t=OrMIghhybZy-O4eSc7ij9zpqeROskH_ny_Af4mBl_Lg","https://media.licdn.com/dms/image/D5622AQEs3FWPkEZ4fg/feedshare-shrink_800/0/1689108313262?e=2147483647&v=beta&t=gz73kyrnFVcsz7-JgvO0Q8gE0F1OfTlMVJw6lncpG0I","https://media.licdn.com/dms/image/D5622AQGwIi2isOxGuQ/feedshare-shrink_800/0/1689108311592?e=2147483647&v=beta&t=aepOpoDEmiQz4zPfmX46gcfPtZwnKjvK8ramekwurxE"]},{"URN":["urn:li:activity:7084620236297015296","urn:li:activity:7084250167482200064"],"age":["6mo"],"reactions":["41"],"images":["https://media.licdn.com/dms/image/D5622AQE8KLX-4zEhng/feedshare-shrink_2048_1536/0/1689016857644?e=2147483647&v=beta&t=CwL7rBPo9NzfIv8wAG_1hQnFADFyE5O53ea8xuef48g"]},{"URN":["urn:li:activity:7047668554556420096","urn:li:activity:7046919667239575552"],"age":["9mo"],"reactions":["106"],"comments":["2 Comments"]},{"URN":["urn:li:activity:7038554054553210881","urn:li:activity:7038533440094289924"],"age":["10mo  Edited"],"reactions":["192"],"comments":["10 Comments"]},{"URN":["urn:li:activity:7033849074818707456","urn:li:activity:7033832487432724480"],"age":["10mo"],"images":["https://media.licdn.com/dms/image/C5622AQE76HZbjrDrwQ/feedshare-shrink_800/0/1676996341740?e=2147483647&v=beta&t=5sa2zdmbhXK330Ou3SOuRo5C15QABd0YF5iGDPLxWVc"]},{"URN":["urn:li:activity:7033826103584579584","urn:li:activity:7032378885606424577"],"age":["11mo  Edited"]},{"URN":["urn:li:activity:7032643995075760128"],"age":["11mo"],"copy":["We're #hiring a new Panel Builder in Bloomington, Minnesota. Apply today or share this post with your network."]},{"URN":["urn:li:activity:7027299780204580864","urn:li:activity:7026934813685075969"],"age":["11mo  Edited"],"images":["https://media.licdn.com/dms/image/C4E22AQF7ei6oYSOCHw/feedshare-shrink_2048_1536/0/1675351812793?e=2147483647&v=beta&t=IMD1lbiHGMtd0lDGMb52zmxuN81bMZxQq01oalr16I4"]},{"URN":["urn:li:activity:7023741456741777408"],"age":["11mo"],"copy":["* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management \n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com "],"images":["https://media.licdn.com/dms/image/C5622AQG3G4m1HdBRTQ/feedshare-shrink_800/0/1674590456558?e=2147483647&v=beta&t=jpm_qxl2hxtHLU2nIaHONFKdHT7oF_2CrLpR38FeQtk","https://media.licdn.com/dms/image/C5622AQHltS4_M21yfQ/feedshare-shrink_800/0/1674590456620?e=2147483647&v=beta&t=E83UT1nFkyqx001U2UcoWxivMH8zkX4snlVv-uEjr-U","https://media.licdn.com/dms/image/C5622AQGKlqfHEc9TVA/feedshare-shrink_800/0/1674590456761?e=2147483647&v=beta&t=CHr8prYsGvekN8iqV51sasSkCDILAjg-pSssm3THvoY","https://media.licdn.com/dms/image/C5622AQEw2Fhe4KSHUA/feedshare-shrink_800/0/1674590456630?e=2147483647&v=beta&t=o6OCUI3Nk29QRqA3Qvm53A8oQmxzTqRGEW2zz_nzpVo"]}]}
```

### the js code of the scrapper hosted on render for reference:
```
const express = require('express');
const puppeteer = require('puppeteer-extra');
const StealthPlugin = require('puppeteer-extra-plugin-stealth');
puppeteer.use(StealthPlugin());
const app = express();
const port = 3001;

app.use(express.json());
async function checkLastPostForCompany(page, postSelector, author) {
const posts = await page.$$(postSelector);
const lastPost = posts[posts.length - 1];

const textContent = await lastPost.evaluate(el => el.innerText);
if (textContent.includes(author)) {
return true;
}

const allAttributes = await lastPost.evaluate(el => {
const attributes = Array.from(el.attributes);
return attributes.map(attr => attr.value);
});

for (const attr of allAttributes) {
if (attr.includes(author)) {
return true;
}
}

return false;
}

app.post('/scrape', async (req, res) => {
const { secret_key, url, selectorsArray, attributesArray, namesArray, postSelector } = req.body;


if (secret_key !== 'test') {
return res.status(401).json({ error: 'Unauthorized' });
}

try {
const browser = await puppeteer.launch();
const page = await browser.newPage();
await page.goto(url, { timeout: 60000 });

let results = [];
let posts = [];
while (true) {
await page.evaluate(() => {
window.scrollBy(0, window.innerHeight);
});

try {
await page.waitForTimeout(3000);
} catch (error) {
break;
}

let currentHeight = await page.evaluate('document.body.scrollHeight');
let viewportHeight = await page.evaluate('window.innerHeight');
let scrollPosition = await page.evaluate('window.scrollY');
condition = await checkLastPostForCompany(page,postSelector,"Alpine Laser")

if (currentHeight <= viewportHeight + scrollPosition || condition == false) {
console.log(condition)
posts = await page.$$(postSelector);
console.log(`Found ${posts.length} posts.`);
break;
}



}

for (const post of posts) {
const itemData = {};

for (let i = 0; i < selectorsArray.length; i++) {
const selector = selectorsArray[i];
const attribute = attributesArray[i];
const name = namesArray[i];

try {
const elements = await post.$$(selector);
let values = [];

for (let element of elements) {
let value;

if (attribute === 'innerText') {
value = await element.evaluate(el => el.innerText);
} else {
value = await element.evaluate((el, attr) => el.getAttribute(attr), attribute);
}

if (value && value.trim() !== '') {
values.push(value);
}
}

if (values.length > 0) {
itemData[name] = values;
}
} catch (error) {
console.error(`Error retrieving data for selector "${selector}": ${error.message}`);
}
}

if (Object.keys(itemData).length > 0 && !results.some(result => JSON.stringify(result) === JSON.stringify(itemData))) {
results.push(itemData);
}
}

await browser.close();
res.json({ results });

} catch (error) {
console.error(error);
res.status(500).json({ error: 'Internal Server Error' });
}
});

app.listen(port, () => {
console.log(`Server is listening on port ${port}`);
});
```
### old version of ajax-actions.php:
```
<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
exit;
}



function publish_unpublish()
{
global $wpdb;
$table_name = $wpdb->prefix . 'lps_synced_posts';

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

// Fetch rows from the lps_synced_posts table
$table_name = $wpdb->prefix . 'lps_synced_posts';
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

function delete_post()
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
$table_name = $wpdb->prefix . 'lps_synced_posts';

foreach ($posts as $post) {
// Check if post has required data
if (!isset($post['URN'], $post['author'], $post['age'], $post['reactions'])) {
continue;
}

// Check company name
if ($post['author'][0] !== 'Alpine Laser') {
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
$table_name = $wpdb->prefix . 'lps_synced_posts';

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
$table_name = $wpdb->prefix . 'lps_synced_posts';

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

```