<?php

/**
 * Test case for the entities functions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the entities functions.
 *
 * @since 1.0.0
 */
class WordPoints_Entities_Functions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test initializing the API.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entities_app_init
	 */
	public function test_init() {

		$entities = new WordPoints_App_Registry( 'entities' );

		wordpoints_entities_app_init( $entities );

		$sub_apps = $entities->sub_apps;

		$this->assertTrue( $sub_apps->is_registered( 'children' ) );
	}

	/**
	 * Test getting the app.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entities
	 */
	public function test_get_app() {

		$this->mock_apps();

		$this->assertInstanceOf( 'WordPoints_App_Registry', wordpoints_entities() );
	}

	/**
	 * Test getting the app when the apps haven't been initialized.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entities
	 */
	public function test_get_app_not_initialized() {

		$this->mock_apps();

		WordPoints_App::$main = null;

		$this->assertInstanceOf( 'WordPoints_App_Registry', wordpoints_entities() );
	}

	/**
	 * Test the entity registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entities_init
	 */
	public function test_entities() {

		$this->mock_apps();

		$entities = new WordPoints_App_Registry( 'entities' );

		wordpoints_entities_init( $entities );

		$this->assertTrue( $entities->is_registered( 'post' ) );
		$this->assertTrue( $entities->is_registered( 'post_type' ) );
		$this->assertTrue( $entities->is_registered( 'comment' ) );
		$this->assertTrue( $entities->is_registered( 'user' ) );
		$this->assertTrue( $entities->is_registered( 'user_role' ) );
		$this->assertTrue( $entities->is_registered( 'term' ) );

		$children = $entities->children;

		$this->assertTrue( $children->is_registered( 'post', 'content' ) );
		$this->assertTrue( $children->is_registered( 'post_type', 'name' ) );
		$this->assertTrue( $children->is_registered( 'user_role', 'name' ) );
		$this->assertTrue( $children->is_registered( 'term', 'id' ) );

		$this->assertTrue( $children->is_registered( 'post', 'author' ) );
		$this->assertTrue( $children->is_registered( 'post', 'type' ) );
		$this->assertTrue( $children->is_registered( 'post', 'terms' ) );
		$this->assertTrue( $children->is_registered( 'user', 'roles' ) );
		$this->assertTrue( $children->is_registered( 'comment', 'post' ) );
		$this->assertTrue( $children->is_registered( 'comment', 'author' ) );
	}

	/**
	 * Test the entity user capability check function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entity_user_can_view
	 */
	public function test_user_can_view() {

		$this->mock_apps();

		$user_id = $this->factory->user->create();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$this->listen_for_filter( 'wordpoints_entity_user_can_view' );

		$this->assertTrue(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);

		$this->assertEquals(
			1
			, $this->filter_was_called( 'wordpoints_entity_user_can_view' )
		);

		add_filter( 'wordpoints_entity_user_can_view', '__return_false' );

		$this->assertFalse(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);
	}

	/**
	 * Test checking if an unregistered entity can be viewed.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entity_user_can_view
	 */
	public function test_user_can_view_not_registered() {

		$this->mock_apps();

		$user_id = $this->factory->user->create();

		$this->listen_for_filter( 'wordpoints_entity_user_can_view' );

		$this->assertFalse(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);

		$this->assertEquals(
			0
			, $this->filter_was_called( 'wordpoints_entity_user_can_view' )
		);
	}

	/**
	 * Test an entity that isn't an entity can be viewed.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entity_user_can_view
	 */
	public function test_user_can_view_not_entity() {

		$this->mock_apps();

		$user_id = $this->factory->user->create();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entityish'
		);

		$this->listen_for_filter( 'wordpoints_entity_user_can_view' );

		$this->assertFalse(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);

		$this->assertEquals(
			0
			, $this->filter_was_called( 'wordpoints_entity_user_can_view' )
		);
	}

	/**
	 * Test checking if a restricted entity can be viewed.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entity_user_can_view
	 */
	public function test_user_can_view_restricted() {

		$this->mock_apps();

		$user_id = $this->factory->user->create();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity_Restricted_Visibility'
		);

		$this->listen_for_filter( 'wordpoints_entity_user_can_view' );

		$this->assertTrue(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);

		$this->assertEquals(
			1
			, $this->filter_was_called( 'wordpoints_entity_user_can_view' )
		);

		add_filter( 'wordpoints_entity_user_can_view', '__return_false' );

		$this->assertFalse(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);
	}

	/**
	 * Test checking if a restricted entity can be viewed.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entity_user_can_view
	 */
	public function test_user_can_view_restricted_not_viewable() {

		$this->mock_apps();

		$user_id = $this->factory->user->create();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity_Restricted_Visibility'
		);

		WordPoints_PHPUnit_Mock_Entity_Restricted_Visibility::$can_view = false;

		$this->listen_for_filter( 'wordpoints_entity_user_can_view' );

		$this->assertFalse(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);

		$this->assertEquals(
			1
			, $this->filter_was_called( 'wordpoints_entity_user_can_view' )
		);

		add_filter( 'wordpoints_entity_user_can_view', '__return_true' );

		$this->assertTrue(
			wordpoints_entity_user_can_view( $user_id, 'test_entity', 1 )
		);
	}
}

// EOF
