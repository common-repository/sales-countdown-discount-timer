<?php
namespace MetSalesCountdown\Core\Features\Product_Loops;

use MetSalesCountdown\Core\Admin\Classes\Settings;
use MetSalesCountdown\Core\Interfaces\Features_Interface;

defined('ABSPATH') || exit;

class Product_Loops implements Features_Interface
{

    public function name(): string
    {
        return __('Product Loops', 'met-sales-countdown');
    }

    public function init(): void
    {

        $countdown_settings = Settings::instance()->get_general_settings();

        if (isset($countdown_settings['display_loop_product']) && $countdown_settings['display_loop_product'] == 1) {
            add_action('woocommerce_before_shop_loop_item_title', [$this, 'loop_product'], 999);
        }
    }

    public function loop_product()
    {
        global $product;
        //Product Scheduled Date
        $sales_price_date_from = (int) get_post_meta($product->get_id(), '_sale_price_dates_from', true);
        $sales_price_date_to   = (int) get_post_meta($product->get_id(), '_sale_price_dates_to', true);

        //Product Scheduled Time
        $sales_price_time_from = (int) get_post_meta($product->get_id(), '_sale_price_times_from', true);
        $sales_price_time_to   = (int) get_post_meta($product->get_id(), '_sale_price_times_to', true);
        if ($product->is_type('variable')) {
            // For variable products
            $variations = $product->get_available_variations();

            foreach ($variations as $variation) {
                $variation_id = $variation['variation_id'];

                // Sale Price Date
                $sales_price_date_from = (int) get_post_meta($variation_id, '_sale_price_dates_from', true);
                $sales_price_date_to   = (int) get_post_meta($variation_id, '_sale_price_dates_to', true);

                // Sale Price Time
                $sales_price_time_from = get_post_meta($variation_id, '_sale_price_times_from', true);
                $sales_price_time_to   = get_post_meta($variation_id, '_sale_price_times_to', true);
            }
        }

        $product_schedules = [$sales_price_date_from, $sales_price_date_to];
        $current_time      = strtotime(gmdate('Y-m-d H:i:s')); // get the current time

        $loop_product_schedules = [
            'sales_price_date_from' => $sales_price_date_from,
            'sales_price_date_to'   => $sales_price_date_to,
            'sales_price_time_from' => $sales_price_time_from,
            'sales_price_time_to'   => $sales_price_time_to,
            'current_time'          => $current_time,
        ];

        if (in_array(0, $product_schedules)) {
            return;
        }
        ?>

<div class="met-sales-countdown-loop-product msc-loop-product met-sales-countdown-wrapper"
    data-loop-product-schedules='<?php echo esc_html(wp_json_encode($loop_product_schedules)); ?>'>
    <!-- <div class="met-sale-countdown-header">
                <h1 class="met-sale-countdown-header-text"></h1>
            </div> -->

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
<?php
}
}