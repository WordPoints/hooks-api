<?php

/**
 * Test case for WordPoints_Entity_Context_Network.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Context_Network.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Context_Network
 */
class WordPoints_Entity_Context_Network_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test getting the current context identifier on a non-multisite install.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress !multisite
	 */
	public function test_get_current_id() {

		$context = new WordPoints_Entity_Context_Network( 'site' );

		$this->assertEquals( 1, $context->get_current_id() );
	}

	/**
	 * Test getting the current context identifier on a multisite install.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_get_current_id_multisite() {

		$context = new WordPoints_Entity_Context_Network( 'site' );

		$this->assertEquals(
			$GLOBALS['current_site']->id
			, $context->get_current_id()
		);
	}
}

// EOF
