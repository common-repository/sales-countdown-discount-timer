<?php
namespace MetSalesCountdown\Core\Admin\Notices;

use MetSalesCountdown;
use MetSalesCountdown\Traits\Singleton;
use MetSalesCountdown\Plugin;

defined('ABSPATH') || exit;

class Dependency_check {

	use Singleton;

	public function init() {
	
        if( !did_action( 'woocommerce_loaded' ) ) {

            add_action('admin_notices', [$this, 'missing_woocommerce']);

            return false;
        }

        return true;
	}

    public function missing_woocommerce() {

        if(file_exists(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php')) {

            $btn['label'] = esc_html__('Activate WooCommerce', 'met-sales-countdown');
            $btn['url']   = wp_nonce_url('plugins.php?action=activate&plugin=woocommerce/woocommerce.php&plugin_status=all&paged=1', 'activate-plugin_woocommerce/woocommerce.php');

        } else {

            $btn['label'] = esc_html__('Install WooCommerce', 'met-sales-countdown');
            $btn['url']   = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=woocommerce'), 'install-plugin_woocommerce');
        }

        MetSalesCountdown\Core\Admin\Notices\Admin_notice::push(
            [
                'id'          => 'missing-woo',
                'type'        => 'error',
                'dismissible' => true,
                'is_required' => true,
                'btn'         => $btn,
                'message'     => sprintf(esc_html__('Met Sales Countdown requires woocommerce Plugin, which is currently NOT RUNNING.', 'met-sales-countdown'))
            ]
        );
    }
}