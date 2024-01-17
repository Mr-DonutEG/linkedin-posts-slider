<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

function linkedin_posts_slider_create_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'lps_synced_posts';
  $charset_collate = $wpdb->get_charset_collate();

  // Create table SQL
  $sql = "CREATE TABLE $table_name (
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

  // Decode JSON data
  $json_data = <<<'EOT'
      [
        {
          "urn": "urn:li:activity:7110664133217288192",
          "post_order": "1",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "1mo •",
          "copy": "Announcing the MediSCAN Pro - Alpine Laser's latest high performance laser processing workstation optimized for medical device manufacturing!\n\nThe configuration shown here features programmable XYZ motion coupled with a Scanlab precSYS 5-axis #micromachining galvo and a TRUMPF 2230 ultra short pulsed 515nm green #laser source and coaxial vision.\n\nThis machine was designed to process very fine features and complex holes in hard to machine polymer materials. (Shown are 0.25mm holes in a 1mm Pellethane tube)\n\nOther configurations of this workstation can be optimized for flat sheet cutting, traditional 2D galvo applications, marking, complex ablation, to name a few.\n\nContact sales@alpinelaser.com for more information.\n\nSCANLAB GmbH\nTRUMPF North America\n#medicaldevicemanufacturing",
          "images": [
            "https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY",
            "https://media.licdn.com/dms/image/D5622AQGu92JK888ZUw/feedshare-shrink_800/0/1695314437386?e=1700697600&v=beta&t=Zf7xMoDtwBTCN905mseXz8rk77dtmfOSm08Tfh7qUUI",
            "https://media.licdn.com/dms/image/D5622AQFevdEZ-d2RfQ/feedshare-shrink_800/0/1695314436856?e=1700697600&v=beta&t=5kRgmLzLb9VPGUlMPWnTGO79_n0hlqW7DhUVwQzs-zQ",
            "https://media.licdn.com/dms/image/D5622AQGfdzbosfaiPw/feedshare-shrink_800/0/1695314437494?e=1700697600&v=beta&t=og3iW9NjIz2VSbFj4aUi385BLsxLLuIZ2MmXvuAe4Ck",
            "https://media.licdn.com/dms/image/D5622AQE9oTsaKKVG9A/feedshare-shrink_800/0/1695314437828?e=1700697600&v=beta&t=eUkg72s4keVlwaJ0QjqK5cz2Pk9LltlbcXA6wY3CizU"
          ],
          "reactions": "116",
          "comments": "8 comments"
        },
        {
          "urn": "urn:li:activity:7117516266000498688",
          "post_order": "3",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "1w •",
          "copy": "Come see a live demo of femtosecond tube cutting today and tomorrow at MDM in booth 2803!",
          "images": [
            "https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4"
          ],
          "reactions": "20",
          "comments": "1 comment"
        },
        {
          "urn": "urn:li:activity:7084633761740423169",
          "post_order": "5",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "3mo •",
          "copy": "Just completed the installation of two femtosecond laser tube cutting workstations paired with bar feeders and custom Alpine automated part extractors enabling this customer to run catheter shaft production lights out.\n\nContact the team at Alpine Laser today to see how we can help you transform your laser cutting operation.\n\nsales@alpinelaser.com",
          "images": [
            "https://media.licdn.com/dms/image/D5622AiOX59Og",
            "https://media.licdn.com/dms/image/D5622AXKgCkA",
            "https://media.licdn.com/dms/image/D56223G0lYTmQ",
            "https://media.licdn.com/dms/image/D56FWPkEZ4fg",
            "https://media.licdn.com/dms/image/D5622sOxGuQ"
          ],
          "reactions": "108",
          "comments": "5 comments"
        },
        {
          "urn": "urn:li:activity:7085263372841041920",
          "post_order": "6",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "3mo •",
          "copy": "Need cuts with no heat affected zone and very clean edges? Take a look at these sample parts cut with the Alpine Laser Medicut Pro workstation utilizing a top of the line ultra short pulse femtosecond laser from TRUMPF.",
          "images": [
            "https://media.licdn.com/dms/image/D4D22AYU5zQJQ/",
            "https://media.licdn.com/dms/image/D4D22AQFMtn0ZgcQ",
            "https://media.licdn.com/dms/image/D4D22AQGzGDO6g",
            "https://media.licdn.com/dms/image/D4D22AQEMRwB6hA"
          ],
          "reactions": "120",
          "comments": "6 comments"
        },
        {
          "urn": "urn:li:activity:7023741456741777408",
          "post_order": "8",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "9mo •",
          "copy": "* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management\n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com",
          "images": [
            "https://media.licdn.com/dms/image/C5622AQGdBRTQ/",
            "https://media.licdn.com/dms/image/C5622AQHl21yfQ/",
            "https://media.licdn.com/dms/image/C5622AQGc9TVA/",
            "https://media.licdn.com/dms/image/C5622AQEw2Fhe4K"
          ],
      
          "reactions": "28",
          "comments": "0 comments"
        },
        {
          "urn": "urn:li:activity:7015728663870541824",
          "post_order": "14",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "10mo •",
          "copy": "* Workstation Spotlight * \n\n Our team shipped and installed this 4-axis Fiber Laser Tube Cutter just before Christmas! \n\n This workstation is configured with... \n\n - Programmable 4-axis motion control \n\n - Complete Quick Change Tooling and Class 1 Cutting Enclosure \n\n - Integrated Closed loop wet cut system \n\n - Compact footprint with side access E-box",
          "images": [
            "https://media.licdn.com/dms/image/C5622AQHvYERDghz__Q/",
            "https://media.licdn.com/dms/image/C5622AQGWbi5H_tF4dg/",
            "https://media.licdn.com/dms/image/C5622AQFZhAkBS2MxdA/",
            "https://media.licdn.com/dms/image/C5622AQE27JYCc571qg/"
          ],
      
          "reactions": "24",
          "comments": "4 comments"
        },
        {
          "urn": "urn:li:activity:7092583182209875968",
          "post_order": "20",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "2mo •",
          "copy": "Laser cutting catheter shafts allows for continuously variable bending stiffness, torsion, and compression to open up a new realm of possibilities for medical device design engineers.\n\nShown below is a 0.027\" OD 0.0025\" wall (0.686mm OD .064mm wall) microcatheter shaft cut on the Medicut Pro Fiber Laser.\n\nContact us at sales@alpinelaser.com for more info.\n\nThank you TRUMPF for the photo.",
          "images": [
            "https://media.licdn.com/dms/image/D5622AQElkuOrteJbWg/feedshare-shrink_800"
          ],
      
          "reactions": "30",
          "comments": "0 comments"
        },
        {
          "urn": "urn:li:activity:7090069626461532160",
          "post_order": "21",
          "profilePicture": "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA",
          "author": "Alpine Laser",
          "username": "alpine-laser",
          "age": "3mo •",
          "copy": "Laser cutting highly reflective material & precious metal alloys can prove challenging for traditional laser workstations.\n\nWe had a recent application inquiry to cut copper and ran the parts on our standard Medicut Pro system equipped with a Fiber Laser. The same success has been demonstrated with precious metal alloys such as Pt, PtIr, PtW, Au, Ag, etc.\n\nPart Description: Copper Tube 2mm OD with a 0.3mm wall\n\nContact us at sales@alpinelaser.com for more info.",
          "images": [
            "https://media.licdn.com/dms/image/D4E22AQH9L9hhXmwLhg/feedshare-shrink_800/0/1690404325098?e=1700697600&v=beta&t=bf4OKRAsom5vyJVJZ1G9oS3Ay3x2Imvr-4EJC2j5Whs"
          ],
          "reactions": "85",
          "comments": "4 comments"
        }
      ]
      EOT;

  // Decode JSON data into a PHP array
  $data = json_decode($json_data, true);

  // Insert each item into the database
  foreach ($data as $item) {
    // Sanitize data
    $sanitized_data = array(
      'urn' => sanitize_text_field($item['urn']),
      'author' => sanitize_text_field($item['author']),
      'username' => sanitize_text_field($item['username']),
      'age' => sanitize_text_field($item['age']),
      'profilePicture' => esc_url_raw($item['profilePicture']),
      'copy' => sanitize_text_field($item['copy']),
      'images' => maybe_serialize($item['images']),
      'reactions' => intval($item['reactions']),
      'comments' => sanitize_text_field($item['comments']),
      'synced' => 1,
      'published' => 1,
      'post_order' => 0 // Temporary placeholder
    );

    $wpdb->insert($table_name, $sanitized_data);

    // Update 'post_order' to match the row ID
    $last_id = $wpdb->insert_id;
    $wpdb->update($table_name, array('post_order' => $last_id), array('id' => $last_id));
  }
}
// Activation function to initialize plugin options
function linkedin_posts_slider_activate()
{
  // Define default settings
  $default_settings = array(
    'linkedin_company_url' => 'https://www.linkedin.com/company/alpine-laser/',
    'linkedin_slider_open_link' => '1',
    'linkedin_update_frequency' => '86400', // 24 hours in seconds
    'linkedin_scrapper_status' => 'OK',
    'linkedin_scrapper_last_update' => 'Not available',
    'linkedin_scrapper_endpoint' => 'https://scrape-js.onrender.com/scrape',
    'linkedin_scrapper_full_post_selector' => 'li[class="mb-1"]',
    'linkedin_scrapper_full_selectors_array' => [
      'li[class="mb-1"] article',
      "a[data-tracking-control-name='organization_guest_main-feed-card_feed-actor-name']",
      'time',
      'a[data-tracking-control-name="organization_guest_main-feed-card_social-actions-reactions"]',
      'a[data-tracking-control-name="organization_guest_main-feed-card_social-actions-comments"]'
    ],
    'linkedin_scrapper_full_attributes_array' => '["data-activity-urn","innerText","innerText","innerText","innerText"]',
    'linkedin_scrapper_full_names_array' => '["URN","author","age","reactions" ,"comments"]',
    'linkedin_scrapper_single_post_selector' => 'section[class="mb-3"]',
    'linkedin_scrapper_single_selectors_array' => [
      'section[class="mb-3"] article',
      'time',
      'a[data-tracking-control-name="public_post_feed-actor-image"] img',
      'p[data-test-id="main-feed-activity-card_commentary"]',
      'span[data-test-id="social-actionsreaction-count"]',
      'a[data-test-id="social-actions_comments"]',
      'ul[data-test-id="feed-images-content"] img'
    ],
    'linkedin_scrapper_single_attributes_array' => '["data-attributed-urn","innerText","src","innerText","innerText","innerText","src"]',
    'linkedin_scrapper_single_names_array' => '["URN","age","profilePicture","copy","reactions" ,"comments","images"]' // Add other settings as needed
  );

  // Add or update default settings in WordPress options
  foreach ($default_settings as $setting_name => $default_value) {
    if (false === get_option($setting_name)) {
      add_option($setting_name, $default_value);
    }
  }
}

// Hook activation function to plugin activation
register_activation_hook(__FILE__, 'linkedin_posts_slider_activate');

register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
