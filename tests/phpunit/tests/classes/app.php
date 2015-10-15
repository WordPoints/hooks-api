<?php

/**
 * Test case for WordPoints_App.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_App.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_App
 */
class WordPoints_App_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test getting a sub-app.
	 *
	 * @since 1.0.0
	 */
	public function test_get_sub_app() {

		$app = new WordPoints_App;

		$this->assertTrue(
			$app->sub_apps->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Object', $app->test );
	}

	/**
	 * Test getting a nonexistent a sub-app.
	 *
	 * @since 1.0.0
	 */
	public function test_get_nonexistent_sub_app() {

		$app = new WordPoints_App;

		$this->assertNull( $app->test );
	}
}

// EOF
