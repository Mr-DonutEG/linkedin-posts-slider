# Linkedin Posts Slider Plugin

This is a custom WordPress plugin that allows you to add LinkedIn posts to a slider widget on your website. The plugin is developed by Omar Nagy.

## Main Plugin File: linkedin-posts-slider.php

This file is the entry point of the plugin. It includes the following functionalities:

- **register_slider_widget**: This function registers a new Elementor widget for the slider. It requires the `slider-widget.php` file and registers a new instance of the `Elementor_Slider_Widget` class.

- **linkedin_posts_slider_options_page**: This function creates an options page for the Linkedin Posts Slider widget in the WordPress admin menu. It handles the form submission for adding a new LinkedIn URL to the database.

- **linkedin_posts_slider_create_table**: This function creates a new table in the WordPress database for storing LinkedIn posts. It is called when the plugin is activated.

- **linkedin_posts_slider_admin_table_page**: This function creates an admin page that displays a table of all LinkedIn posts stored in the database. It also handles the delete action for deleting a post from the database.

- **update_row**: This function is an AJAX handler for updating a row in the LinkedIn posts table. It is called when the Sync button is clicked in the admin table page.

- **publish_unpublish**: This function is an AJAX handler for publishing or unpublishing a LinkedIn post. It is called when the Publish/Unpublish button is clicked in the admin table page.

- **get_linkedin_posts**: This function is an AJAX handler for fetching LinkedIn posts that are both synced and published. It is called from the frontend.

- **linkedin_posts_slider_add_admin_menu**: This function adds the Linkedin Posts Slider options page and the Posts Table page to the WordPress admin menu.

The plugin also includes some AJAX handlers and a function for creating the LinkedIn posts table in the WordPress database when the plugin is activated.

## Other Files

- **public/script.js**: This file contains the JavaScript code for the frontend of the plugin. It handles the AJAX requests for fetching LinkedIn posts and updating the slider widget. The file uses the jQuery library to handle AJAX requests and the Swiper library to create a responsive slider. The file first initializes a Swiper slider with specific options, including breakpoints to adjust the slider's configuration based on the window's width. Then, it sends an AJAX POST request to fetch LinkedIn posts. If the request is successful, it processes the returned data and updates the slider with the fetched LinkedIn posts.

- **src/slider-widget.php**: This file defines the `Elementor_Slider_Widget` class for the slider widget. The `Elementor_Slider_Widget` class extends the `Elementor\Widget_Base` class. The constructor of the class registers and enqueues the necessary styles and scripts for the slider widget. The `get_name`, `get_title`, `get_icon`, `get_custom_help_url`, `get_categories`, and `get_keywords` methods provide the basic information about the widget. The `register_controls` method defines the controls for the widget, such as the typography and the number of lines for the post post_text. The `render` method generates the HTML output for the widget on the frontend, and the `content_template` method generates the live preview of the widget in the Elementor editor.

## CSS Classes Used in public/script.js

Here is a detailed explanation of the CSS classes used in `public/script.js`:

1. `.swiper-slide`: This class is used for each slide in the Swiper slider.
2. `.li-icon-white`: This class is used for the LinkedIn icon in each slide.
3. `.img-container`: This class is used for the container of the images in each slide.
4. `.li-single-img`, `.li-img-two`, `.li-img-three-main`, `.li-img-three-sec-container`, `.li-img-three-sec`: These classes are used for the images in each slide, depending on the number of images.
5. `.info-container`: This class is used for the container of the information in each slide.
6. `.li-author-img`: This class is used for the author's image in each slide.
7. `.section-company`: This class is used for the author's name in each slide.
8. `.section-author-date`: This class is used for the container of the username and post age in each slide.
9. `.section-body`: This class is used for the post post_text in each slide.
10. `.section-interactions`: This class is used for the container of the reactions and comments in each slide.
