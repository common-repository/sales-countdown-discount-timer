<?php
namespace MetSalesCountdown\Core\Admin\Classes;

use MetSalesCountdown\Traits\Singleton;

defined('ABSPATH') || exit;

class Settings
{
    use Singleton;

	const GENERAL_SETTINGS_KEY           = 'metsalescountdown_general';
	const CAMPAIGN_SETTINGS_KEY          = 'metsalescountdown_campaign';
	const STOCK_STATUS_BAR_SETTINGS_KEY  = 'metsalescountdown_stock_status_bar';

    private $general;
	private $campaign;
	private $stock_status_bar;

    /**
     * Settings constructor.
     * 
     * @return void
     */
	public function __construct() {

		$this->general    = get_option(self::GENERAL_SETTINGS_KEY, []);
		$this->campaign   = get_option(self::CAMPAIGN_SETTINGS_KEY, []);
		$this->stock_status_bar = get_option(self::STOCK_STATUS_BAR_SETTINGS_KEY, []);
	}

    /**
     * Get general settings
     * 
     * @return array
     * @since 1.0.0
     * @access public
     * 
     */
    public function get_general_settings() {
        return $this->general;
    }

    /**
     * Get campaign settings
     * 
     * @return array
     * @since 1.0.0
     * @access public
     * 
     */
    public function get_campaign_settings(){
        return $this->campaign;
    }

    /**
     * Get stock status bar settings
     * 
     * @return array
     * @since 1.0.0
     * @access public
     * 
     */
    public function get_stock_status_bar_settings(){
        return $this->stock_status_bar;
    }

    /**
     * Check if show stock status bar
     * 
     * @return bool
     * @since 1.0.0
     * @access public
     * 
     */
    public function is_show_stock_status_bar(){

        if(isset($this->stock_status_bar['display_stock_status_bar']) && $this->stock_status_bar['display_stock_status_bar'] == 1){
            return true;
        }

        return false;

    }

    /**
     * Get general countdown title
     * 
     * @return string
     * @since 1.0.0
     * @access public
     * 
     */
    public function get_general_countdown_title(){
        return isset($this->general['met_sale_countdown_msg']) ? $this->general['met_sale_countdown_msg'] : '';
    }

    /**
     * Get campaign countdown title
     * 
     * @return string
     * @since 1.0.0
     * @access public
     * 
     */
    public function get_campaign_countdown_title(){
        return isset($this->campaign['campaign_title']) ? $this->campaign['campaign_title'] : '';
    }
}