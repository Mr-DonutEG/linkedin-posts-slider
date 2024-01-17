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
                            'linkedin_scrapper_single_names_array' => '["URN","age","profilePicture","copy","reactions" ,"comments","images"]'