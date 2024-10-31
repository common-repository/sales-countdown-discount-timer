<?php
namespace MetSalesCountdown;

defined( 'ABSPATH' ) || exit;

/**
 * MetSalesCountdown autoloader.
 * 
 * Handles dynamically loading classes only when needed all the classes with MetSalesCountdown namespace.
 *
 * @since 1.0.0
 */
class Autoloader {
    
	/**
     * Autoloader constructor
     *
     * @since 1.0.0
     * 
     * @return void
     */
	public static function run() {
		spl_autoload_register( [ __CLASS__, 'autoload' ] );
    }
    
    /**
	 * Autoload Classes.
     * 
	 * For a given class, check if it exist and load it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $class Class name.
	 */
	private static function autoload( $class_name ) {

        // If the class being requested does not start with our prefix
        // we know it's not one in our project.
        if ( 0 !== strpos( $class_name, __NAMESPACE__ ) ) {
            return;
        }
        
        $file_name = strtolower(
            preg_replace(
                [ '/\b'.__NAMESPACE__.'\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
                [ '', '$1-$2', '-', DIRECTORY_SEPARATOR],
                $class_name
            )
        );

        // Compile our path from the corosponding location.
        $file = plugin_dir_path(__FILE__) . $file_name . '.php';
        
        // If a file is found.
        if ( file_exists( $file ) ) {
            // Then load it up!
            require_once( $file );
        }
    }
}

// Run the autoloader
Autoloader::run();