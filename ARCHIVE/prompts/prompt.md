for a wordpress plugin called linkedin posts slider
write the code for the php file responsible for registering an elemenor widget that shows a slider of posts getting the posts from a custom table `lps_synced_posts`
the slider is created using the swiperjs library including its full code in the plugin package for easeir implementation
the schema of the lps_synced_posts table is :

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

we are currently using a separate js file containing the code responsile for showing the slider and including it in the php widget registration file but we are going to include all the code in the php file so here is the old js code for reference:

```
// This is a jQuery function that runs when the document is ready
jQuery(function ($) {
// Initialize the Swiper slider
// Swiper is a powerful JavaScript library to implement responsive, accessible, flexible, touch-enabled carouses/sliders on your mobile websites and apps.
var swiper = new Swiper('.swiper', {
slidesPerView: 1,
spaceBetween: 10,
navigation: {
nextEl: '.next-right-arrow',
prevEl: '.pre-left-arrow',
},
breakpoints: {
// Breakpoints in Swiper are used to change slider's configuration (like slidesPerView, spaceBetween) dynamically on window resize event.
// when window width is >= 320px
480: {
slidesPerView: 1, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
},
// when window width is >= 480px
768: {
slidesPerView: 2, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
},
// when window width is >= 640px
1024: {
slidesPerView: 3, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
}
}
// Add more options if needed
// For more options, you can refer to the official Swiper API documentation: https://swiperjs.com/swiper-api
});

// Fetch LinkedIn posts using the AJAX request
// AJAX is a technique for creating fast and dynamic web pages.
$.ajax({
url: ajax_object.ajax_url, // The URL to which the request is sent
type: 'POST', // The type of HTTP method (post, get, etc)
data: {
action: 'get_linkedin_posts' // Data to be sent to the server
},
success: function (response) {
// A function to be run when the request succeeds
if (response.success) {
// Process the rows returned in response.data
var rows = response.data; // The data returned from the server
var processedRows = []; // An array to store the processed rows
for (var i = 0; i < rows.length; i++) { var row=rows[i]; // The current row var processedRow={}; // An object to store the processed row // Loop through each key in the row for (var key in row) { // Check if the row has the key as its own property if (row.hasOwnProperty(key)) { // Add the key-value pair to the processed row processedRow[key]=row[key]; } } // Add the processed row to the processedRows array processedRows.push(processedRow); } // Use the processedRows array to create and display slider items $('.li-placeholder').hide(); processedRows.forEach(function (post) { post.images=post.images.filter((img)=> img !== '');
let imagesHtml = '';
if (post.images.length === 1) {
imagesHtml = `<div class="li-single-img" style="background-image: url('${post.images[0].replace(/\\/g, '')}')"></div>`;
} else if (post.images.length === 2) {
imagesHtml = post.images.map(img => `<div class="li-img-two" style="background-image: url('${img.replace(/\\/g, '')}')"></div>`).join('');
} else if (post.images.length >= 3) {
imagesHtml = `<div class="li-img-three-main" style="background-image: url('${post.images[0].replace(/\\/g, '')}')"></div>` +
`<div class="li-img-three-sec-container">` +
`<div class="li-img-three-sec" style="background-image: url('${post.images[1].replace(/\\/g, '')}')"></div>` +
`<div class="li-img-three-sec" style="background-image: url('${post.images[2].replace(/\\/g, '')}')"></div>` +
`</div>`;
}
var slide = document.createElement('div');
slide.className = 'swiper-slide';
slide.addEventListener('click', function () {
//DONE: Add the URN to URL for the post
window.open('https://www.linkedin.com/feed/update/' + post.urn, '_blank');
});
slide.innerHTML = `
<div class="li-icon-white">
<svg style="width: 30px; height: 30px; overflow: visible; fill: rgb(255, 255, 255);" viewBox="0 0 448 512">
<path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path>
</svg>
</div>
<div class="img-container">
${imagesHtml}
</div>

<div class="info-container">
<div class="li-author-img" style="background-image: url('${post.profilePicture}')"></div>
<div class="section-company section-company">${post.author}</div>
<div class="section-author-date">
<span class="li-author-username">@${post.username} . </span>
<span class="li-post-age">${post.age} ago</span>
</div>
<p class="section-body">${post.copy}</p>
<div class="section-interactions">
<span><svg style="width: 24px; height: 24px; overflow: visible; fill: rgb(0, 122, 255);" viewBox="0 0 24 24">
<path fill="none" d="M0 0h24v24H0z"></path>
<path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path>
<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
<path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path>
</svg></span>
<span class="li-post-reactions">${post.reactions} . </span>
<span class="li-post-comments">${post.comments}</span>
</div>
</div>


</div>
`;
swiper.appendSlide(slide);
});


}
}
});
});

```

previously we were using wp settings api to store style option creating an option for each variable which we will replace by elementor widget style controls, here is the css variables and the values we were customizing:

```
// Company Name
.section-company {
color: {$settings['section-company-color']};
font-size: {$settings['section-company-font-size']}px;
font-family: {$settings['section-company-font-family']};
line-height: {$settings['section-company-line-height']}px;
}

// Author and Date
.section-author-date {
color: {$settings['section-author-date-color']};
font-size: {$settings['section-author-date-font-size']}px;
font-family: {$settings['section-author-date-font-family']};
font-weight: {$settings['section-author-date-font-weight']};
line-height: {$settings['section-author-date-line-height']}px;
}

// Post Text
.section-body {
color: {$settings['section-body-color']};
font-size: {$settings['section-body-font-size']}px;
font-family: {$settings['section-body-font-family']};
-webkit-line-clamp: {$settings['section-body-webkit-line-clamp']};
}

// Interactions
.section-interactions {
color: {$settings['section-interactions-color']};
font-size: {$settings['section-interactions-font-size']}px;
font-family: {$settings['section-interactions-font-family']};
font-weight: {$settings['section-interactions-font-weight']};
line-height: {$settings['section-interactions-line-height']}px;
}
```

the current main plugin file:
linkedin-posts-slider.php

```
<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
exit;
}

function enqueue_custom_scripts_and_styles()
{
wp_enqueue_style('custom-style', plugins_url('style.css', __FILE__));
wp_enqueue_script('custom-script', plugins_url('script.js', __FILE__), array('jquery', 'jquery-ui-sortable'), null, true);
wp_localize_script('custom-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts_and_styles');

// Include the new files
require_once plugin_dir_path(__FILE__) . 'src/widget-registration.php';
require_once plugin_dir_path(__FILE__) . 'src/options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/scrapper-options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/db-table-creation.php';
require_once plugin_dir_path(__FILE__) . 'src/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'src/ajax-actions.php';
//require_once plugin_dir_path(__FILE__) . 'src/linkedin-posts-syncing.php';
require_once plugin_dir_path(__FILE__) . 'src/table-page.php';


function move_row()
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
$action = sanitize_text_field($_POST['action']);

// Fetch current post_order and IDs
$rows = $wpdb->get_results("SELECT id, post_order FROM $table_name ORDER BY post_order ASC");

// Find the index of the row to be moved
$index = array_search($id, array_column($rows, 'id'));

if ($index !== false && $index >= 0 && $index < count($rows)) {
if ($action === 'move_up' && $index > 0) {
// Step 3: Swap post_order values for moving up
$swap_index = $index - 1;
} elseif ($action === 'move_down' && $index < count($rows) - 1) {
// Step 3: Swap post_order values for moving down
$swap_index = $index + 1;
} else {
wp_send_json_error('Invalid move');
return;
}

// Step 4: Update the database
$wpdb->query("START TRANSACTION");

$wpdb->update(
$table_name,
array('post_order' => $rows[$swap_index]->post_order),
array('id' => $id)
);

$wpdb->update(
$table_name,
array('post_order' => $rows[$index]->post_order),
array('id' => $rows[$swap_index]->id)
);

$wpdb->query("COMMIT");

wp_send_json_success('Row moved successfully');
} else {
wp_send_json_error('Invalid ID or index');
}
}
add_action('wp_ajax_move_up', 'move_row');
add_action('wp_ajax_move_down', 'move_row');

register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
register_activation_hook(__FILE__, 'linkedin_posts_slider_activate');

```

the old php code for the widget registration :

```
<?php

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
exit;
}

/**
 * Elementor Slider Widget.
 *
 * @since 1.0.0
 */
class Elementor_Slider_Widget extends \Elementor\Widget_Base
{

/**
* Constructor
*/
public function __construct($data = [], $args = null)
{

// Call parent constructor
parent::__construct($data, $args);

// Enqueue styles
wp_register_style('slider-style', plugins_url('../public/styles.css', __FILE__));
wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));

// Enqueue scripts
wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);
wp_enqueue_script('swiper-script');

wp_register_script('slider-script', plugins_url('../public/script.js', __FILE__), ['jquery', 'swiper-script'], false, true);
wp_localize_script('slider-script', 'ajax_object', [
'ajax_url' => admin_url('admin-ajax.php')
]);

// Enqueue styles
wp_enqueue_style('slider-style');

// Get custom settings
$settings = array(
'section-company-color' => get_option('section-company-color'),
'section-company-font-size' => get_option('section-company-font-size'),
'section-company-font-family' => get_option('section-company-font-family'),
'section-company-line-height' => get_option('section-company-line-height'),
'section-author-date-color' => get_option('section-author-date-color'),
'section-author-date-font-size' => get_option('section-author-date-font-size'),
'section-author-date-font-family' => get_option('section-author-date-font-family'),
'section-author-date-font-weight' => get_option('section-author-date-font-weight'),
'section-author-date-line-height' => get_option('section-author-date-line-height'),
'section-body-color' => get_option('section-body-color'),
'section-body-font-size' => get_option('section-body-font-size'),
'section-body-font-family' => get_option('section-body-font-family'),
'section-body-webkit-line-clamp' => get_option('section-body-webkit-line-clamp'),
'section-interactions-color' => get_option('section-interactions-color'),
'section-interactions-font-size' => get_option('section-interactions-font-size'),
'section-interactions-font-family' => get_option('section-interactions-font-family'),
'section-interactions-font-weight' => get_option('section-interactions-font-weight'),
'section-interactions-line-height' => get_option('section-interactions-line-height'),
);

// Add custom CSS
$custom_css = "
// Company Name
.section-company {
color: {$settings['section-company-color']};
font-size: {$settings['section-company-font-size']}px;
font-family: {$settings['section-company-font-family']};
line-height: {$settings['section-company-line-height']}px;
}

// Author and Date
.section-author-date {
color: {$settings['section-author-date-color']};
font-size: {$settings['section-author-date-font-size']}px;
font-family: {$settings['section-author-date-font-family']};
font-weight: {$settings['section-author-date-font-weight']};
line-height: {$settings['section-author-date-line-height']}px;
}

// Post Text
.section-body {
color: {$settings['section-body-color']};
font-size: {$settings['section-body-font-size']}px;
font-family: {$settings['section-body-font-family']};
-webkit-line-clamp: {$settings['section-body-webkit-line-clamp']};
}

// Interactions
.section-interactions {
color: {$settings['section-interactions-color']};
font-size: {$settings['section-interactions-font-size']}px;
font-family: {$settings['section-interactions-font-family']};
font-weight: {$settings['section-interactions-font-weight']};
line-height: {$settings['section-interactions-line-height']}px;
}
";

wp_add_inline_style('slider-style', $custom_css);
}

/**
* Get widget name
*/
public function get_name()
{
return 'Linkedin Slider';
}

/**
* Get widget title
*/
public function get_title()
{
return __('Linkedin Slider', 'linkedin-slider');
}

/**
* Get widget icon
*/
public function get_icon()
{
return 'eicon-slider-album';
}

/**
* Get widget categories
*/
public function get_categories()
{
return ['general'];
}

/**
* Register widget controls
*/
protected function register_controls()
{

// Custom CSS
$this->start_controls_section(
'section_custom_css',
[
'label' => __('Custom CSS', 'linkedin-slider'),
'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
]
);

$this->add_control(
'custom_css',
[
'label' => __('Custom CSS', 'linkedin-slider'),
'type' => \Elementor\Controls_Manager::CODE,
'language' => 'css',
'rows' => 10,
'default' => '',
'description' => __('Add custom CSS here', 'linkedin-slider'),
]
);

$this->end_controls_section();
}

/**
* Render widget output
*/
protected function render()
{
wp_enqueue_style('swiper-style');
wp_enqueue_script('swiper-script');
wp_enqueue_script('slider-script');
wp_enqueue_style('slider-style');



?>

<div class="swiper">
<div class="swiper-wrapper">
<div class="li-placeholder swiper-slide"></div>
</div>
<!-- Add Arrows -->
<div class="next-right-arrow"><button type="button" class="slick-next"><svg fill="#000000" height="35px" width="35px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
<g>
<g>
<path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M335.083,271.083L228.416,377.749c-4.16,4.16-9.621,6.251-15.083,6.251c-5.461,0-10.923-2.091-15.083-6.251c-8.341-8.341-8.341-21.824,0-30.165L289.835,256l-91.584-91.584c-8.341-8.341-8.341-21.824,0-30.165s21.824-8.341,30.165,0l106.667,106.667C343.424,249.259,343.424,262.741,335.083,271.083z" />
</g>
</g>
</svg></button></div>
<div class="pre-left-arrow"><button type="button" class="slick-prev"><svg fill="#000000" height="35px" width="35px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
<g>
<g>
<path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M313.749,347.584c8.341,8.341,8.341,21.824,0,30.165c-4.16,4.16-9.621,6.251-15.083,6.251c-5.461,0-10.923-2.091-15.083-6.251L176.917,271.083c-8.341-8.341-8.341-21.824,0-30.165l106.667-106.667c8.341-8.341,21.824-8.341,30.165,0s8.341,21.824,0,30.165L222.165,256L313.749,347.584z" />
</g>
</g>
</svg></button></div>
</div>

<?php
}
}

```

now return the full php file for this widget leaving the functions empty having only comments with its functionality so we can write the code of each function separately

---

###############################################################################

now write the full line by line code for the function register_controls()
adding controls for :

- Company Name:

* Class Name : .section-company
* the control should handle the text and font properties of the element with this class name

- Autor And Date:

* Class Name: .section-author-date
* the control should handle the text and font properties of the element with this class name

- Interactions:

* Class Name: .section-interactions
* the control should handle the text and font properties of the element with this class name

- Copy:

* Class Name: .section-body
* the control should handle the text and font properties of the element with this class nameand the -webkit-line-clamp property is controled by a field with title number of lines

return the full line by line code for all controls

---

###############################################################################

for a wordpress plugin called linkedin posts slider
write the code for the php file responsible for registering an elemenor widget that shows a slider of posts getting the posts from a custom table `lps_synced_posts`
the slider is created using the swiperjs library including its full code in the plugin package for easeir implementation
the schema of the lps_synced_posts table is :

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

we are currently using a separate js file containing the code responsile for showing the slider and including it in the php widget registration file but we are going to include all the code in the php file so here is the old js code for reference:

```
// This is a jQuery function that runs when the document is ready
jQuery(function ($) {
// Initialize the Swiper slider
// Swiper is a powerful JavaScript library to implement responsive, accessible, flexible, touch-enabled carouses/sliders on your mobile websites and apps.
var swiper = new Swiper('.swiper', {
slidesPerView: 1,
spaceBetween: 10,
navigation: {
nextEl: '.next-right-arrow',
prevEl: '.pre-left-arrow',
},
breakpoints: {
// Breakpoints in Swiper are used to change slider's configuration (like slidesPerView, spaceBetween) dynamically on window resize event.
// when window width is >= 320px
480: {
slidesPerView: 1, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
},
// when window width is >= 480px
768: {
slidesPerView: 2, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
},
// when window width is >= 640px
1024: {
slidesPerView: 3, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
}
}
// Add more options if needed
// For more options, you can refer to the official Swiper API documentation: https://swiperjs.com/swiper-api
});

// Fetch LinkedIn posts using the AJAX request
// AJAX is a technique for creating fast and dynamic web pages.
$.ajax({
url: ajax_object.ajax_url, // The URL to which the request is sent
type: 'POST', // The type of HTTP method (post, get, etc)
data: {
action: 'get_linkedin_posts' // Data to be sent to the server
},
success: function (response) {
// A function to be run when the request succeeds
if (response.success) {
// Process the rows returned in response.data
var rows = response.data; // The data returned from the server
var processedRows = []; // An array to store the processed rows
for (var i = 0; i < rows.length; i++) { var row=rows[i]; // The current row var processedRow={}; // An object to store the processed row // Loop through each key in the row for (var key in row) { // Check if the row has the key as its own property if (row.hasOwnProperty(key)) { // Add the key-value pair to the processed row processedRow[key]=row[key]; } } // Add the processed row to the processedRows array processedRows.push(processedRow); } // Use the processedRows array to create and display slider items $('.li-placeholder').hide(); processedRows.forEach(function (post) { post.images=post.images.filter((img)=> img !== '');
let imagesHtml = '';
if (post.images.length === 1) {
imagesHtml = `<div class="li-single-img" style="background-image: url('${post.images[0].replace(/\\/g, '')}')"></div>`;
} else if (post.images.length === 2) {
imagesHtml = post.images.map(img => `<div class="li-img-two" style="background-image: url('${img.replace(/\\/g, '')}')"></div>`).join('');
} else if (post.images.length >= 3) {
imagesHtml = `<div class="li-img-three-main" style="background-image: url('${post.images[0].replace(/\\/g, '')}')"></div>` +
`<div class="li-img-three-sec-container">` +
`<div class="li-img-three-sec" style="background-image: url('${post.images[1].replace(/\\/g, '')}')"></div>` +
`<div class="li-img-three-sec" style="background-image: url('${post.images[2].replace(/\\/g, '')}')"></div>` +
`</div>`;
}
var slide = document.createElement('div');
slide.className = 'swiper-slide';
slide.addEventListener('click', function () {
//DONE: Add the URN to URL for the post
window.open('https://www.linkedin.com/feed/update/' + post.urn, '_blank');
});
slide.innerHTML = `
<div class="li-icon-white">
<svg style="width: 30px; height: 30px; overflow: visible; fill: rgb(255, 255, 255);" viewBox="0 0 448 512">
<path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path>
</svg>
</div>
<div class="img-container">
${imagesHtml}
</div>

<div class="info-container">
<div class="li-author-img" style="background-image: url('${post.profilePicture}')"></div>
<div class="section-company section-company">${post.author}</div>
<div class="section-author-date">
<span class="li-author-username">@${post.username} . </span>
<span class="li-post-age">${post.age} ago</span>
</div>
<p class="section-body">${post.copy}</p>
<div class="section-interactions">
<span><svg style="width: 24px; height: 24px; overflow: visible; fill: rgb(0, 122, 255);" viewBox="0 0 24 24">
<path fill="none" d="M0 0h24v24H0z"></path>
<path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path>
<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
<path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path>
</svg></span>
<span class="li-post-reactions">${post.reactions} . </span>
<span class="li-post-comments">${post.comments}</span>
</div>
</div>


</div>
`;
swiper.appendSlide(slide);
});


}
}
});
});

```

previously we were using wp settings api to store style option creating an option for each variable which we will replace by elementor widget style controls, here is the css variables and the values we were customizing:

```
// Company Name
.section-company {
color: {$settings['section-company-color']};
font-size: {$settings['section-company-font-size']}px;
font-family: {$settings['section-company-font-family']};
line-height: {$settings['section-company-line-height']}px;
}

// Author and Date
.section-author-date {
color: {$settings['section-author-date-color']};
font-size: {$settings['section-author-date-font-size']}px;
font-family: {$settings['section-author-date-font-family']};
font-weight: {$settings['section-author-date-font-weight']};
line-height: {$settings['section-author-date-line-height']}px;
}

// Post Text
.section-body {
color: {$settings['section-body-color']};
font-size: {$settings['section-body-font-size']}px;
font-family: {$settings['section-body-font-family']};
-webkit-line-clamp: {$settings['section-body-webkit-line-clamp']};
}

// Interactions
.section-interactions {
color: {$settings['section-interactions-color']};
font-size: {$settings['section-interactions-font-size']}px;
font-family: {$settings['section-interactions-font-family']};
font-weight: {$settings['section-interactions-font-weight']};
line-height: {$settings['section-interactions-line-height']}px;
}
```

the current main plugin file:
linkedin-posts-slider.php

```
<?php
/*
Plugin Name: Linkedin Posts Slider
Plugin URI: https://github.com/omarnagy91
Description: This is a custom plugin that I'm developing.
Version: 2.0
Author: Omar Nagy
Author URI: https://github.com/omarnagy91
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
exit;
}

function enqueue_custom_scripts_and_styles()
{
wp_enqueue_style('custom-style', plugins_url('style.css', __FILE__));
wp_enqueue_script('custom-script', plugins_url('script.js', __FILE__), array('jquery', 'jquery-ui-sortable'), null, true);
wp_localize_script('custom-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_custom_scripts_and_styles');

// Include the new files
require_once plugin_dir_path(__FILE__) . 'src/widget-registration.php';
require_once plugin_dir_path(__FILE__) . 'src/options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/scrapper-options-page.php';
require_once plugin_dir_path(__FILE__) . 'src/db-table-creation.php';
require_once plugin_dir_path(__FILE__) . 'src/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'src/ajax-actions.php';
//require_once plugin_dir_path(__FILE__) . 'src/linkedin-posts-syncing.php';
require_once plugin_dir_path(__FILE__) . 'src/table-page.php';


function move_row()
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
$action = sanitize_text_field($_POST['action']);

// Fetch current post_order and IDs
$rows = $wpdb->get_results("SELECT id, post_order FROM $table_name ORDER BY post_order ASC");

// Find the index of the row to be moved
$index = array_search($id, array_column($rows, 'id'));

if ($index !== false && $index >= 0 && $index < count($rows)) {
if ($action === 'move_up' && $index > 0) {
// Step 3: Swap post_order values for moving up
$swap_index = $index - 1;
} elseif ($action === 'move_down' && $index < count($rows) - 1) {
// Step 3: Swap post_order values for moving down
$swap_index = $index + 1;
} else {
wp_send_json_error('Invalid move');
return;
}

// Step 4: Update the database
$wpdb->query("START TRANSACTION");

$wpdb->update(
$table_name,
array('post_order' => $rows[$swap_index]->post_order),
array('id' => $id)
);

$wpdb->update(
$table_name,
array('post_order' => $rows[$index]->post_order),
array('id' => $rows[$swap_index]->id)
);

$wpdb->query("COMMIT");

wp_send_json_success('Row moved successfully');
} else {
wp_send_json_error('Invalid ID or index');
}
}
add_action('wp_ajax_move_up', 'move_row');
add_action('wp_ajax_move_down', 'move_row');

register_activation_hook(__FILE__, 'linkedin_posts_slider_create_table');
register_activation_hook(__FILE__, 'linkedin_posts_slider_activate');

```

the old php code for the widget registration :

```
<?php

// Check if accessed directly and exit
if (!defined('ABSPATH')) {
exit;
}

/**
 * Elementor Slider Widget.
 *
 * @since 1.0.0
 */
class Elementor_Slider_Widget extends \Elementor\Widget_Base
{

/**
* Constructor
*/
public function __construct($data = [], $args = null)
{

// Call parent constructor
parent::__construct($data, $args);

// Enqueue styles
wp_register_style('slider-style', plugins_url('../public/styles.css', __FILE__));
wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));

// Enqueue scripts
wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);
wp_enqueue_script('swiper-script');

wp_register_script('slider-script', plugins_url('../public/script.js', __FILE__), ['jquery', 'swiper-script'], false, true);
wp_localize_script('slider-script', 'ajax_object', [
'ajax_url' => admin_url('admin-ajax.php')
]);

// Enqueue styles
wp_enqueue_style('slider-style');

// Get custom settings
$settings = array(
'section-company-color' => get_option('section-company-color'),
'section-company-font-size' => get_option('section-company-font-size'),
'section-company-font-family' => get_option('section-company-font-family'),
'section-company-line-height' => get_option('section-company-line-height'),
'section-author-date-color' => get_option('section-author-date-color'),
'section-author-date-font-size' => get_option('section-author-date-font-size'),
'section-author-date-font-family' => get_option('section-author-date-font-family'),
'section-author-date-font-weight' => get_option('section-author-date-font-weight'),
'section-author-date-line-height' => get_option('section-author-date-line-height'),
'section-body-color' => get_option('section-body-color'),
'section-body-font-size' => get_option('section-body-font-size'),
'section-body-font-family' => get_option('section-body-font-family'),
'section-body-webkit-line-clamp' => get_option('section-body-webkit-line-clamp'),
'section-interactions-color' => get_option('section-interactions-color'),
'section-interactions-font-size' => get_option('section-interactions-font-size'),
'section-interactions-font-family' => get_option('section-interactions-font-family'),
'section-interactions-font-weight' => get_option('section-interactions-font-weight'),
'section-interactions-line-height' => get_option('section-interactions-line-height'),
);

// Add custom CSS
$custom_css = "
// Company Name
.section-company {
color: {$settings['section-company-color']};
font-size: {$settings['section-company-font-size']}px;
font-family: {$settings['section-company-font-family']};
line-height: {$settings['section-company-line-height']}px;
}

// Author and Date
.section-author-date {
color: {$settings['section-author-date-color']};
font-size: {$settings['section-author-date-font-size']}px;
font-family: {$settings['section-author-date-font-family']};
font-weight: {$settings['section-author-date-font-weight']};
line-height: {$settings['section-author-date-line-height']}px;
}

// Post Text
.section-body {
color: {$settings['section-body-color']};
font-size: {$settings['section-body-font-size']}px;
font-family: {$settings['section-body-font-family']};
-webkit-line-clamp: {$settings['section-body-webkit-line-clamp']};
}

// Interactions
.section-interactions {
color: {$settings['section-interactions-color']};
font-size: {$settings['section-interactions-font-size']}px;
font-family: {$settings['section-interactions-font-family']};
font-weight: {$settings['section-interactions-font-weight']};
line-height: {$settings['section-interactions-line-height']}px;
}
";

wp_add_inline_style('slider-style', $custom_css);
}

/**
* Get widget name
*/
public function get_name()
{
return 'Linkedin Slider';
}

/**
* Get widget title
*/
public function get_title()
{
return __('Linkedin Slider', 'linkedin-slider');
}

/**
* Get widget icon
*/
public function get_icon()
{
return 'eicon-slider-album';
}

/**
* Get widget categories
*/
public function get_categories()
{
return ['general'];
}

/**
* Register widget controls
*/
protected function register_controls()
{

// Custom CSS
$this->start_controls_section(
'section_custom_css',
[
'label' => __('Custom CSS', 'linkedin-slider'),
'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
]
);

$this->add_control(
'custom_css',
[
'label' => __('Custom CSS', 'linkedin-slider'),
'type' => \Elementor\Controls_Manager::CODE,
'language' => 'css',
'rows' => 10,
'default' => '',
'description' => __('Add custom CSS here', 'linkedin-slider'),
]
);

$this->end_controls_section();
}

/**
* Render widget output
*/
protected function render()
{
wp_enqueue_style('swiper-style');
wp_enqueue_script('swiper-script');
wp_enqueue_script('slider-script');
wp_enqueue_style('slider-style');



?>

<div class="swiper">
<div class="swiper-wrapper">
<div class="li-placeholder swiper-slide"></div>
</div>
<!-- Add Arrows -->
<div class="next-right-arrow"><button type="button" class="slick-next"><svg fill="#000000" height="35px" width="35px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
<g>
<g>
<path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M335.083,271.083L228.416,377.749c-4.16,4.16-9.621,6.251-15.083,6.251c-5.461,0-10.923-2.091-15.083-6.251c-8.341-8.341-8.341-21.824,0-30.165L289.835,256l-91.584-91.584c-8.341-8.341-8.341-21.824,0-30.165s21.824-8.341,30.165,0l106.667,106.667C343.424,249.259,343.424,262.741,335.083,271.083z" />
</g>
</g>
</svg></button></div>
<div class="pre-left-arrow"><button type="button" class="slick-prev"><svg fill="#000000" height="35px" width="35px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
<g>
<g>
<path d="M256,0C114.837,0,0,114.837,0,256s114.837,256,256,256s256-114.837,256-256S397.163,0,256,0z M313.749,347.584c8.341,8.341,8.341,21.824,0,30.165c-4.16,4.16-9.621,6.251-15.083,6.251c-5.461,0-10.923-2.091-15.083-6.251L176.917,271.083c-8.341-8.341-8.341-21.824,0-30.165l106.667-106.667c8.341-8.341,21.824-8.341,30.165,0s8.341,21.824,0,30.165L222.165,256L313.749,347.584z" />
</g>
</g>
</svg></button></div>
</div>

<?php
}
}

```

## The current wiget code :

```

<?php

/**
 * Plugin Name: Linkedin Posts Slider Widget for Elementor
 * Description: A custom Elementor widget to display a slider of LinkedIn posts.
 * Author: Omar Nagy
 * Version: 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
exit;
}

/**
 * Register the LinkedIn Posts Slider widget
 */
function register_linkedin_posts_slider_widget()
{
// Check if Elementor is installed and activated
if (class_exists('Elementor\Widget_Base')) {
require_once plugin_dir_path(__FILE__) . 'slider-widget.php';
}
}
add_action('elementor/widgets/widgets_registered', 'register_linkedin_posts_slider_widget');

/**
 * Elementor LinkedIn Posts Slider Widget.
 */
class Elementor_Linkedin_Posts_Slider_Widget extends \Elementor\Widget_Base
{

/**
 * Get widget name
 */
public function get_name()
{
// Unique widget name
return 'linkedin_posts_slider';
}

/**
 * Get widget title
 */
public function get_title()
{
// Widget title displayed in Elementor
return __('LinkedIn Posts Slider', 'linkedin-posts-slider');
}

/**
 * Get widget icon
 */
public function get_icon()
{
// Icon for the widget
return 'eicon-slider-album';
}

/**
 * Widget category
 */
public function get_categories()
{
// Category the widget belongs to in Elementor panel
return ['general'];
}

/**
 * Register widget controls
 */
protected function register_controls()
{
// Start controls for Company Name
$this->start_controls_section(
'section_company',
[
'label' => __('Company Name', 'linkedin-posts-slider'),
'tab' => \Elementor\Controls_Manager::TAB_STYLE,
]
);

$this->add_group_control(
\Elementor\Group_Control_Typography::get_type(),
[
'name' => 'company_typography',
'label' => __('Typography', 'linkedin-posts-slider'),
'selector' => '{{WRAPPER}} .section-company',
]
);

$this->end_controls_section();

// Start controls for Author and Date
$this->start_controls_section(
'section_author_date',
[
'label' => __('Author and Date', 'linkedin-posts-slider'),
'tab' => \Elementor\Controls_Manager::TAB_STYLE,
]
);

$this->add_group_control(
\Elementor\Group_Control_Typography::get_type(),
[
'name' => 'author_date_typography',
'label' => __('Typography', 'linkedin-posts-slider'),
'selector' => '{{WRAPPER}} .section-author-date',
]
);

$this->end_controls_section();

// Start controls for Interactions
$this->start_controls_section(
'section_interactions',
[
'label' => __('Interactions', 'linkedin-posts-slider'),
'tab' => \Elementor\Controls_Manager::TAB_STYLE,
]
);

$this->add_group_control(
\Elementor\Group_Control_Typography::get_type(),
[
'name' => 'interactions_typography',
'label' => __('Typography', 'linkedin-posts-slider'),
'selector' => '{{WRAPPER}} .section-interactions',
]
);

$this->end_controls_section();

// Start controls for Copy
$this->start_controls_section(
'section_copy',
[
'label' => __('Copy', 'linkedin-posts-slider'),
'tab' => \Elementor\Controls_Manager::TAB_STYLE,
]
);

$this->add_group_control(
\Elementor\Group_Control_Typography::get_type(),
[
'name' => 'copy_typography',
'label' => __('Typography', 'linkedin-posts-slider'),
'selector' => '{{WRAPPER}} .section-body',
]
);

$this->add_control(
'number_of_lines',
[
'label' => __('Number of Lines', 'linkedin-posts-slider'),
'type' => \Elementor\Controls_Manager::NUMBER,
'min' => 1,
'max' => 10,
'step' => 1,
'default' => 3,
'selectors' => [
'{{WRAPPER}} .section-body' => '-webkit-line-clamp: {{VALUE}};',
],
]
);

$this->end_controls_section();

// Start controls for Custom CSS
$this->start_controls_section(
'section_custom_css',
[
'label' => __('Custom CSS', 'linkedin-posts-slider'),
'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
]
);

$this->add_control(
'custom_css_code',
[
'label' => __('Custom CSS', 'linkedin-posts-slider'),
'type' => \Elementor\Controls_Manager::CODE,
'language' => 'css',
'rows' => 20,
'description' => __('Add your custom CSS code here', 'linkedin-posts-slider'),
]
);

$this->end_controls_section();
}


/**
 * Render widget output on the frontend
 */
protected function render()
{
// Enqueue SwiperJS styles and scripts
wp_enqueue_style('swiper-style');
wp_enqueue_script('swiper-script');

// Get custom CSS from widget settings and apply it
$settings = $this->get_settings_for_display();
if (!empty($settings['custom_css_code'])) {
$this->add_inline_style($settings['custom_css_code']);
}

// Slider HTML structure
?>
<div class="swiper">
<div class="swiper-wrapper">
<!-- Slides will be injected here via AJAX -->
</div>
<!-- Add Arrows -->
<div class="next-right-arrow swiper-button-next"></div>
<div class="pre-left-arrow swiper-button-prev"></div>
</div>
<script>
jQuery(document).ready(function($) {
var swiper = new Swiper('.swiper', {
slidesPerView: 1,
spaceBetween: 10,
navigation: {
nextEl: '.swiper-button-next',
prevEl: '.swiper-button-prev',
},
breakpoints: {
// Breakpoints in Swiper are used to change slider's configuration (like slidesPerView, spaceBetween) dynamically on window resize event.
// when window width is >= 320px
480: {
slidesPerView: 1, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
},
// when window width is >= 480px
768: {
slidesPerView: 2, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
},
// when window width is >= 640px
1024: {
slidesPerView: 3, // Number of slides per view (slides visible at the same time on slider's container).
spaceBetween: 10 // Distance between slides in px.
}
}
// Other Swiper options can be added here
});

// AJAX call to fetch LinkedIn posts
$.ajax({
url: ajax_object.ajax_url,
type: 'POST',
data: {
action: 'get_linkedin_posts'
},
success: function(response) {
if (response.success) {
response.data.forEach(function(post) {
// Generate the HTML for each slide
var slideContent = '<div class="swiper-slide">' +
'<div class="li-icon-white"> ... </div>' + // Add LinkedIn icon HTML
'<div class="img-container">' + generateImagesHtml(post.images) + '</div>' +
'<div class="info-container">' +
'<div class="li-author-img" style="background-image: url(\'' + post.profilePicture + '\')"></div>' +
'<div class="section-company">' + post.author + '</div>' +
'<div class="section-author-date">@' + post.username + ' . ' + post.age + ' ago</div>' +
'<p class="section-body">' + post.copy + '</p>' +
'<div class="section-interactions"> ... </div>' + // Add interactions HTML
'</div>' +
'</div>';

swiper.appendSlide(slideContent);
});
}
}
});

// Function to generate the images HTML
function generateImagesHtml(images) {
var imagesHtml = '';
if (images.length === 1) {
imagesHtml = '<div class="li-single-img" style="background-image: url(\'' + images[0] + '\')"></div>';
} else if (images.length === 2) {
imagesHtml = images.map(function(img) {
return '<div class="li-img-two" style="background-image: url(\'' + img + '\')"></div>';
}).join('');
} else if (images.length >= 3) {
imagesHtml = '<div class="li-img-three-main" style="background-image: url(\'' + images[0] + '\')"></div>' +
'<div class="li-img-three-sec-container">' +
images.slice(1).map(function(img) {
return '<div class="li-img-three-sec" style="background-image: url(\'' + img + '\')"></div>';
}).join('') +
'</div>';
}
return imagesHtml;
}
});
</script>
<?php
}


/**
 * Render widget output in the editor
 */
protected function _content_template()
{
// Render widget output in the Elementor editor
// This is similar to the render() method but for backend
}
}


```

## Request:

now the \_content_template() function including the code replacing the functionality previously included in the js file given and return the full line by line code of the function as done in the render function and adding instant reactivity reflecting the changes instantly when adjusted in the editor
