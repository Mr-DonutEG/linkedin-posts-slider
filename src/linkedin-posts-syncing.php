<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function sync_linkedin_posts()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'linkedin_posts';

    // Fetch all rows that are not synced
    $rows = $wpdb->get_results("SELECT * FROM $table_name WHERE synced = 0");

    foreach ($rows as $row) {
        $urn = $row->urn;

        // Make the API call to scrape-js.onrender.com
        $response = wp_remote_post('https://scrape-js.onrender.com/', [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode([
                'url' => $urn,
                'secretKey' => 'PzoiJcU2ocfOeWj6AQQdkQ'
            ])
        ]);

        if (is_wp_error($response)) {
            continue;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // Update the row in the database
        $wpdb->update(
            $table_name,
            [
                //'urn' => $data['urn'],
                'author' => $data['author'],
                'username' => $data['username'],
                'age' => $data['age'],
                'profilePicture' => $data['profilePicture'],
                'copy' => $data['copy'],
                'images' => json_encode($data['images']),
                'reactions' => $data['reactions'],
                'comments' => $data['comments'],
                'synced' => 1
            ],
            ['urn' => $urn],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d'],
            ['%s']
        );
    }
}

add_action('linkedin_posts_sync_event', 'sync_linkedin_posts');
