<?php

/**
 * Test case for WordPoints_Entity_Post_Terms.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Post_Terms.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Post_Terms
 */
class WordPoints_Entity_Post_Terms_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the title when the taxonomy is known.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title() {

		$relationship = new WordPoints_Entity_Post_Terms( 'terms\post_tag' );

		$this->assertEquals( __( 'Tags' ), $relationship->get_title() );
	}

	/**
	 * Test getting the title when the taxonomy is unknown.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title_nonexistent_taxonomy() {

		$relationship = new WordPoints_Entity_Post_Terms( 'terms\invalid' );

		$this->assertEquals(
			'term\invalid{}'
			, $relationship->get_title()
		);
	}
}

// EOF
