<?php
namespace MetSalesCountdown\Core\Admin\Classes;

use MetSalesCountdown\Helpers\Settings_Api;
use MetSalesCountdown\Plugin;
use MetSalesCountdown\Traits\Singleton;

defined('ABSPATH') || exit;

class Side_Menu
{
    use Singleton;

    private $settings_api;

    public function __construct()
    {
        $this->settings_api = Settings_Api::instance();

        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_init', [$this, 'admin_init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_custom_script']);
    }

    /**
     * Set section and fileds for setings API
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_init()
    {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }

    /**
     * Get settings sections
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings_sections()
    {
        $sections = array(
            array(
                'id'    => 'metsalescountdown_general',
                'title' => __('General Settings', 'met-sales-countdown'),
            ),
            array(
                'id'    => 'metsalescountdown_campaign',
                'title' => __('Campaign Settings', 'met-sales-countdown'),
            ),
            array(
                'id'    => 'metsalescountdown_stock_status_bar',
                'title' => __('Stock Status Bar', 'met-sales-countdown'),
            ),
        );
        return $sections;
    }

    /**
     * Get settings fields
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings_fields()
    {

        $need_pro = true;

        if (class_exists('MetSalesCountdownPro')) {
            $need_pro = false;
        }

        $settings_fields = array(
            'metsalescountdown_general'          => array(
                array(
                    'name'              => 'met_sale_countdown_msg',
                    'size'              => 'met-sale-countdown-msg regular',
                    'label'             => __('Message', 'met-sales-countdown'),
                    'desc'              => __('This message will show as countdown timer title.', 'met-sales-countdown'),
                    'placeholder'       => __('Enter your message', 'met-sales-countdown'),
                    'type'              => 'text',
                    'default'           => 'Hurry Up!!! Offer ends in...',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'met_sale_countdown_time_format',
                    'label'             => __('Format', 'met-sales-countdown'),
                    'size'              => 'regular-text met-sale-countdown-format',
                    'desc'              => __('Time/Date format.', 'met-sales-countdown'),
                    'type'              => 'select',
                    'default'           => 'tillMonth',
                    'options'           => array(
                        'tillMonth' => __('MM | DD | ( h : m : s )', 'met-sales-countdown'),
                        'tillDay'   => __('DD | ( h : m : s )', 'met-sales-countdown'),
                        'tillHour'  => __('h : m : s', 'met-sales-countdown'),
                        'tillMin'   => __('m : s', 'met-sales-countdown'),
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'met_sale_countdown_separator',
                    'label'             => __('Select Separator', 'met-sales-countdown'),
                    'desc'              => __('Separator style', 'met-sales-countdown'),
                    'size'              => 'met-sale-countdown-time-separator regular-text',
                    'type'              => 'select',
                    'default'           => 'no',
                    'options'           => array(
                        'no' => __('No Separator', 'met-sales-countdown'),
                        ':'  => __('Colon(:)', 'met-sales-countdown'),
                        '/'  => __('Slash(/)', 'met-sales-countdown'),
                        '-'  => __('Dash(-)', 'met-sales-countdown'),
                        '|'  => __('Bar(|)', 'met-sales-countdown'),
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'display_loop_product',
                    'label'             => __('Display in Loop Product', 'met-sales-countdown'),
                    'type'              => 'radio',
                    'default'           => '0',
                    'options'           => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'display_single_product',
                    'label'             => __('Display in Single Product', 'met-sales-countdown'),
                    'type'              => 'radio',
                    'default'           => '0',
                    'options'           => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'display_single_product_position',
                    'label'             => __('Countdown Position in Single Product', 'met-sales-countdown'),
                    'desc'              => __('Where you want to show the countdown.', 'met-sales-countdown'),
                    'type'              => 'select',
                    'size'              => 'regular-text',
                    'default'           => 'before_add_to_cart',
                    'options'           => array(
                        'before_add_to_cart' => __('Before Add To Cart', 'met-sales-countdown'),
                        'after_add_to_cart'  => __('After Add To Cart', 'met-sales-countdown'),
                        'before_price'       => __('Before Price', 'met-sales-countdown'),
                        'after_price'        => __('After Price', 'met-sales-countdown'),
                        'before_sale_flash'  => __('Before Sale Flash', 'met-sales-countdown'),
                        'after_sale_flash'   => __('After Sale Flash', 'met-sales-countdown'),
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
            'metsalescountdown_campaign'         => array(
                array(
                    'name'              => 'display_campaign_product_page',
                    'label'             => __('Display in Loop Product', 'met-sales-countdown'),
                    'type'              => 'radio',
                    'default'           => '0',
                    'options'           => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'display_campaign_single_product',
                    'label'             => __('Display in Single Product', 'met-sales-countdown'),
                    'type'              => 'radio',
                    'default'           => '0',
                    'options'           => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'campaign_title',
                    'label'             => __('Campaign Title', 'met-sales-countdown'),
                    'desc'              => __('This title will show as campaign title.', 'met-sales-countdown'),
                    'placeholder'       => __('Enter your title', 'met-sales-countdown'),
                    'default'           => 'My Awesome Campaign Title!',
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'campaign_start_time',
                    'label'             => __('Select Campaign Start Time', 'met-sales-countdown'),
                    'desc'              => __('The campaign will start from this time.', 'met-sales-countdown'),
                    'type'              => 'datetime',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'campaign_end_time',
                    'label'             => __('Select Campaign End Time', 'met-sales-countdown'),
                    'desc'              => __('The campaign will end at this time.', 'met-sales-countdown'),
                    'type'              => 'datetime',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'campaign_category',
                    'label'             => __('Select Product Category', 'met-sales-countdown'),
                    'size'              => 'regular',
                    'desc'              => __('In the category of the products will show the campaign.', 'met-sales-countdown'),
                    'type'              => 'select2',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'campaign_discount_amount',
                    'label'             => __('Campaign Discount Amount', 'met-sales-countdown'),
                    'desc'              => __('Enter the discount amount.', 'met-sales-countdown'),
                    'size'              => 'regular-text regular',
                    'placeholder'       => __('Enter your amount', 'met-sales-countdown'),
                    'default'           => '10',
                    'type'              => 'number',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'campaign_discount_type',
                    'label'             => __('Discount Type', 'met-sales-countdown'),
                    'desc'              => __('Select the type of discount.', 'met-sales-countdown'),
                    'type'              => 'select',
                    'size'              => 'regular-text',
                    'default'           => 'precent',
                    'options'           => array(
                        'percent' => __('Percentage', 'met-sales-countdown'),
                        'fixed'   => __('Fixed', 'met-sales-countdown'),
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'  => 'metsalescountdown_campaign_shortcode',
                    'label' => __('Shortcode', 'met-sales-countdown'),
                    'desc'  => __('This shortcode will show the campaign timer.', 'met-sales-countdown'),
                    'type'  => $need_pro ? 'needpro' : 'custom_metsaleshortcode',
                ),
            ),
            'metsalescountdown_stock_status_bar' => array(
                array(
                    'name'              => 'display_stock_status_bar',
                    'label'             => __('Display Stock Status Bar', 'met-sales-countdown'),
                    'type'              => 'radio',
                    'default'           => '0',
                    'options'           => array(
                        '1' => 'Yes',
                        '0' => 'No',
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'stock_status_bar_message',
                    'label'             => __('Stock Status Bar Message', 'met-sales-countdown'),
                    'desc'              => __('The {sold_item} and {remain_item} will be replaced with actual sold items and remaining items.', 'met-sales-countdown'),
                    'placeholder'       => __('Enter your message', 'met-sales-countdown'),
                    'default'           => __('Sold {sold_item} and only {remain_item} items remaining!', 'met-sales-countdown'),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'stock_status_bar_position',
                    'label'             => __('Stock Status Bar Position', 'met-sales-countdown'),
                    'desc'              => __('Where you want to show the stock status bar.', 'met-sales-countdown'),
                    'type'              => 'select',
                    'size'              => 'regular-text',
                    'default'           => 'before_add_to_cart',
                    'options'           => array(
                        'before_add_to_cart' => __('Before Add To Cart', 'met-sales-countdown'),
                        'after_add_to_cart'  => __('After Add To Cart', 'met-sales-countdown'),
                        'before_price'       => __('Before Price', 'met-sales-countdown'),
                        'after_price'        => __('After Price', 'met-sales-countdown'),
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name'              => 'stock_status_bar_style',
                    'label'             => __('Stock Status Bar Style', 'met-sales-countdown'),
                    'type'              => 'radio_image',
                    'default'           => 'style-1',
                    'options'           => array(
                        'style-1' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_01.png',
                            'package'   => 'free',
                        ),
                        'style-2' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_02.png',
                            'package'   => 'pro',
                        ),
                        'style-3' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_03.png',
                            'package'   => 'pro',
                        ),
                        'style-4' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_04.png',
                            'package'   => 'pro',
                        ),
                        'style-5' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_05.png',
                            'package'   => 'pro',
                        ),
                        'style-6' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_06.png',
                            'package'   => 'pro',
                        ),
                        'style-7' => array(
                            'image_url' => \MetSalesCountdown\Plugin::plugin_url() . 'public/assets/img/style_07.png',
                            'package'   => 'pro',
                        ),
                    ),
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        );

        return $settings_fields;
    }

    public function admin_menu()
    {
        add_menu_page(
            esc_html__("MetSales Timer", 'met-sales-countdown'),
            esc_html__("MetSales Timer", 'met-sales-countdown'),
            'manage_options',
            'met-sales-countdown',
            [$this, 'countdown_timer_overview'],
            'dashicons-clock',
            60
        );
    }

    public function countdown_timer_overview()
    {

        $general_settings  = get_option('metsalescountdown_general');
        $campaign_settings = get_option('metsalescountdown_campaign');

        include Plugin::plugin_dir() . ('/core/admin/views/settings.php');

        echo wp_kses('<div class="wrap">', \MetSalesCountdown\Helpers\Utils::get_kses_array());

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo wp_kses('</div>', \MetSalesCountdown\Helpers\Utils::get_kses_array());
    }

    public function enqueue_custom_script()
    {

        wp_enqueue_script('metsalescountdown-app', Plugin::plugin_url() . 'public/assets/js/app.js', array('jquery'), Plugin::version(), true);

        $general_settings  = get_option('metsalescountdown_general');
        $campaign_settings = get_option('metsalescountdown_campaign');

        // Localize the script with metsales settings data for frontend
        $localized_data = array(
            'message'                 => isset($general_settings['met_sale_countdown_msg']) ? $general_settings['met_sale_countdown_msg'] : '',
            'defaultMessage'          => esc_html__('Hurry Up!!! Offer ends in...', 'met-sales-countdown'),
            'countdown_format'        => isset($general_settings['met_sale_countdown_time_format']) ? $general_settings['met_sale_countdown_time_format'] : '',
            'time_separator'          => isset($general_settings['met_sale_countdown_separator']) ? $general_settings['met_sale_countdown_separator'] : '',
            'loop_countdown_toggle'   => isset($general_settings['display_loop_product']) ? $general_settings['display_loop_product'] : '',
            'single_countdown_toggle' => isset($general_settings['display_single_product']) ? $general_settings['display_single_product'] : '',
            'current_time'            => strtotime(gmdate('Y-m-d H:i:s')),
            'api_url'                 => get_rest_url('', 'metsalescountdown/v1/'),
            'campaign_title'          => isset($campaign_settings['campaign_title']) ? $campaign_settings['campaign_title'] : '',
            'campaign_start'          => isset($campaign_settings['campaign_start_time']) ? $campaign_settings['campaign_start_time'] : '',
            'campaign_end'            => isset($campaign_settings['campaign_end_time']) ? $campaign_settings['campaign_end_time'] : '',
            'campaign_category'       => isset($campaign_settings['campaign_category']) ? $campaign_settings['campaign_category'] : '',
            'display_on_product_page' => isset($campaign_settings['display_campaign_product_page']) ? $campaign_settings['display_campaign_product_page'] : '',
        );

        wp_localize_script('metsalescountdown-app', 'countdownData', $localized_data);
    }
}