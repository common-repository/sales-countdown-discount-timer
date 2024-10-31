<?php

namespace MetSalesCountdown\Core\Features\Stock_Progress_Bar;

use MetSalesCountdown\Core\Admin\Classes\Settings;
use MetSalesCountdown\Core\Interfaces\Features_Interface;
use MetSalesCountdown\Helpers\Utils;
use MetSalesCountdown\Plugin;

defined( 'ABSPATH' ) || exit;

class Stock_Progress_Bar implements Features_Interface {

    private $stock_status_settings;

    /**
     * Feature name
     * 
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function name(): string {
        return __('Stock Status Progress Bar', 'met-sales-countdown');
    }

    /**
     * Initialize feature
     * 
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function init(): void {

        $this->stock_status_settings = Settings::instance()->get_stock_status_bar_settings();
        
        if ( !empty( $this->stock_status_settings ) && isset($this->stock_status_settings['display_stock_status_bar']) && $this->stock_status_settings['display_stock_status_bar'] == 1 ) {

            //remove default stock html
            add_filter( 'woocommerce_get_stock_html', '__return_empty_string', 10, 2 );

            $stock_status_bar_position = isset($this->stock_status_settings['stock_status_bar_position']) ? $this->stock_status_settings['stock_status_bar_position'] : 'before_add_to_cart';

            switch ( $stock_status_bar_position ) {
                case 'before_add_to_cart':
                    add_action('woocommerce_before_add_to_cart_form', [$this, 'before_after_cart'], 9);
                    break;
                case 'after_add_to_cart':
                    add_action('woocommerce_after_add_to_cart_form', [$this, 'before_after_cart'], 9);
                    break;
                case 'before_price':
                    add_action('woocommerce_before_template_part', [$this, 'before_after_price'], 9);
                    break;
                case 'after_price':
                    add_action('woocommerce_after_template_part', [$this, 'before_after_price'], 9);
                    break;
                default:
                    add_action('woocommerce_before_add_to_cart_form', [$this, 'default_timer_show'], 9);
                    break;
            }
            if (wp_is_block_theme()) {
                add_action('wp_enqueue_scripts', [$this, 'enqueue_stock_progress_script']);
            }
        }
    }

    /**
     * Product stock status bar template style
     * 
     * @since 1.0.0
     * @access public
     * @return array
     */
    public function get_template_styles(): array {

        //If any new styles are added, please add here
        $styles = [
            'style-1' => [
                'package' => 'free',
            ],
            'style-2' => [
                'package' => 'pro',
            ],
            'style-3' => [
                'package' => 'pro',
            ],
            'style-4' => [
                'package' => 'pro',
            ],
            'style-5' => [
                'package' => 'pro'
            ],
            'style-6' => [
                'package' => 'pro'
            ], 
            'style-7' => [
                'package' => 'pro'
            ]
        ];
      
        return apply_filters('metsalescountdown_stock_progress_bar_template_styles', $styles);
    }

    /**
     * Product stock status bar position
     * 
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function before_after_price($template_name)  {
        
        if( $template_name == 'single-product/price.php') {
           
            echo wp_kses($this->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());

        }
    }

    /**
     * Product stock status bar position
     * 
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function default_timer_show() {
        echo wp_kses($this->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
    }

    /**
     * Product stock status bar position
     * 
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function before_after_cart() {
        echo wp_kses($this->single_product(), \MetSalesCountdown\Helpers\Utils::get_kses_array());
    }

    /**
     * Display product stock status bar on the single product page template.
     * 
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function single_product() {

        global $product;

        if (!$product || !method_exists($product, 'managing_stock') || !$product->managing_stock()) return; 
        
        $total_sales = intval(get_post_meta( $product->get_id(), 'total_sales', true ));
        $current_stock = intval($product->get_stock_quantity());
        $total_stock     = $total_sales + $current_stock;
        $message = isset($this->stock_status_settings['stock_status_bar_message']) ? $this->stock_status_settings['stock_status_bar_message'] : '';
        $message = str_replace('{sold_item}', $total_sales, $message);
        $message = str_replace('{remain_item}', $current_stock , $message);
        $style = isset($this->stock_status_settings['stock_status_bar_style']) ? $this->stock_status_settings['stock_status_bar_style'] : 'style-1';
        $styles = $this->get_template_styles();

        if(array_key_exists($style, $styles)) {

            $package = isset($styles[$style]['package']) ? $styles[$style]['package'] : '';

            if($package === 'pro' && class_exists('MetSalesCountdownPro')){
                $file_path = \MetSalesCountdownPro\Plugin::features_dir() . 'stock-progress-bar/templates/'.$style.'-html.php';
            }else if($package === 'pro' && !class_exists('MetSalesCountdownPro')){
                $file_path = \MetSalesCountdown\Plugin::features_dir() . 'stock-progress-bar/templates/style-1-html.php';
            }else{
                $file_path = \MetSalesCountdown\Plugin::features_dir() . 'stock-progress-bar/templates/'.$style.'-html.php';
            }
    
            ob_start();
    
            if(file_exists($file_path)) {
                require_once($file_path);
            }

            return ob_get_clean();
        }
    }

    /**
     * Enqueue stock progress script for block themes
     * 
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function enqueue_stock_progress_script() {
        
        $stock_status_bar_html = $this->single_product();
        wp_enqueue_script('met-stock-progress-bar', Plugin::plugin_url() . 'public/assets/js/stock-progress-bar.min.js', ['jquery'], Plugin::version(), true);
        wp_localize_script('met-stock-progress-bar', 'stockProgressBar', [
            'stockStatusBarHTML' => $stock_status_bar_html,
            'position' => $this->stock_status_settings['stock_status_bar_position']
        ]);
    }
} 
