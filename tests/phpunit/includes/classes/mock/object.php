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

	public $calls = array();

	public function __construct() {
		$this->__call( '__construct', func_get_args() );
	}

	public function __call( $name, $arguments ) {
		$this->calls[] = array( 'name' => $name, 'arguments' => $arguments );
	}
}

// EOF
