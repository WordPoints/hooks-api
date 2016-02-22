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
		$this->assertTrue( $sub_apps->is_registered( 'contexts' ) );
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

		$entities = wordpoints_entities();

		$filter = 'wordpoints_register_entities_for_post_types';
		$this->listen_for_filter( $filter );

		$filter_2 = 'wordpoints_register_entities_for_taxonomies';
		$this->listen_for_filter( $filter_2 );

		wordpoints_entities_init( $entities );

		$this->assertEquals( 1, $this->filter_was_called( $filter ) );
		$this->assertEquals( 1, $this->filter_was_called( $filter_2 ) );

		$children = $entities->children;

		$this->assertTrue( $entities->is_registered( 'post\post' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'content' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'author' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'terms\post_tag' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'terms\category' ) );
		$this->assertTrue( $children->is_registered( 'post\post', 'terms\post_format' ) );

		$this->assertTrue( $entities->is_registered( 'comment\post' ) );
		$this->assertTrue( $children->is_registered( 'comment\post', 'post\post' ) );
		$this->assertTrue( $children->is_registered( 'comment\post', 'author' ) );

		$this->assertTrue( $entities->is_registered( 'term\post_tag' ) );
		$this->assertTrue( $children->is_registered( 'term\post_tag', 'id' ) );

		$this->assertTrue( $entities->is_registered( 'term\category' ) );
		$this->assertTrue( $children->is_registered( 'term\category', 'id' ) );

		$this->assertTrue( $entities->is_registered( 'term\post_format' ) );
		$this->assertTrue( $children->is_registered( 'term\post_format', 'id' ) );

		$this->assertTrue( $entities->is_registered( 'post\page' ) );
		$this->assertTrue( $children->is_registered( 'post\page', 'content' ) );
		$this->assertTrue( $children->is_registered( 'post\page', 'author' ) );

		$this->assertTrue( $entities->is_registered( 'comment\page' ) );
		$this->assertTrue( $children->is_registered( 'comment\page', 'post\page' ) );
		$this->assertTrue( $children->is_registered( 'comment\page', 'author' ) );

		$this->assertTrue( $entities->is_registered( 'post\attachment' ) );
		$this->assertTrue( $children->is_registered( 'post\attachment', 'author' ) );

		$this->assertTrue( $entities->is_registered( 'comment\attachment' ) );
		$this->assertTrue( $children->is_registered( 'comment\attachment', 'post\attachment' ) );
		$this->assertTrue( $children->is_registered( 'comment\attachment', 'author' ) );

		$this->assertTrue( $entities->is_registered( 'user' ) );
		$this->assertTrue( $children->is_registered( 'user', 'roles' ) );

		$this->assertTrue( $entities->is_registered( 'user_role' ) );
		$this->assertTrue( $children->is_registered( 'user_role', 'name' ) );
	}

	/**
	 * Test that it registers the expected entities.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_register_taxonomy_entities
	 */
	public function test_register_taxonomy_entities() {

		$this->mock_apps();

		$filter = 'wordpoints_register_taxonomy_entities';
		$mock = $this->listen_for_filter( $filter );

		wordpoints_register_taxonomy_entities( 'post_tag' );

		$this->assertEquals( 1, $mock->call_count );
		$this->assertEquals( array( 'post_tag' ), $mock->calls[0] );

		$entities = wordpoints_entities();
		$children = $entities->children;

		$this->assertTrue( $entities->is_registered( 'term\post_tag' ) );
		$this->assertTrue( $children->is_registered( 'term\post_tag', 'id' ) );
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

		$entity_slug = $this->factory->wordpoints->entity->create();

		$this->listen_for_filter( 'wordpoints_entity_user_can_view' );

		$this->assertTrue(
			wordpoints_entity_user_can_view( $user_id, $entity_slug, 1 )
		);

		$this->assertEquals(
			1
			, $this->filter_was_called( 'wordpoints_entity_user_can_view' )
		);

		add_filter( 'wordpoints_entity_user_can_view', '__return_false' );

		$this->assertFalse(
			wordpoints_entity_user_can_view( $user_id, $entity_slug, 1 )
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

	/**
	 * Test the entity context registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_entity_contexts_init
	 */
	public function test_contexts() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$contexts = $entities->contexts;

		wordpoints_entity_contexts_init( $contexts );

		$this->assertTrue( $contexts->is_registered( 'network' ) );
		$this->assertTrue( $contexts->is_registered( 'site' ) );
	}
}

// EOF
