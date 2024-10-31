<?php
/**
 * Plugin Name: Met Sales Countdown
 * Description: A addon for display WooCommerce Sales Countdown on various pages.
 * Plugin URI: https://wpmet.com/plugin/met-sales-countdown
 * Author: Wpmet
 * Version: 1.0.5
 * Author URI: https://wpmet.com/
 * Text Domain: met-sales-countdown
 * Domain Path: /languages
 * License:  GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */

use MetSalesCountdown\Traits\Singleton;

defined( 'ABSPATH' ) || exit;


// Run autoloader
require_once plugin_dir_path( __FILE__ ) . 'autoloader.php';

if ( ! class_exists( 'MetSalesCountdown' )) {
    /**
     * Description: The main plugin class
     *
     * @package    MetSalesCountdown
     * @subpackage Plugin Core
     * @since      1.0.0
     */
    final class MetSalesCountdown {
    
        use Singleton;
    
        /**
         * Class Contractor
         *
         * The main constructor that will loads the plugin
         * @since      1.0.0
         * @access     public
         * @return     void
         */
        public function __construct() {

            add_action( 'plugins_loaded', [$this, 'metsalescountdown_plugin_loaded'] );
          
        }

        public function metsalescountdown_plugin_loaded() {
            do_action('metsalescountdown/before_loaded');
    
            // Instantiate the plugin
            MetSalesCountdown\Plugin::instance()->init();
    
            do_action('metsalescountdown/after_loaded');
    
            register_activation_hook( __FILE__, [$this, 'metsalescountdown_plugin_activate'] );
        }

    
        public function metsalescountdown_plugin_activate() {
    
            //Save plugin installation time.
            $installed = get_option('metsalescountdown_installation_time');
            if (!$installed) {
                update_option('metsalescountdown_installation_time', time());
            }

            //Update plugin version in the option table.
            update_option('metsalescountdown_version', \MetSalesCountdown\Plugin::version());            
        }
    }
}

// Instantiate the plugin
new MetSalesCountdown();