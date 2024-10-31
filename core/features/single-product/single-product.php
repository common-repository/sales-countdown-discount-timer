<?php
namespace MetSalesCountdown\Core\Features\Single_Product;

defined('ABSPATH') || exit;

use MetSalesCountdown\Core\Interfaces\Features_Interface;
use MetSalesCountdown\Plugin;

class Single_Product implements Features_Interface
{
    public function name(): string
    {
        return __('Single Product', 'met-sales-countdown');
    }

    public function init(): void
    {
    }

    public function single_product()
    {
        global $product;

        if (empty($product)) {
            return;
        }

        $current_time = strtotime(gmdate('Y-m-d H:i:s'));

        $sales_price_date_from = (int) get_post_meta($product->get_id(), '_sale_price_dates_from', true);
        $sales_price_date_to   = (int) get_post_meta($product->get_id(), '_sale_price_dates_to', true);

        $sales_price_time_from = get_post_meta($product->get_id(), '_sale_price_times_from', true);
        $sales_price_time_to   = get_post_meta($product->get_id(), '_sale_price_times_to', true);

        $variation_times = [];

        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();

            if (empty($variations)) {
                return;
            }

            foreach ($variations as $v_key => $variation) {
                $variation_id = $variation['variation_id'];

                $variation_attributes = $variation['attributes'];
                $attribute_values     = [];

                foreach ($variation_attributes as $attribute_name => $attribute_value) {
                    $taxonomy       = str_replace('attribute_', '', $attribute_name);
                    $attribute_term = get_term_by('slug', $attribute_value, $taxonomy);

                    if ($attribute_term && !is_wp_error($attribute_term)) {
                        $attribute_values[$taxonomy] = $attribute_term->name;
                    } else {
                        $attribute_values[$taxonomy] = $attribute_value;
                    }
                }

                $sales_price_date_from = (int) get_post_meta($variation_id, '_sale_price_dates_from', true);
                $sales_price_date_to   = (int) get_post_meta($variation_id, '_sale_price_dates_to', true);

                $sales_price_time_from = get_post_meta($variation_id, '_sale_price_times_from', true);
                $sales_price_time_to   = get_post_meta($variation_id, '_sale_price_times_to', true);

                $variation_times[] = [
                    'v_key'                 => $v_key,
                    'attribute_values'      => $attribute_values,
                    'sales_price_date_from' => $sales_price_date_from,
                    'sales_price_date_to'   => $sales_price_date_to,
                    'sales_price_time_from' => $sales_price_time_from,
                    'sales_price_time_to'   => $sales_price_time_to,
                    'current_time'          => $current_time,
                    'variation_id'          => $variation_id,
                ];
            }
        }

        $product_schedules = [$sales_price_date_from, $sales_price_date_to];

        $msc_product_schedules = !empty($variation_times) ? $variation_times : [
            'sales_price_date_from' => $sales_price_date_from,
            'sales_price_date_to'   => $sales_price_date_to,
            'sales_price_time_from' => $sales_price_time_from,
            'sales_price_time_to'   => $sales_price_time_to,
            'current_time'          => $current_time,
        ];

        if (!$product->is_type('variable') && in_array(0, $product_schedules)) {
            return;
        }

        ob_start();
        ?>

<div class="met-sales-countdown-single-product msc-single-product msc-single-product-campaign met-sales-countdown-wrapper"
    data-metsales-schedules='<?php echo esc_html(wp_json_encode($msc_product_schedules)); ?>'>
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
<?php
return ob_get_clean();
}
}
?>
