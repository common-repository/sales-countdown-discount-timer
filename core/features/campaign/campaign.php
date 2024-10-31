<?php 

namespace MetSalesCountdown\Core\Features\Campaign;

use MetSalesCountdown\Core\Admin\Classes\Settings;
use MetSalesCountdown\Core\Interfaces\Features_Interface;
use MetSalesCountdown\Core\Features\Campaign\Campaign_Loop_Product;
use MetSalesCountdown\Core\Features\Campaign\Campaign_Single_Product;
use MetSalesCountdown\Traits\Singleton;
use WP_Query;

defined('ABSPATH') || exit; 

class Campaign implements Features_Interface {

	use Singleton;

	private $selected_categories;
	private $campaign_data;
	private $campaign_products = [];

  	/**
     * Get the name of the feature
     * 
     * @since 1.0.0
     * @access public
     * @return string
     */
	public function name(): string {
        return __('Campaign', 'met-sales-countdown');
    }

    /**
     * Initialize feature
     * 
     * @since 1.0.5
     * @access public
     * @return void
     */
	public function init(): void
	{

		//campaign loop product and single product countdown feature
		( new Campaign_Loop_Product() )->init();
		( new Campaign_Single_Product() )->init();
		
		$this->campaign_data = Settings::instance()->get_campaign_settings();
        $this->selected_categories = isset( $this->campaign_data['campaign_category'] ) ? $this->campaign_data['campaign_category'] : [];
		
		add_filter('woocommerce_product_get_price', [$this, 'update_product_price'], 10, 2);
		add_filter( 'woocommerce_variation_prices', [$this, 'product_variation_price'] , 10, 3 );
		add_filter( 'woocommerce_product_variation_get_price', [$this, 'product_variation_single_price'], 10, 2 );
		add_filter( 'woocommerce_product_is_on_sale',  [$this, 'woocommerce_product_is_on_sale'], 999, 2 );
	}

	/**
	 * Update product price as per campaign discount sale
	 * 
	 * @since 1.0.5
	 * @param float $price
	 * @param object $product
	 * 
	 * @return float
	 */
	public function update_product_price( $price, $product ) {

		if ( ! $this->is_campaign_time() || empty( array_intersect( $product->get_category_ids(), $this->selected_categories ) ) ) {
			return $price;
		}

        $campaign_dis_ammount = isset( $this->campaign_data['campaign_discount_amount'] ) ? $this->campaign_data['campaign_discount_amount'] : '';
        $campaign_dis_type = isset( $this->campaign_data['campaign_discount_type'] ) ? $this->campaign_data['campaign_discount_type'] : '';
		$product_id = $product->get_id();

		if( $campaign_dis_ammount && $campaign_dis_type ) {

			if( $campaign_dis_type === 'fixed' ) {
				$price = $price - $campaign_dis_ammount;
			}

			if( $campaign_dis_type === 'percent' ) {
				$price = $price - ( $campaign_dis_ammount / 100 ) * $price;
			}
		}

		$price = $price < 0 ? 0 : $price;

		return $price;
	}

	/**
	 * Check if product is on sale and return true for campaign product
	 * 
	 * @since 1.0.5
	 * @param bool $onsale
	 * @param object $product
	 * 
	 * @return bool|float
	 */
	public function woocommerce_product_is_on_sale( $onsale, $product ){

		if ( ! $this->is_campaign_time() || empty( array_intersect( $product->get_category_ids(), $this->selected_categories ) ) ) {
			return $onsale;
		}

		return true;
	}

	/**
	 * Update variation price as per campaign discount sale
	 * 
	 * @since 1.0.5
	 * @param array $price_ranges
	 * @param object $product
	 * @param string $display
	 * 
	 * @return array
	 */
	public function product_variation_price( $price_ranges, $product, $display ) {	

		if ( ! $this->is_campaign_time() || empty( array_intersect( $product->get_category_ids(), $this->selected_categories ) ) ) {
			return $price_ranges;
		}

		$product_id = $product->get_id();
		$campaign_dis_ammount = isset( $this->campaign_data['campaign_discount_amount'] ) ? $this->campaign_data['campaign_discount_amount'] : '';
        $campaign_dis_type = isset( $this->campaign_data['campaign_discount_type'] ) ? $this->campaign_data['campaign_discount_type'] : '';
		
		if( $campaign_dis_ammount && $campaign_dis_type ) {

			if( $campaign_dis_type === 'fixed' ) {
				foreach($price_ranges['price'] as $key => $value){					
					$price_ranges['price'][$key] = $value - $campaign_dis_ammount;
				}
			}

			if( $campaign_dis_type === 'percent' ) {
				foreach($price_ranges['price'] as $key => $value){					
					$price_ranges['price'][$key] = $value - ($campaign_dis_ammount / 100)* $value;
				}
			}
		}

		return $price_ranges;
	}

	/**
	 * Update variation single product price as per campaign discount sale
	 * 
	 * @since 1.0.5
	 * @param float $price
	 * @param object $product
	 * 
	 * @return float
	 */
	public function product_variation_single_price( $price, $product ){
		
		if ( ! $this->is_campaign_time() || empty( array_intersect( $product->get_category_ids(), $this->selected_categories ) ) ) {
			return $price;
		}

		$product_id = $product->get_id();
		$campaign_dis_ammount = isset( $this->campaign_data['campaign_discount_amount'] ) ? $this->campaign_data['campaign_discount_amount'] : '';
        $campaign_dis_type = isset( $this->campaign_data['campaign_discount_type'] ) ? $this->campaign_data['campaign_discount_type'] : '';
				
		if( $campaign_dis_ammount && $campaign_dis_type ) {

			if( $campaign_dis_type === 'fixed') {
				$price = $price - $campaign_dis_ammount;
			}

			if( $campaign_dis_type === 'percent') {
				$price = $price - ($campaign_dis_ammount / 100)* $price;
			}
		}
		
		return $price;
	}

	/**
	 * Check if campaign time is set and includes current time
	 *
	 * @since 1.0.5
	 *
	 * @return bool
	 */
	public function is_campaign_time() {

		$campaign_start_time = isset( $this->campaign_data['campaign_start_time'] ) ? $this->campaign_data['campaign_start_time'] : '';
		$campaign_end_time = isset( $this->campaign_data['campaign_end_time'] ) ? $this->campaign_data['campaign_end_time'] : '';

		if ( !empty( $campaign_start_time ) && !empty( $campaign_end_time ) ) {
			
			$campaign_start = new \DateTime( $campaign_start_time );
			$campaign_end = new \DateTime( $campaign_end_time );
			$current_time = new \DateTime();
			
			if ( $current_time >= $campaign_start && $current_time <= $campaign_end ) {
				
				return true;
			}
		}
		
		return false;
	}
}
