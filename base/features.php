<?php
namespace MetSalesCountdown\Base;

use MetSalesCountdown\Traits\Singleton;
use MetSalesCountdown\Core\Interfaces\Features_Interface;

// Features
use MetSalesCountdown\Core\Features\Single_Product\Single_Product;
use MetSalesCountdown\Core\Features\Product_Loops\Product_Loops;
use MetSalesCountdown\Core\Features\Product_Tab\Product_Tab;
use MetSalesCountdown\Core\Features\Stock_Progress_Bar\Stock_Progress_Bar;
use MetSalesCountdown\Core\Features\Campaign\Campaign;

defined( 'ABSPATH' ) || exit;

/**
 * Description: list of features loader
 *
 * @package    MetSalesCountdown
 * @subpackage Plugin Core
 * @since      1.0.0
 */
class Features {

    use Singleton;

    private $features = [];

    public function init_features() {

        // apply filter on features array
        $this->features = apply_filters('metsalescountdown_features', [
            'product-loops' => new Product_Loops(),
            'single-product' => new Single_Product(),
            'product' => new Product_Tab(),
            'stock-progress-bar' => new Stock_Progress_Bar(), 
            'campaign' => new Campaign(), 
        ]);

        // Load features
        foreach ($this->features as $feature) {
            $this->load_feature($feature);
        }
    }

    public function load_feature(Features_Interface $feature) {
        
        do_action('metsalescountdown_load_feature', $feature );
        // Load features
        $feature->init();
    }
}