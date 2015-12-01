<?php

/**
 * Test case for WordPoints_Entity_Term.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Term.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Term
 */
class WordPoints_Entity_Term_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the title when the taxonomy is known.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title() {

		$entity = new WordPoints_Entity_Term( 'term\post_tag' );

		$this->assertEquals( __( 'Tag' ), $entity->get_title() );
	}

	/**
	 * Test getting the title when the taxonomy is unknown.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title_nonexistent_taxonomy() {

		$entity = new WordPoints_Entity_Term( 'term\invalid' );

		$this->assertEquals( 'term\invalid', $entity->get_title() );
	}
}

// EOF
