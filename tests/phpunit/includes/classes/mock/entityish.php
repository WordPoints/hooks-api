<?php

/**
 * Mock entityish class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock entityish class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Entityish extends WordPoints_Entityish {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_entity';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return 'Mock Entityish';
	}
}

// EOF
