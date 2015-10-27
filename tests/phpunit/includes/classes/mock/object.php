<?php

/**
 * A class that can be used in the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Mock object to be used in the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Object {

	/**
	 * The method calls made on this object.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $calls = array();

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->__call( __FUNCTION__, func_get_args() );
	}

	/**
	 * Record method calls on this object.
	 *
	 * @since 1.0.0
	 */
	public function __call( $name, $arguments ) {
		$this->calls[] = array( 'name' => $name, 'arguments' => $arguments );
	}
}

// EOF
