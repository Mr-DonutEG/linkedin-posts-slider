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
          loop: true,
          effect: 'coverflow',
          coverflowEffect: {
            rotate: 60,
            slideShadows: true,
            depth: 20,
            modifier: 0.5,
            scale: 0.85,
            stretch: 0
          },
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
                  '<div class="li-icon-white"> <svg style="width: 30px; height: 30px; overflow: visible; fill: rgb(255, 255, 255);" viewBox="0 0 448 512"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg> </div>' + // Add LinkedIn icon HTML
                  '<div class="img-container">' + generateImagesHtml(post.images) + '</div>' +
                  '<div class="info-container">' +
                  '<div class="li-author-img" style="background-image: url(\'' + post.profilePicture + '\')"></div>' +
                  '<div class="section-company">' + post.author + '</div>' +
                  '<div class="section-author-date">@' + post.username + ' . ' + post.age + ' ago</div>' +
                  '<p class="section-body">' + post.copy + '</p>' +
                  `<div class="section-interactions"> 
                  <span><svg style="width: 24px; height: 24px; overflow: visible; fill: rgb(0, 122, 255);" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path><path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path></svg></span>
                                <span class="li-post-reactions">${post.reactions} . </span>
                                <span class="li-post-comments">${post.comments}</span>
                  </div>`

                  + // Add interactions HTML
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
              <div class="li-icon-white"> <svg style="width: 30px; height: 30px; overflow: visible; fill: rgb(255, 255, 255);" viewBox="0 0 448 512">
                  <path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path>
                </svg> </div>
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
                <div class="section-interactions"><span><svg style="width: 24px; height: 24px; overflow: visible; fill: rgb(0, 122, 255);" viewBox="0 0 24 24">
                      <path fill="none" d="M0 0h24v24H0z"></path>
                      <path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path>
                      <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                      <path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path>
                    </svg></span>
                  <span class="li-post-reactions">{{ post.reactions }} • </span>
                  <span class="li-post-comments">{{ post.comments }}</span>
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
      jQuery(document).ready(function($) {
        // Initialize Swiper for the editor preview
        var swiper = new Swiper('.swiper', {
          loop: true,
          effect: 'coverflow',
          coverflowEffect: {
            rotate: 60,
            slideShadows: true,
            depth: 20,
            modifier: 0.5,
            scale: 0.85,
            stretch: 0
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
