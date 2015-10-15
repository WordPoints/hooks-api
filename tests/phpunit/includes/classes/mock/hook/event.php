<?php

/**
 * Mock hook event class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook event class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Event extends WordPoints_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return 'Mock Event Title';
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {
		return 'Mock event description.';
	}
}

// EOF
