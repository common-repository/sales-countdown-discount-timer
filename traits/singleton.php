<?php

namespace MetSalesCountdown\Traits;

/**
 * Description: Singleton trait.
 *
 * @package    MetSalesCountdown\Traits
 * @subpackage Plugin Core
 * @since      1.0.0
 */
trait Singleton {

	private static $instance;


	/**
	 * Get the instance of the class.
	 *
	 * @return static The instance of the class.
	 */
	public static function instance() {
		if(!self::$instance) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}