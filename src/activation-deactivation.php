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
