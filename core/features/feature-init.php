<?php

namespace MetSalesCountdown\Core\Features;

use MetSalesCountdown\Core\Admin\Classes\Settings;
use MetSalesCountdown\Core\Features\Single_Product\Single_Product;
use MetSalesCountdown\Plugin;

defined('ABSPATH') || exit;

class FeatureInit
{
    protected $countdown_settings;
    protected $campaign_data;
    protected $single_product;

    // A variable to keep track if the countdown is shown
    protected $countdown_shown = false;

    public function __construct()
    {
        $this->set_countdown_settings();
        if (isset($this->countdown_settings['display_single_product'])&& $this->countdown_settings['display_single_product'] == 1) {
            add_action('woocommerce_before_add_to_cart_form', [$this, 'show_countdown_before_add_to_cart_form']);
            add_action('woocommerce_after_add_to_cart_button', [$this, 'show_countdown_after_add_to_cart_button']);
            add_action('woocommerce_before_template_part', array($this, 'countdown_before_template'));
            add_action('woocommerce_after_template_part', array($this, 'countdown_after_template'));
            add_action('wp', [$this, 'conditional_enqueue']);
        }

    }

    public function conditional_enqueue()
    {
        if (class_exists('WooCommerce') && wp_is_block_theme() && is_product()) {
            // Ensure WooCommerce product is available
            $product = wc_get_product(get_the_ID()); // Make sure to pass the product ID
        
            if ($product && $product->is_type('simple')) {
                add_action('wp_enqueue_scripts', [$this, 'enqueue_countdown_script']);
            }
        }
    }

    private function set_countdown_settings()
    {
        $this->countdown_settings = Settings::instance()->get_general_settings();
        $this->campaign_data      = Settings::instance()->get_campaign_settings();
        $this->single_product     = new Single_Product();
    }

    public function show_countdown_before_add_to_cart_form() {

            $this->set_countdown_settings();

            if (isset($this->countdown_settings['display_single_product_position'])
                && $this->countdown_settings['display_single_product_position'] === 'before_add_to_cart') {

                echo wp_kses($this->single_product->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
            }
    }
    public function show_countdown_after_add_to_cart_button()
    {

        if (isset($this->countdown_settings['display_single_product_position'])
            && $this->countdown_settings['display_single_product_position'] === 'after_add_to_cart') {

            echo wp_kses($this->single_product->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
        }
    }

    public function countdown_before_template($template_name)
    {

        $this->set_countdown_settings();

        switch ($template_name) {
            case 'single-product/sale-flash.php':
                if (isset($this->countdown_settings['display_single_product_position'])
                    && $this->countdown_settings['display_single_product_position'] == 'before_sale_flash') {

                    echo wp_kses($this->single_product->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
                }
                break;
            case 'single-product/price.php':
                if (isset($this->countdown_settings['display_single_product_position'])
                    && $this->countdown_settings['display_single_product_position'] == 'before_price') {

                    echo wp_kses($this->single_product->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
                }
                break;
            default:
                return;
        }
    }

    public function countdown_after_template($template_name)
    {
        $this->set_countdown_settings();

        switch ($template_name) {
            case 'single-product/price.php':
                if (isset($this->countdown_settings['display_single_product_position'])
                    && $this->countdown_settings['display_single_product_position'] == 'after_price') {

                    echo wp_kses($this->single_product->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
                }
                break;
            case 'single-product/sale-flash.php':
                if (isset($this->countdown_settings['display_single_product_position'])
                    && $this->countdown_settings['display_single_product_position'] === 'after_sale_flash') {

                    echo wp_kses($this->single_product->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
                        
                }
                break;
            default:
                return;
        }
    }

    public function enqueue_countdown_script() {

        $general_settings = get_option('metsalescountdown_general');
        $campaign_settings = get_option('metsalescountdown_campaign');

        wp_enqueue_script('met-sales-price-countdown', Plugin::plugin_url() . 'public/assets/js/price-countdown.min.js', ['jquery'], Plugin::version(), true);
        wp_localize_script('met-sales-price-countdown', 'priceCountdown', [
            'countdownHTML' =>  $this->single_product->single_product(),
            'position' => $this->countdown_settings['display_single_product_position'],
            'message' => isset($general_settings['met_sale_countdown_msg']) ? $general_settings['met_sale_countdown_msg'] : '',
            'defaultMessage' => esc_html__('Hurry Up!!! Offer ends in...', 'met-sales-countdown'),
            'countdown_format' => isset($general_settings['met_sale_countdown_time_format']) ? $general_settings['met_sale_countdown_time_format'] : '',
            'time_separator' => isset($general_settings['met_sale_countdown_separator']) ? $general_settings['met_sale_countdown_separator'] : '',
            'loop_countdown_toggle' => isset($general_settings['display_loop_product']) ? $general_settings['display_loop_product'] : '',
            'single_countdown_toggle' => isset($general_settings['display_single_product']) ? $general_settings['display_single_product'] : '',
            'current_time' => strtotime(gmdate('Y-m-d H:i:s')),
            'campaign_title'          => isset($campaign_settings['campaign_title']) ? $campaign_settings['campaign_title'] : '',
            'campaign_start'          => isset($campaign_settings['campaign_start_time']) ? $campaign_settings['campaign_start_time'] : '',
            'campaign_end'            => isset($campaign_settings['campaign_end_time']) ? $campaign_settings['campaign_end_time'] : '',
            'campaign_category'       => isset($campaign_settings['campaign_category']) ? $campaign_settings['campaign_category'] : '',
            'display_on_product_page' => isset($campaign_settings['display_campaign_product_page']) ? $campaign_settings['display_campaign_product_page'] : '',
        ]);
    }
}