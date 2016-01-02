<?php

/**
 * Mock hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Mock hook arg for using in the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Arg extends WordPoints_Hook_Arg {

	/**
	 * @since 1.0.0
	 */
	public $is_stateful = false;

	/**
	 * @since 1.0.0
	 */
	public $value = 1;

	/**
	 * @since 1.0.0
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return $this->get_entity()->get_title();
	}
}

// EOF
