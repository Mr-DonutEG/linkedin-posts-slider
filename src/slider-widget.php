<?php


// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Register the LinkedIn Posts Slider widget
 */
function register_linkedin_posts_slider_widget()
{
  if (did_action('elementor/loaded')) {
    require_once plugin_dir_path(__FILE__) . 'src/slider-widget.php';
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor_Linkedin_Posts_Slider_Widget());
  }
}
add_action('elementor/widgets/widgets_registered', 'register_linkedin_posts_slider_widget');


/**
 * Elementor LinkedIn Posts Slider Widget.
 */
class Elementor_Linkedin_Posts_Slider_Widget extends \Elementor\Widget_Base
{
  /**
   * Constructor
   */
  public function __construct($data = [], $args = null)
  {
    parent::__construct($data, $args);

    wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));
    wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);

    wp_register_style('linkedin-slider-style', plugins_url('../public/styles.css', __FILE__));
   
  }

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
    wp_enqueue_style('linkedin-slider-style');

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
  wp_enqueue_style('swiper-style');
  wp_enqueue_script('swiper-script');
  wp_enqueue_style('linkedin-slider-style');
  ?>
    <div class="swiper">
      <div class="swiper-wrapper">
        <# var posts=[ { "urn" : "urn:li:activity:7110664133217288192" , "post_order" : "1" , "profilePicture" : "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA" , "author" : "Alpine Laser" , "username" : "alpine-laser" , "age" : "1mo •" , "copy" : "Announcing the MediSCAN Pro - Alpine Laser's latest high performance laser processing workstation optimized for medical device manufacturing!\n\nThe configuration shown here features programmable XYZ motion coupled with a Scanlab precSYS 5-axis #micromachining galvo and a TRUMPF 2230 ultra short pulsed 515nm green #laser source and coaxial vision.\n\nThis machine was designed to process very fine features and complex holes in hard to machine polymer materials. (Shown are 0.25mm holes in a 1mm Pellethane tube)\n\nOther configurations of this workstation can be optimized for flat sheet cutting, traditional 2D galvo applications, marking, complex ablation, to name a few.\n\nContact sales@alpinelaser.com for more information.\n\nSCANLAB GmbH\nTRUMPF North America\n#medicaldevicemanufacturing" , "images" : [ "https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY" , "https://media.licdn.com/dms/image/D5622AQGu92JK888ZUw/feedshare-shrink_800/0/1695314437386?e=1700697600&v=beta&t=Zf7xMoDtwBTCN905mseXz8rk77dtmfOSm08Tfh7qUUI" , "https://media.licdn.com/dms/image/D5622AQFevdEZ-d2RfQ/feedshare-shrink_800/0/1695314436856?e=1700697600&v=beta&t=5kRgmLzLb9VPGUlMPWnTGO79_n0hlqW7DhUVwQzs-zQ" , "https://media.licdn.com/dms/image/D5622AQGfdzbosfaiPw/feedshare-shrink_800/0/1695314437494?e=1700697600&v=beta&t=og3iW9NjIz2VSbFj4aUi385BLsxLLuIZ2MmXvuAe4Ck" , "https://media.licdn.com/dms/image/D5622AQE9oTsaKKVG9A/feedshare-shrink_800/0/1695314437828?e=1700697600&v=beta&t=eUkg72s4keVlwaJ0QjqK5cz2Pk9LltlbcXA6wY3CizU" ], "reactions" : "116" , "comments" : "8 comments" }, { "urn" : "urn:li:activity:7117516266000498688" , "post_order" : "3" , "profilePicture" : "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA" , "author" : "Alpine Laser" , "username" : "alpine-laser" , "age" : "1w •" , "copy" : "Come see a live demo of femtosecond tube cutting today and tomorrow at MDM in booth 2803!" , "images" : [ "https://media.licdn.com/dms/image/D4E22AQHZ109l5a2sMg/feedshare-shrink_800/0/1696948113736?e=1700697600&v=beta&t=keJyTShAaigbh_J5MNMW6ZZKkM1WwZY58ajF0vkf-O4" ], "reactions" : "20" , "comments" : "1 comment" }, { "urn" : "urn:li:activity:7084633761740423169" , "post_order" : "5" , "profilePicture" : "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA" , "author" : "Alpine Laser" , "username" : "alpine-laser" , "age" : "3mo •" , "copy" : "Just completed the installation of two femtosecond laser tube cutting workstations paired with bar feeders and custom Alpine automated part extractors enabling this customer to run catheter shaft production lights out.\n\nContact the team at Alpine Laser today to see how we can help you transform your laser cutting operation.\n\nsales@alpinelaser.com" , "images" : [ "https://media.licdn.com/dms/image/D5622AQE0uiOv1X59Og/feedshare-shrink_800/0/1689108312570?e=1700697600&v=beta&t=eJ1Ntg5tN2cqRJ--r5sJcHbaLCGW60wGlbWvl5OAZH8" , "https://media.licdn.com/dms/image/D5622AQEDvNoAXKgCkA/feedshare-shrink_800/0/1689108308231?e=1700697600&v=beta&t=1soEvuOe2pQNSHGwxWPl5jPdBttmoM3T8rQm_Myxkss" , "https://media.licdn.com/dms/image/D5622AQGuLM3G0lYTmQ/feedshare-shrink_800/0/1689108310054?e=1700697600&v=beta&t=KBIg0S6fPTpsgfDzvY5jx5mWh6EEU4AoLCQPG7y_n0Q" , "https://media.licdn.com/dms/image/D5622AQEs3FWPkEZ4fg/feedshare-shrink_800/0/1689108313262?e=1700697600&v=beta&t=HoJuuTrLQWy4iZXrvMVoIv1wBgPUN1nYBk34XYSGUjA" , "https://media.licdn.com/dms/image/D5622AQGwIi2isOxGuQ/feedshare-shrink_800/0/1689108311592?e=1700697600&v=beta&t=WvJ4ZE6Lk0KpjnWv-9iAs8Ix8aRAA9DYHr3SC3zdnhY" ], "reactions" : "108" , "comments" : "5 comments" }, { "urn" : "urn:li:activity:7085263372841041920" , "post_order" : "6" , "profilePicture" : "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA" , "author" : "Alpine Laser" , "username" : "alpine-laser" , "age" : "3mo •" , "copy" : "Need cuts with no heat affected zone and very clean edges? Take a look at these sample parts cut with the Alpine Laser Medicut Pro workstation utilizing a top of the line ultra short pulse femtosecond laser from TRUMPF." , "images" : [ "https://media.licdn.com/dms/image/D4D22AQGqLOmYU5zQJQ/feedshare-shrink_800/0/1689258424335?e=1700697600&v=beta&t=uP8Ie76uxvmOw9ahFB3slq595VwceCZnTBhObQLgGkM" , "https://media.licdn.com/dms/image/D4D22AQFjeXMtn0ZgcQ/feedshare-shrink_800/0/1689258424269?e=1700697600&v=beta&t=v7XNtnlThPCVqQm4mYP_-0eKuWfLRkqwBQUMbXuzlxw" , "https://media.licdn.com/dms/image/D4D22AQECZgYGzGDO6g/feedshare-shrink_800/0/1689258424307?e=1700697600&v=beta&t=uWSERibQHlagEnUZjWzktamM9FH97kBC3qjwN82N9Rw" , "https://media.licdn.com/dms/image/D4D22AQEMYp-_RwB6hA/feedshare-shrink_800/0/1689258424267?e=1700697600&v=beta&t=2m71oyvQM6TdvYzUCyAHTVZ15j08UB2X58FNfi2TVSE" ], "reactions" : "120" , "comments" : "6 comments" }, { "urn" : "urn:li:activity:7023741456741777408" , "post_order" : "8" , "profilePicture" : "https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&amp;v=beta&amp;t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA" , "author" : "Alpine Laser" , "username" : "alpine-laser" , "age" : "9mo •" , "copy" : "* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management\n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com" , "images" : [ "https://media.licdn.com/dms/image/C5622AQG3G4m1HdBRTQ/feedshare-shrink_800/0/1674590456558?e=1700697600&v=beta&t=k5YtvgDRkv5WaSn1dHoYUCeUv0cTuOOxRMGtQvZXWSg" , "https://media.licdn.com/dms/image/C5622AQHltS4_M21yfQ/feedshare-shrink_800/0/1674590456620?e=1700697600&v=beta&t=XjGMMnhIUNUcXz6xMwiUb-T9Aq1608FNLQ-_XARboyk" , "https://media.licdn.com/dms/image/C5622AQGKlqfHEc9TVA/feedshare-shrink_800/0/1674590456761?e=1700697600&v=beta&t=sBYFSRv1aWvisfv-sTsyx5wantSgUJ5FkvQoKwwuFzc" , "https://media.licdn.com/dms/image/C5622AQEw2Fhe4KSHUA/feedshare-shrink_800/0/1674590456630?e=1700697600&v=beta&t=YyVctzHbooMWL4sntKLUFTcQSAWjYtQ_PZz0VqSUbU8" ], "reactions" : "28" , "comments" : "0 comments" } ]; posts.forEach(function(post) { var imagesHtml=generateImagesHtml(post.images); #>
          <div class="swiper-slide">
            <div class="li-icon-white">... (icon placeholder) ...</div>
            <div class="img-container">{{{ imagesHtml }}}</div>
            <div class="info-container">
              <div class="li-author-img" style="background-image: url('{{{ post.profilePicture }}}')"></div>
              <div class="section-company">{{{ post.author }}}</div>
              <div class="section-author-date">@{{{ post.username }}} . {{{ post.age }}} ago</div>
              <p class="section-body">{{{ post.copy }}}</p>
              <div class="section-interactions">
                {{{ post.reactions }}} • {{{ post.comments }}}
              </div>
            </div>
          </div>
          <# }); #>
      </div>
      <!-- Add Arrows -->
      <div class="next-right-arrow swiper-button-next"></div>
      <div class="pre-left-arrow swiper-button-prev"></div>
    </div>

    <script>
      function generateImagesHtml(images) {
        var imagesHtml = '';
        images.forEach(function(img, index) {
          imagesHtml += '<div class="li-img" style="background-image: url(\'' + img + '\')"></div>';
        });
        return imagesHtml;
      }

      jQuery(document).ready(function($) {
        // Initialize Swiper for the editor preview
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
        });
      });
    </script>
<?php
  }
}
