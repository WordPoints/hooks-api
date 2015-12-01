<?php

/**
 * Mock dynamic hook event class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock dynamic hook event class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Event_Dynamic extends WordPoints_Hook_Event_Dynamic {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test';

	/**
	 * @since 1.0.0
	 */
	protected $generic_entity_slug = 'generic';

	/**
	 * @since 1.0.0
	 */
	public function get_entity_title() {
		return parent::get_entity_title();
	}

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
