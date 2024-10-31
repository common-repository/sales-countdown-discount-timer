<?php
defined('ABSPATH') || exit;

use MetSalesCountdown\Plugin;


wp_enqueue_style('metsalescountdown-style', Plugin::plugin_url() . 'public/assets/css/countdown.css', array(), Plugin::version(), 'all');
wp_enqueue_script('metsalescountdown-countdown', Plugin::plugin_url() . 'public/assets/js/countdown.js', array('jquery'), Plugin::version(), true);
 
// Localize the script with metsales settings data for admin part
$localized_data = array(
    'message' => isset($general_settings['met_sale_countdown_msg']) ? $general_settings['met_sale_countdown_msg'] : '',
    'countdown_format' => isset($general_settings['met_sale_countdown_time_format']) ? $general_settings['met_sale_countdown_time_format'] : '',
    'separator_toggle' => isset($general_settings['separator_toggle']) ? $general_settings['separator_toggle'] : '',
    'time_separator' => isset($general_settings['met_sale_countdown_separator']) ? $general_settings['met_sale_countdown_separator'] : '',
    'loop_countdown_toggle' => isset($general_settings['display_loop_product']) ? $general_settings['display_loop_product'] : '',
    'single_countdown_toggle' => isset($general_settings['display_single_product']) ? $general_settings['display_single_product'] : '',
    'campaign_category' => isset($campaign_settings['campaign_category']) ? $campaign_settings['campaign_category'] : '',
    'api_url' => get_rest_url('', 'metsalescountdown/v1/'),
    'campaign_title' => isset($campaign_settings['campaign_title']) ? $campaign_settings['campaign_title'] : '',
    'campaign_start' => isset($campaign_settings['campaign_start_time']) ? $campaign_settings['campaign_start_time'] : '',
    'campaign_end' => isset($campaign_settings['campaign_end_time']) ? $campaign_settings['campaign_end_time'] : '',
    'display_on_product_page' => isset($campaign_settings['display_campaign_product_page']) ? $campaign_settings['display_campaign_product_page'] : '',
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'nonce' => wp_create_nonce('save_custom_form_data')
);

wp_localize_script('metsalescountdown-countdown', 'countdownData', $localized_data);

?>
<div class="wrap met-sales-countdown-wrapper-admin-settings">
    <h1><?php echo esc_html__('Sales Timer Settings', 'met-sales-countdown') ?></h1>
    <div class="met-sales-countdown-wrapper">
        <div class="met-sale-countdown-header">
            <h1 class="met-sale-countdown-header-text"></h1>
        </div>
        <div id="clock">
            <div class="block month-block">
                <p class="digit met-sales-months" id="months"></p>
                <p class="label label-month"></p>
            </div>
            <p class="met-sales-countdown-time-separator"></p>
            <div class="block day-block">
                <p class="digit met-sales-days" id="days"></p>
                <p class="label label-day"></p>
            </div>
            <p class="met-sales-countdown-time-separator"></p>
            <div class="block hour-block">
                <p class="digit met-sales-hours" id="hours"></p>
                <p class="label label-hour"></p>
            </div>
            <p class="met-sales-countdown-time-separator"></p>
            <div class="block minute-block">
                <p class="digit met-sales-minutes" id="minutes"></p>
                <p class="label label-minute"></p>
            </div>
            <p class="met-sales-countdown-time-separator"></p>
            <div class="block second-block">
                <p class="digit met-sales-seconds" id="seconds"></p>
                <p class="label label-second"></p>
            </div>
        </div>
    </div>
</div>