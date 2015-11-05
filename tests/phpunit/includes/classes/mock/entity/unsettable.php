<?php

/**
 * Mock unsettable entity class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock unsettable entity class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Entity_Unsettable
	extends WordPoints_PHPUnit_Mock_Entity {

	/**
	 * @since 1.0.0
	 */
	public function set_the_value( $value ) {
		return false;
	}
}

// EOF
