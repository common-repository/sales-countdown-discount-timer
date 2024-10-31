<?php
namespace MetSalesCountdown;

use MetSalesCountdown\Core\Admin\Classes\Side_Menu;
use MetSalesCountdown\Core\Admin\Notices\Admin_notice;
use MetSalesCountdown\Core\Admin\Notices\Dependency_check;
use MetSalesCountdown\Core\Api\Get_Categories;
use MetSalesCountdown\Core\Features\FeatureInit;
use MetSalesCountdown\Traits\Singleton;

defined('ABSPATH') || exit;

/**
 * Description: Main Plugin Class
 *
 * @package    MetSalesCountdown
 * @subpackage Plugin Core
 * @since      1.0.0
 */
class Plugin
{

    use Singleton;

    /**
     * Plugin init method
     *
     * This method will initialize the plugin
     * @since      1.0.0
     * @access     plugin
     * @return     void
     */
    public function init()
    {

        if( !Dependency_check::instance()->init() ) return;

        add_action('init', [$this, 'i18n']);
        add_action('admin_init', [$this, 'admin_init']);

        // Load plugin assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Load files for admin dashboard
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

        // Load settings dashbaord menu
        Side_Menu::instance();

        // Load admin notices
        Admin_notice::instance();

        // Load Api categories
        new Get_Categories();

        // Load Plugin Features
        \MetSalesCountdown\Base\Features::instance()->init_features();

        // Load feature init
        new FeatureInit();
    }

    /**
     * Admin init
     *
     * This method will be fired while initializing the wp admin area.
     * @since      1.0.0
     * @access     plugin
     * @return     void
     */
    public function admin_init()
    {
    }

    /**
     * Load Textdomain
     *
     * Instantiate the plugin's textdomain
     * @since      1.0.0
     * @access     public
     * @return     void
     */
    public function i18n()
    {
        load_plugin_textdomain('met-sales-countdown', false, self::plugin_dir() . 'languages/');
    }

    /**
     * Enqueue Assets
     *
     * Load all the plugin css and js files.
     * @since      1.0.0
     * @access     plugin
     * @return     void
     */
    public function enqueue_assets()
    {

        wp_enqueue_style(
            'metsalescountdown-main-css',
            self::plugin_url() . 'public/assets/css/countdown.css',
            array(),
            self::version()
        );

        //load js files from plugin directory
        wp_enqueue_script(
            'metsalescountdown-lib-js',
            self::plugin_url() . 'public/assets/js/countdown-lib.js',
            array(),
            self::version(),
            true
        );

    }

    /**
     * Enqueue admin scripts
     *
     * @since      1.0.0
     * @access     public
     * @return     void
     */
    public function admin_enqueue_scripts($hook)
    {

        $pages_to_load_assets = array(
            'sales-timer_page_sales-countdown-time',
            'toplevel_page_met-sales-countdown',
        );

        if (!in_array($hook, $pages_to_load_assets)) {
            return;
        }

        wp_enqueue_style(
            'metsalescountdown-main-css',
            self::plugin_url() . 'public/assets/css/countdown.css',
            array(),
            self::version()
        );

        wp_enqueue_script('jquery-ui-accordion');

        wp_enqueue_style(
            'metsalescountdown-select2-css',
            self::plugin_url() . 'public/assets/css/select2.css',
            array(),
            self::version()
        );

        // load js files from plugin directory
        wp_enqueue_script(
            'metsalescountdown-select2-js',
            self::plugin_url() . 'public/assets/js/lib/select2.js',
            array(),
            self::version(),
            true
        );
        wp_enqueue_script(
            'metsalescountdown-lib-js',
            self::plugin_url() . 'public/assets/js/countdown-lib.js',
            array(),
            self::version(),
            true
        );
    }

    /**
     * Plugin Version
     *
     * @since 1.0.0
     * @return string The plugin version.
     */
    public static function version()
    {
        return '1.0.4';
    }

    /**
     * Plugin file plugins's root file.
     *
     * @return string
     * @since 1.0.0
     *
     */
    public static function plugin_file()
    {
        return __FILE__;
    }

    /**
     * Plugin url
     *
     * @return mixed
     * @since 1.0.0
     */
    public static function plugin_url()
    {
        return trailingslashit(plugin_dir_url(__FILE__));
    }

    /**
     * Plugin dir
     *
     * @return mixed
     * @since 1.0.0
     */
    public static function plugin_dir()
    {
        return trailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * Plugin core directory
     *
     * @return string
     * @since 1.0.0
     */
    public static function core_dir()
    {
        return self::plugin_dir() . 'core/';
    }

    /**
     * Plugin core url
     *
     * @return string
     * @since 1.0.0
     */
    public static function core_url()
    {
        return self::plugin_url() . 'core/';
    }

    /**
     * Plugin features directory
     *
     * @return string
     * @since 1.0.0
     */
    public static function features_dir()
    {
        return self::core_dir() . 'features/';
    }

    /**
     * Plugin features url
     *
     * @return string
     * @since 1.0.0
     */
    public static function features_url()
    {
        return self::plugin_url() . 'core/';
    }
}