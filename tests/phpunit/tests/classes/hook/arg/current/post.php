<?php

/**
 * Test case for WordPoints_Hook_Arg_Current_Post.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Arg_Current_Post.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Arg_Current_Post
 */
class WordPoints_Hook_Arg_Current_Post_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the arg value.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_Entity_Post'
		);

		$post = $this->factory->post->create_and_get();

		global $wp_query, $wp_the_query;
		$wp_query->queried_object = $post;
		$wp_query->queried_object_id = $post->ID;

		// Make this the main query.
		$wp_the_query = $wp_query;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test_action', array() );
		$arg = new WordPoints_Hook_Arg_Current_Post( 'test_entity', $action );

		$this->assertEquals( $post, $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_Entity_Post', $entity );

		$this->assertEquals( $post->ID, $entity->get_the_id() );
		$this->assertNotEmpty( $arg->get_title() );
	}

	/**
	 * Test getting the arg value when the current query isn't the main query.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value_not_main_query() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_Entity_Post'
		);

		$post = $this->factory->post->create_and_get();

		global $wp_query;
		$wp_query->queried_object = $post;
		$wp_query->queried_object_id = $post->ID;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test_action', array() );
		$arg = new WordPoints_Hook_Arg_Current_Post( 'test_entity', $action );

		$this->assertFalse( $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_Entity_Post', $entity );

		$this->assertNull( $entity->get_the_id() );
	}

	/**
	 * Test getting the arg value when the current query isn't for a post.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value_not_post() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_Entity_Post'
		);

		$user = $this->factory->user->create_and_get();

		global $wp_query, $wp_the_query;
		$wp_query->queried_object = $user;
		$wp_query->queried_object_id = $user->ID;

		// Make this the main query.
		$wp_the_query = $wp_query;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test_action', array() );
		$arg = new WordPoints_Hook_Arg_Current_Post( 'test_entity', $action );

		$this->assertFalse( $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_Entity_Post', $entity );

		$this->assertNull( $entity->get_the_id() );
	}
}

// EOF
