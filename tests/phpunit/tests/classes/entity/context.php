<?php

/**
 * Test case for WordPoints_Entity_Context.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Context.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Context
 */
class WordPoints_Entity_Context_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test getting the slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug() {

		$context = new WordPoints_PHPUnit_Mock_Entity_Context( 'test' );

		$this->assertEquals( 'test', $context->get_slug() );
	}
}

// EOF
