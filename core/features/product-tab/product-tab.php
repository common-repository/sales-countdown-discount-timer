<?php 
namespace MetSalesCountdown\Core\Features\Product_Tab;

defined('ABSPATH') || exit;

use MetSalesCountdown\Core\Interfaces\Features_Interface;
use MetSalesCountdown\Plugin;
use MetSalesCountdown\Helpers\Utils;
use WP_Query;

defined( 'ABSPATH' ) || exit;

class Product_Tab implements Features_Interface {
	protected $settings;
	protected $data;

	public function name(): string {
        return __('Product Tab', 'met-sales-countdown');
    }

	public function init(): void {
        
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );

		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'woocommerce_process_product_meta_simple' ) );
		add_action( 'woocommerce_process_product_meta_external', array( $this, 'woocommerce_process_product_meta_simple', ) );
		add_action( 'woocommerce_save_product_variation', array( $this, 'woocommerce_save_product_variation' ), 10, 2 );

		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'woocommerce_product_content_buffering_start' ) );
		add_action( 'woocommerce_variation_options', array( $this, 'woocommerce_product_content_buffering_start' ) );
		add_action( 'woocommerce_product_options_pricing', array( $this, 'woocommerce_product_options_pricing' ), 99 );
		add_action( 'woocommerce_variation_options_pricing', array( $this, 'woocommerce_variation_options_pricing' ), 10, 3 );

		if(!function_exists('metsalescountdown_time')){
			function metsalescountdown_time($time){
				if(!$time){
					return 0;
				}
				$temp=explode(":",$time);
				if(count($temp)==2){
					return (absint($temp[0])*3600+absint($temp[1])*60);
				}else{
					return 0;
				}
			}
		}

		if(!function_exists('metsalescountdown_time_revert')){
			function metsalescountdown_time_revert($time){
				$hour=floor($time/3600);
				$min=floor(($time-3600*$hour)/60);
				return implode(':',array(zeroise($hour,2),zeroise($min,2)));
			}
		}
    }
	
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( $screen->id == 'product' ) {
			wp_enqueue_script( 'metsalescountdown-timer-admin-product',  Plugin::plugin_url() . 'public/assets/js/met-sales-countdown-timer-admin-product.js', array( 'jquery' ), Plugin::version(), true );
			wp_enqueue_style( 'metsalescountdown-timer-admin-product', Plugin::plugin_url() . 'public/assets/css/met-sales-countdown-timer-admin-product.css', Plugin::version(), 'all' );	
		} 
	}

	public function woocommerce_product_content_buffering_start() {
		ob_start();
	}

	public function woocommerce_product_options_pricing() {

		global $post;
		$html = ob_get_clean();
		preg_match_all( '/<p class=\"form-field sale_price_dates_fields\"(.+?)<\/p>/si', $html, $datefields );
		$html = str_replace( $datefields[0], '', $html );
		echo wp_kses($html, Utils::get_kses_array());
		$product_object        = wc_get_product( $post->ID );
		$sale_from             = $product_object->get_date_on_sale_from( 'edit' ) ? $product_object->get_date_on_sale_from( 'edit' )->getOffsetTimestamp() : 0;
		$sale_to               = $product_object->get_date_on_sale_to( 'edit' ) ? $product_object->get_date_on_sale_to( 'edit' )->getOffsetTimestamp() : 0;
		$sale_price_dates_from = $sale_from ? date_i18n( 'Y-m-d', $sale_from ) : '';
		$sale_price_dates_to   = $sale_to ? date_i18n( 'Y-m-d', $sale_to ) : '';
		$sale_price_time_from  = $sale_from % 86400;
		$sale_price_time_to    = $sale_to % 86400;

		echo wp_kses('<p class="form-field sale_price_dates_field">
				<label for="_sale_price_dates_from">' . esc_html__('Sale price dates and times', 'met-sales-countdown') . '</label>
				<input type="text" class="short" name="_sale_price_dates_from" id="_sale_price_dates_from" value="' . esc_attr($sale_price_dates_from) . '" placeholder="' . esc_html(_x('From&hellip;', 'placeholder', 'met-sales-countdown')) . ' YYYY-MM-DD" maxlength="10" pattern="' . esc_attr(apply_filters('woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])')) . '" />
				<input type="time" name="_sale_price_times_from" id="sale_price_times_from" value="' . esc_attr(metsalescountdown_time_revert($sale_price_time_from)) . '">
				<input type="text" class="short" name="_sale_price_dates_to" id="_sale_price_dates_to" value="' . esc_attr($sale_price_dates_to) . '" placeholder="' . esc_html(_x('To&hellip;', 'placeholder', 'met-sales-countdown')) . '  YYYY-MM-DD" maxlength="10" pattern="' . esc_attr(apply_filters('woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])')) . '" />
				<input type="time" name="_sale_price_times_to" id="sale_price_times_to" value="' . esc_attr(metsalescountdown_time_revert($sale_price_time_to)) . '">
				<a href="#" class="description cancel_sale_schedule">' . esc_html__('Cancel', 'met-sales-countdown') . '</a>' . wp_kses(wc_help_tip('Dates and times value are set in your website timezone.', 'met-sales-countdown'), \wp_kses_allowed_html()) . '
			</p>', Utils::get_kses_array());
		echo wp_kses('<div class="met-sales-countdown-timer-admin-product">', Utils::get_kses_array());
		
		echo wp_kses('</div>', Utils::get_kses_array());
	}


	public function woocommerce_variation_options_pricing( $loop, $variation_data, $variation ) {
		 
		$html = ob_get_clean();
		preg_match_all( '/<div class=\"form-field sale_price_dates_fields hidden\"(.+?)<\/div>/si', $html, $datefields );
		$html = str_replace( $datefields[0], '', $html );
		echo wp_kses( $html, Utils::get_kses_array());
		$variation_object      = wc_get_product( $variation->ID );
		$sale_from             = $variation_object->get_date_on_sale_from( 'edit' ) ? $variation_object->get_date_on_sale_from( 'edit' )->getOffsetTimestamp() : 0;
		$sale_to               = $variation_object->get_date_on_sale_to( 'edit' ) ? $variation_object->get_date_on_sale_to( 'edit' )->getOffsetTimestamp() : 0;
		$sale_price_dates_from = $sale_from ? date_i18n( 'Y-m-d', $sale_from ) : '';
		$sale_price_dates_to   = $sale_to ? date_i18n( 'Y-m-d', $sale_to ) : '';
		$sale_price_time_from  = $sale_from % 86400;
		$sale_price_time_to    = $sale_to % 86400;

		echo wp_kses('<div class="form-field sale_price_dates_field hidden">
				<p class="form-row form-row-first">
					<label>' . esc_html__('Sale start date', 'met-sales-countdown') . '</label>
					<input type="text" class="sale_price_dates_from" name="variable_sale_price_dates_from[' . esc_attr($loop) . ']" value="' . esc_attr($sale_price_dates_from) . '" placeholder="' . esc_html_x('From&hellip;', 'placeholder', 'met-sales-countdown') . ' YYYY-MM-DD" maxlength="10" pattern="' . esc_attr(apply_filters('woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])')) . '" />
					<input type="time" name="variable_sale_price_times_from[' . esc_attr($loop) . ']" class="variable_sale_price_times_from" value="' . esc_attr(metsalescountdown_time_revert($sale_price_time_from)) . '">
				</p>
				<p class="form-row form-row-last">
					<label>' . esc_html__('Sale end date', 'met-sales-countdown') . '</label>
					<input type="text" class="sale_price_dates_to" name="variable_sale_price_dates_to[' . esc_attr($loop) . ']" value="' . esc_attr($sale_price_dates_to) . '" placeholder="' . esc_html_x('To&hellip;', 'placeholder', 'met-sales-countdown') . '  YYYY-MM-DD" maxlength="10" pattern="' . esc_attr(apply_filters('woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])')) . '" />
					<input type="time" name="variable_sale_price_times_to[' . esc_attr($loop) . ']" class="variable_sale_price_times_to" value="' . esc_attr(metsalescountdown_time_revert($sale_price_time_to)) . '">
					<input type="hidden" name="_metsale_product_meta_nonce" value="' . esc_attr(wp_create_nonce('metsale_product_meta_nonce')) . '">
				</p>
			</div>', Utils::get_kses_array());
		echo wp_kses('<div class="met-sales-countdown-timer-admin-product">', Utils::get_kses_array());
		echo wp_kses('</div>', Utils::get_kses_array());
	}

	public function woocommerce_process_product_meta_simple( $post_id ) {

		if(isset( $_POST['woocommerce_meta_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data')){
			
			$gmt_offset            = get_option( 'gmt_offset' );
			$date_on_sale_from     = isset( $_POST['_sale_price_dates_from'] ) ? strtotime( sanitize_text_field( wp_unslash( $_POST['_sale_price_dates_from'] ) ) ) : '';
			$date_on_sale_to       = isset( $_POST['_sale_price_dates_to'] ) ? strtotime( sanitize_text_field( wp_unslash( $_POST['_sale_price_dates_to'] ) ) ) : '';
			$sale_price_times_from = isset( $_POST['_sale_price_times_from'] ) ? metsalescountdown_time( sanitize_text_field( wp_unslash( $_POST['_sale_price_times_from'] ) ) ) : '';
			$sale_price_times_to   = isset( $_POST['_sale_price_times_to'] ) ? metsalescountdown_time( sanitize_text_field( wp_unslash( $_POST['_sale_price_times_to'] ) ) ) : '';
			
			if ( $date_on_sale_from ) {
				$date_on_sale_from += $sale_price_times_from;
			} elseif ( $date_on_sale_to ) {
				$date_on_sale_from = strtotime( gmdate( "Y-m-d" ) );
			}
			if ( $date_on_sale_to ) {
				$date_on_sale_to += $sale_price_times_to;
			}
			update_post_meta( $post_id, '_sale_price_dates_from', ( $date_on_sale_from - $gmt_offset * 3600 ) > 0 ? ( $date_on_sale_from - $gmt_offset * 3600 ) : '' );
			update_post_meta( $post_id, '_sale_price_dates_to', ( $date_on_sale_to - $gmt_offset * 3600 ) > 0 ? ( $date_on_sale_to - $gmt_offset * 3600 ) : '' );
			update_post_meta( $post_id, '_sale_price_times_from', isset( $_POST['_sale_price_times_from'] ) ? sanitize_text_field( wp_unslash( $_POST['_sale_price_times_from'] ) ) : '00:00' );
			update_post_meta( $post_id, '_sale_price_times_to', isset( $_POST['_sale_price_times_to'] ) ? sanitize_text_field( wp_unslash( $_POST['_sale_price_times_to'] ) ) : '00:00' );
		}
	}

	public function woocommerce_save_product_variation( $variation_id, $i ) {

		if(isset( $_POST['_metsale_product_meta_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['_metsale_product_meta_nonce'] ), 'metsale_product_meta_nonce')){

			global $post;
			update_post_meta( $variation_id, 'metsalescountdown_timer', isset( $_POST['metsalescountdown_timer'][ $i ] ) ? sanitize_text_field( wp_unslash( $_POST['metsalescountdown_timer'][ $i ] ) ) : '' );
			$gmt_offset            = get_option( 'gmt_offset' );
			$date_on_sale_from     = isset( $_POST['variable_sale_price_dates_from'][ $i ] ) ? strtotime( sanitize_text_field( wp_unslash( $_POST['variable_sale_price_dates_from'][ $i ] ) ) ) : '';
			$date_on_sale_to       = isset( $_POST['variable_sale_price_dates_to'][ $i ] ) ? strtotime( sanitize_text_field( wp_unslash( $_POST['variable_sale_price_dates_to'][ $i ] ) ) ) : '';
			$sale_price_times_from = isset( $_POST['variable_sale_price_times_from'][ $i ] ) ? sanitize_text_field( wp_unslash( $_POST['variable_sale_price_times_from'][ $i ] )) : '00:00';
			$sale_price_times_to   = isset( $_POST['variable_sale_price_times_to'][ $i ] ) ? sanitize_text_field( wp_unslash( $_POST['variable_sale_price_times_to'][ $i ] )) : '00:00';
			$time_from             = metsalescountdown_time( $sale_price_times_from );
			$time_to               = metsalescountdown_time( $sale_price_times_to );
			if ( $date_on_sale_from ) {
				$date_on_sale_from += $time_from;
			} else {
				$date_on_sale_from     = strtotime( gmdate( "Y-m-d" ) );
				$sale_price_times_from = '00:00';
			}
			if ( $date_on_sale_to ) {
				$date_on_sale_to += $time_to;
			}
			$expire=$date_on_sale_from-current_time( 'timestamp' );
			if ( isset( $_POST['variable_sale_price'][ $i ] ) && $_POST['variable_sale_price'][ $i ] && $date_on_sale_from && $expire>0 ) { 
				set_transient( 'metsalescountdown_timer_update_variable_price_start_sale_' . $variation_id, $date_on_sale_from,$expire );
			}
			update_post_meta( $variation_id, '_sale_price_dates_from', ( $date_on_sale_from - $gmt_offset * 3600 ) > 0 ? ( $date_on_sale_from - $gmt_offset * 3600 ) : '' );
			update_post_meta( $variation_id, '_sale_price_dates_to', ! empty( $date_on_sale_to ) && ( $date_on_sale_to - $gmt_offset * 3600 ) > 0 ? ( $date_on_sale_to - $gmt_offset * 3600 ) : '' );
			update_post_meta( $variation_id, 'metsalescountdown_timer', isset( $_POST['metsalescountdown_timer'] ) ? sanitize_text_field(wp_unslash( $_POST['metsalescountdown_timer'][ $i ] ) ) : '' );
			update_post_meta( $variation_id, '_sale_price_times_from', $sale_price_times_from );
			update_post_meta( $variation_id, '_sale_price_times_to', $sale_price_times_to );
		}		
	}
}