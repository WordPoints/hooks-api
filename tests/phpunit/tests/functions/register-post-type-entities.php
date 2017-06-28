<?php

/**
 * Test case for the wordpoints_register_post_type_entities() function.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests wordpoints_register_post_type_entities().
 *
 * @since 1.0.0
 *
 * @covers ::wordpoints_register_post_type_entities
 */
class WordPoints_Functions_Register_Post_Type_Entities_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * @since 1.0.0
	 */
	public function tearDown() {

		parent::tearDown();

		_unregister_post_type( 'test' );
	}

	/**
	 * Test that it registers the term relationships for supported taxonomies.
	 *
	 * @since 1.0.0
	 */
	public function test_taxonomies() {

		$this->mock_apps();

		$this->factory->wordpoints->post_type->create(
			array( 'name' => 'test', 'supports' => array() )
		);

		wordpoints_register_post_type_entities( 'test' );

		$entities = wordpoints_entities();
		$children = $entities->get_sub_app( 'children' );

		$this->assertFalse( $children->is_registered( 'post\test', 'terms\post_tag' ) );

		register_taxonomy_for_object_type( 'post_tag', 'test' );

		wordpoints_register_post_type_entities( 'test' );

		$this->assertTrue( $children->is_registered( 'post\test', 'terms\post_tag' ) );
	}
}

// EOF
