<?php

/**
 * Mock entity context class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock entity context class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Entity_Context extends WordPoints_Entity_Context {

	/**
	 * The ID of the current context.
	 *
	 * @since 1.0.0
	 *
	 * @var string|int
	 */
	public $current_id;

	/**
	 * @since 1.0.0
	 */
	public function get_current_id() {
		return $this->current_id;
	}
}

// EOF