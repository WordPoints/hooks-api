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
	 * Test that it registers the expected entities.
	 *
	 * @since 1.0.0
	 */
	public function test_basic() {

		$this->mock_apps();

		$this->factory->wordpoints->post_type->create(
			array( 'name' => 'test', 'supports' => array( 'testing' ) )
		);

		$filter = 'wordpoints_register_post_type_entities';
		$mock = $this->listen_for_filter( $filter );

		wordpoints_register_post_type_entities( 'test' );

		$this->assertEquals( 1, $mock->call_count );
		$this->assertEquals( array( 'test' ), $mock->calls[0] );

		$entities = wordpoints_entities();
		$children = $entities->children;

		$this->assertTrue( $entities->is_registered( 'post\test' ) );
		$this->assertTrue( $children->is_registered( 'post\test', 'author' ) );
	}

	/**
	 * Test that it registers the content entity only when the editor is supported.
	 *
	 * @since 1.0.0
	 */
	public function test_supports_editor() {

		$this->mock_apps();

		$this->factory->wordpoints->post_type->create(
			array( 'name' => 'test', 'supports' => array( 'testing' ) )
		);

		wordpoints_register_post_type_entities( 'test' );

		$entities = wordpoints_entities();
		$children = $entities->children;

		$this->assertFalse( $children->is_registered( 'post\test', 'content' ) );

		add_post_type_support( 'test', 'editor' );

		wordpoints_register_post_type_entities( 'test' );

		$this->assertTrue( $children->is_registered( 'post\test', 'content' ) );
	}

	/**
	 * Test that it registers the comment entities only when comments are supported.
	 *
	 * @since 1.0.0
	 */
	public function test_supports_comments() {

		$this->mock_apps();

		$this->factory->wordpoints->post_type->create(
			array( 'name' => 'test', 'supports' => array() )
		);

		wordpoints_register_post_type_entities( 'test' );

		$entities = wordpoints_entities();
		$children = $entities->children;

		$this->assertFalse( $entities->is_registered( 'comment\test' ) );
		$this->assertFalse( $children->is_registered( 'comment\test', 'post\test' ) );
		$this->assertFalse( $children->is_registered( 'comment\test', 'author' ) );

		add_post_type_support( 'test', 'comments' );

		wordpoints_register_post_type_entities( 'test' );

		$this->assertTrue( $entities->is_registered( 'comment\test' ) );
		$this->assertTrue( $children->is_registered( 'comment\test', 'post\test' ) );
		$this->assertTrue( $children->is_registered( 'comment\test', 'author' ) );
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
		$children = $entities->children;

		$this->assertFalse( $children->is_registered( 'post\test', 'terms\post_tag' ) );

		register_taxonomy_for_object_type( 'post_tag', 'test' );

		wordpoints_register_post_type_entities( 'test' );

		$this->assertTrue( $children->is_registered( 'post\test', 'terms\post_tag' ) );
	}
}

// EOF
