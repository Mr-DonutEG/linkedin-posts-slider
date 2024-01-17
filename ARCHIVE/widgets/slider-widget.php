<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Elementor Slider Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
// This class defines the Elementor Slider Widget
class Elementor_Slider_Widget extends \Elementor\Widget_Base
{
  /**
   * Constructor
   *
   * @since 1.0.0
   * @access public
   */
  // Constructor for the widget
  public function __construct($data = [], $args = null)
  {
    // Call the parent constructor
    parent::__construct($data, $args);

    wp_register_style('slider-style', plugins_url('../public/styles.css', __FILE__));
    wp_register_style('swiper-style', plugins_url('../public/swiperjs/swiper-bundle.css', __FILE__));
    wp_register_script('swiper-script', plugins_url('../public/swiperjs/swiper-bundle.js', __FILE__), ['jquery'], false, true);
    wp_enqueue_script('swiper-script');
    wp_register_script('slider-script', plugins_url('../public/script.js', __FILE__), ['jquery', 'swiper-script'], false, true);
    wp_localize_script('slider-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('slider-style'); // Add this line to enqueue the style
  }

  /**
   * Get widget name.
   *
   * Retrieve Slider widget name.
   *
   * @since 1.0.0
   * @access public
   * @return string Widget name.
   */
  public function get_name()
  {
    return 'Linkedin Slider';
  }

  /**
   * Get widget title.
   *
   * Retrieve Slider widget title.
   *
   * @since 1.0.0
   * @access public
   * @return string Widget title.
   */
  public function get_title()
  {
    return esc_html__('Linkedin Slider', 'elementor-slider-widget');
  }

  /**
   * Get widget icon.
   *
   * Retrieve Slider widget icon.
   *
   * @since 1.0.0
   * @access public
   * @return string Widget icon.
   */
  public function get_icon()
  {
    return 'eicon-slider-album';
  }

  /**
   * Get custom help URL.
   *
   * Retrieve a URL where the user can get more information about the widget.
   *
   * @since 1.0.0
   * @access public
   * @return string Widget help URL.
   */
  public function get_custom_help_url()
  {
    return 'https://developers.elementor.com/docs/widgets/';
  }

  /**
   * Get widget categories.
   *
   * Retrieve the slider of categories the slider widget belongs to.
   *
   * @since 1.0.0
   * @access public
   * @return array Widget categories.
   */
  public function get_categories()
  {
    return ['general'];
  }

  /**
   * Get widget keywords.
   *
   * Retrieve the slider of keywords the slider widget belongs to.
   *
   * @since 1.0.0
   * @access public
   * @return array Widget keywords.
   */
  public function get_keywords()
  {
    return ['slider', 'sliders', 'linkedin'];
  }

  /**
   * Register slider widget controls.
   *
   * Add input fields to allow the user to customize the widget settings.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function register_controls()
  {


    // 1h. Custom CSS box control
    $this->start_controls_section(
      'custom_css_section',
      [
        'label' => __('Custom CSS', 'elementor-slider-widget'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'custom_css',
      [
        'label' => __('Custom CSS', 'elementor-slider-widget'),
        'type' => \Elementor\Controls_Manager::CODE,
        'language' => 'css',
        'rows' => 10,
        'default' => '',
        'description' => __('Add your custom CSS code here. It will be applied to the frontend.', 'elementor-slider-widget'),
      ]
    );

    $this->end_controls_section();
  }

  /**
   * Render slider widget output on the frontend.
   *
   * Written in PHP and used to generate the final HTML.
   *
   * @since 1.0.0
   * @access protected
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



  /**
   * Render slider widget output in the editor.
   *
   * Written as a Backbone JavaScript template and used to generate the live preview.
   *
   * @since 1.0.0
   * @access protected
   */
  protected function content_template()
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
