<?php

namespace MetSalesCountdown\Core\Features\Campaign;

use MetSalesCountdown\Core\Admin\Classes\Settings;
use MetSalesCountdown\Traits\Singleton;
use WP_Query;

defined('ABSPATH') || exit;

class Campaign_Loop_Product
{
    use Singleton;

    private $product_data;
    private $selected_categories;
    private $campaign_data;
    private $campaign_products = [];
    private $general_settings;

    /**
     * Initialize feature
     *
     * @since 1.0.0
     * @access public
     * @return void
     */
    public function init(): void
    {
        $this->campaign_data    = Settings::instance()->get_campaign_settings();
        $this->general_settings = Settings::instance()->get_general_settings();

        if (isset($this->campaign_data['display_campaign_product_page']) && $this->campaign_data['display_campaign_product_page'] == 1) {
            $this->selected_categories = isset($this->campaign_data['campaign_category']) ? $this->campaign_data['campaign_category'] : [];
            add_action('woocommerce_before_shop_loop_item_title', [$this, 'loop_product'], 999);
        }
    }

    public function loop_product()
    {
        global $product;
        $this->get_campaign_products();

        //Product Scheduled Date
        $sales_price_date_from = (int) get_post_meta(get_the_ID(), '_sale_price_dates_from', true);
        $sales_price_date_to   = (int) get_post_meta(get_the_ID(), '_sale_price_dates_to', true);
        $is_general_timer_on   = (isset($this->general_settings['display_loop_product']) && $this->general_settings['display_loop_product'] == 1) ? true : false;
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
        if ($is_general_timer_on && $sales_price_date_from != 0 && $sales_price_date_to != 0 && $sales_price_date_to > time()) {
            return;
        }

        $categories = wp_get_post_terms(get_the_ID(), 'product_cat');

        $current_product_cat_id = $categories[0]->term_id;

        if (in_array(get_the_ID(), array_unique($this->campaign_products))) {
            $product               = get_the_title(get_the_ID());
            $start_date            = $this->campaign_data['campaign_start_time'];
            $end_date              = $this->campaign_data['campaign_end_time'];
            $sales_price_time_from = !empty($start_date) ? $start_date : '';
            $sales_price_time_to   = !empty($end_date) ? $end_date : '';
            $current_time          = strtotime(gmdate('Y-m-d H:i:s')); // get the current time
            $product_schedules     = [$sales_price_time_from, $sales_price_time_to];

            if (!isset($sales_price_from) || empty($sales_price_from)) {
                $sales_price_from = strtotime(get_the_date());
            }

            $deal_data = [
                'sales_price_date_from' => $sales_price_time_from,
                'sales_price_date_to'   => $sales_price_time_to,
                'current_time'          => $current_time,
            ];

            ?>

<div class="met-sales-countdown-loop-product msc-category-loop-product met-sales-countdown-wrapper"
    data-loop-product-schedules='<?php echo esc_html(wp_json_encode($deal_data)); ?>'>

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

    public function get_campaign_products()
    {

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1, // You can adjust this as needed
            'post_status' => 'publish',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'id',
                    'terms'    => $this->selected_categories,
                ),
            ),
        );

        $products = new WP_Query($args);
        if ($products->have_posts()):
            while ($products->have_posts()): $products->the_post();

                $this->campaign_products[] = get_the_ID();

                // You can output product information here

            endwhile;
        endif;

        wp_reset_postdata();
    }
}