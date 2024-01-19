<?php


// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}




/**
 * Elementor LinkedIn Posts Slider Widget.
 */
class Elementor_Linkedin_Posts_Slider_Widget extends \Elementor\Widget_Base
{


  public function get_script_depends()
  {
    return ['swiper-script'];
  }

  public function get_style_depends()
  {
    return ['swiper-style', 'linkedin-slider-style'];
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
    //wp_enqueue_style('swiper-style');
    //wp_enqueue_script('swiper-script');
    //wp_enqueue_style('linkedin-slider-style');

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
          effect: 'coverflow',
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
    // Enqueue SwiperJS styles and scripts
    //wp_enqueue_style('swiper-style');
    //wp_enqueue_script('swiper-script');
    //wp_enqueue_style('linkedin-slider-style');

    // Get custom CSS from widget settings and apply it
    //$settings = $this->get_settings_for_display();
    //if (!empty($settings['custom_css_code'])) {
    //  $this->add_inline_style($settings['custom_css_code']);
    //}
  ?>
    <div class="swiper">
      <div class="swiper-wrapper">
        <!-- Static test slide -->
        <div class="swiper-slide">
          <div class="li-icon-white">Icon placeholder</div>
          <div class="img-container">
            <div class="li-img" style="background-image: url('https://example.com/image.jpg')"></div>
          </div>
          <div class="info-container">
            <div class="li-author-img" style="background-image: url('https://example.com/profile.jpg')"></div>
            <div class="section-company">Test Author</div>
            <div class="section-author-date">@testuser . 1mo ago</div>
            <p class="section-body">Sample post content...</p>
            <div class="section-interactions">100 likes • 10 comments</div>
          </div>
        </div>
        <# var posts=[{ profilePicture: 'https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&v=beta&t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA' , author: 'Alpine Laser' , username: 'alpine-laser' , age: '1mo' , copy: '* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management \n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com ' , images: [ 'https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY' ], reactions: '116' , comments: '8 comments' }, { profilePicture: 'https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&v=beta&t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA' , author: 'Alpine Laser' , username: 'alpine-laser' , age: '1mo' , copy: '* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management \n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com ' , images: [ 'https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY' ], reactions: '116' , comments: '8 comments' }, { profilePicture: 'https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&v=beta&t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA' , author: 'Alpine Laser' , username: 'alpine-laser' , age: '1mo' , copy: '* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management \n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com ' , images: [ 'https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY' ], reactions: '116' , comments: '8 comments' }, { profilePicture: 'https://media.licdn.com/dms/image/D560BAQFaqoyrA4ri6A/company-logo_100_100/0/1691067153061/alpine_laser_logo?e=1706140800&v=beta&t=MnwqT5MFRX2U6DpzGpU7PNhCRnkbTrb7ccnKfbSIluA' , author: 'Alpine Laser' , username: 'alpine-laser' , age: '1mo' , copy: '* Femtosecond Workstation Spotlight *\n\n- Extremely compact integration of an Ultra-Short Pulse, Femtosecond laser source\n- Hollow Core Fiber Delivery with Active Beam Management \n- Laser control module and laser head unit mounted within the machine base\n- Available in both programmable 2 and 4 axis configurations\n\nInquire to learn more at sales@alpinelaser.com ' , images: [ 'https://media.licdn.com/dms/image/D5622AQHrz8D5-4lTDw/feedshare-shrink_800/0/1695314437373?e=1700697600&v=beta&t=slwjjR_eHPJPHLveIXf24XLpNRp32hy41phrEB_pMyY' ], reactions: '116' , comments: '8 comments' } ]; #>

          <# _.each(posts, function(post) { #>
            <div class="swiper-slide">
              <div class="li-icon-white"> ... </div>
              <div class="img-container">
                <# _.each(post.images, function(image) { #>
                  <div class="li-img" style="background-image: url('{{ image }}')"></div>
                  <# }); #>
              </div>
              <div class="info-container">
                <div class="li-author-img" style="background-image: url('{{ post.profilePicture }}')"></div>
                <div class="section-company">{{ post.author }}</div>
                <div class="section-author-date">@{{ post.username }} . {{ post.age }} ago</div>
                <p class="section-body">{{ post.copy }}</p>
                <div class="section-interactions">{{ post.reactions }} • {{ post.comments }}</div>
              </div>
            </div>
            <# }); #>
      </div>
      <!-- Add Arrows -->
      <div class="next-right-arrow swiper-button-next"></div>
      <div class="pre-left-arrow swiper-button-prev"></div>
    </div>

    <script>
      jQuery(document).ready(function($) {
        // Initialize Swiper for the editor preview
        var swiper = new Swiper('.swiper', {
          effect: 'coverflow',
          coverflowEffect: {
            rotate: 30,
            slideShadows: false,
          },
          slidesPerView: 1,
          spaceBetween: 10,
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          // Include the same breakpoint settings as in the 'render' function
          breakpoints: {
            480: {
              slidesPerView: 1,
              spaceBetween: 10
            },
            768: {
              slidesPerView: 2,
              spaceBetween: 10
            },
            1024: {
              slidesPerView: 3,
              spaceBetween: 10
            }
          }
        });
      });
    </script>
<?php
  }
}
