<?php

/**
 * Test case for WordPoints_Entity_Post.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Post.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Post
 */
class WordPoints_Entity_Post_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the title when the post type is known.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title() {

		$entity = new WordPoints_Entity_Post( 'post\page' );

		$this->assertEquals( __( 'Page' ), $entity->get_title() );
	}

	/**
	 * Test getting the title when the post type is unknown.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title_nonexistent_post_type() {

		$entity = new WordPoints_Entity_Post( 'post\invalid' );

		$this->assertEquals( 'post\invalid', $entity->get_title() );
	}
}

// EOF
