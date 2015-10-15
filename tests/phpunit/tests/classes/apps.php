<?php

/**
 * Test case for WordPoints_Apps.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Apps.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Apps
 */
class WordPoints_Apps_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test that it calls the wordpoints_apps_init action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_apps_init', array( $mock, 'action' ) );

		$app = new WordPoints_Apps;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $app->sub_apps === $mock->calls[0][0] );
	}
}

// EOF
