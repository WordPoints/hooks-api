<?php

/**
 * Mock hook action class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook action class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Action extends WordPoints_Hook_Action {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_action';

	/**
	 * @since 1.0.0
	 */
	protected $arg_index = array( 0 => 'test_entity' );

	/**
	 * Set a protected property's value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $var   The property name.
	 * @param mixed  $value The property value.
	 */
	public function set( $var, $value ) {
		$this->$var = $value;
	}
}

// EOF
