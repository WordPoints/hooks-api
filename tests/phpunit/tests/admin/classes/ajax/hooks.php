<?php

/**
 * Test case for WordPoints_Admin_Ajax_Hooks.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the WordPoints_Admin_Ajax_Hooks class.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Admin_Ajax_Hooks
 */
class WordPoints_Admin_Ajax_Hooks_Test extends WordPoints_PHPUnit_TestCase_Ajax {

	/**
	 * @since 1.0.0
	 */
	public function setUp() {

		parent::setUp();

		new WordPoints_Admin_Ajax_Hooks();
	}

	/**
	 * Test preparing a hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_prepare_hook_reaction() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$reaction->add_meta( 'test', 'value' );

		$reaction_guid = wp_json_encode( $reaction->get_guid() );

		$this->assertEquals(
			array(
				'id' => $reaction->ID,
				'event' => $reaction->get_event_slug(),
				'reactor' => $reaction->get_reactor_slug(),
				'nonce' => wp_create_nonce(
					"wordpoints_update_hook_reaction|{$reaction_guid}"
				),
				'delete_nonce' => wp_create_nonce(
					"wordpoints_delete_hook_reaction|{$reaction_guid}"
				),
				'test' => 'value',
				'target' => array( 'test_entity' ),
			)
			, WordPoints_Admin_Ajax_Hooks::prepare_hook_reaction( $reaction )
		);
	}

	/**
	 * Test creating a hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reactor_slug = $reactor->get_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_create_nonce( $reactor );
		$_POST['reactor'] = $reactor_slug;
		$_POST['event']   = $this->factory->wordpoints->hook_event->create();
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$response = $this->assertJSONSuccessResponse(
			'wordpoints_admin_create_hook_reaction'
		);

		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertInternalType( 'object', $response->data );

		$this->assertObjectHasAttribute( 'id', $response->data );

		$this->assertObjectHasAttribute( 'event', $response->data );
		$this->assertEquals( $_POST['event'], $response->data->event );

		$this->assertObjectHasAttribute( 'reactor', $response->data );
		$this->assertEquals( $_POST['reactor'], $response->data->reactor );

		$this->assertObjectHasAttribute( 'target', $response->data );
		$this->assertEquals( $_POST['target'], $response->data->target );

		$reaction = $reactor->reactions->get_reaction( $response->data->id );
		$reaction_guid = wp_json_encode( $reaction->get_guid() );

		$this->assertObjectHasAttribute( 'nonce', $response->data );
		$this->assertEquals(
			wp_create_nonce(
				"wordpoints_update_hook_reaction|{$reaction_guid}"
			)
			, $response->data->nonce
		);

		$this->assertObjectHasAttribute( 'delete_nonce', $response->data );
		$this->assertEquals(
			wp_create_nonce(
				"wordpoints_delete_hook_reaction|{$reaction_guid}"
			)
			, $response->data->delete_nonce
		);
	}

	/**
	 * Test creating a hook reaction requires the correct capabilities.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction_not_admin() {

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_create_nonce( $reactor );
		$_POST['reactor'] = $reactor->get_slug();
		$_POST['event']   = $this->factory->wordpoints->hook_event->create();
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$this->assertJSONErrorResponse( 'wordpoints_admin_create_hook_reaction' );
	}

	/**
	 * Test creating a hook reaction requires a valid nonce.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction_no_nonce() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$_POST['reactor'] = $this->factory->wordpoints->hook_reactor->create();
		$_POST['event']   = $this->factory->wordpoints->hook_event->create();
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$this->assertJSONErrorResponse( 'wordpoints_admin_create_hook_reaction' );
	}

	/**
	 * Test creating a hook reaction requires a valid nonce.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction_invalid_nonce() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$_POST['nonce']   = 'invalid';
		$_POST['reactor'] = $this->factory->wordpoints->hook_reactor->create();
		$_POST['event']   = $this->factory->wordpoints->hook_event->create();
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$this->assertJSONErrorResponse( 'wordpoints_admin_create_hook_reaction' );
	}

	/**
	 * Test creating a hook reaction requires a valid reactor slug.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction_no_reactor_slug() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_create_nonce(
			$this->factory->wordpoints->hook_reactor->create_and_get()
		);
		$_POST['event']   = $this->factory->wordpoints->hook_event->create();
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$this->assertJSONErrorResponse( 'wordpoints_admin_create_hook_reaction' );
	}

	/**
	 * Test creating a hook reaction requires a valid reactor slug.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction_invalid_reactor_slug() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_create_nonce(
			$this->factory->wordpoints->hook_reactor->create_and_get()
		);
		$_POST['reactor'] = 'invalid';
		$_POST['event']   = $this->factory->wordpoints->hook_event->create();
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$this->assertJSONErrorResponse( 'wordpoints_admin_create_hook_reaction' );
	}

	/**
	 * Test creating a hook reaction requires valid reaction settings.
	 *
	 * @since 1.0.0
	 */
	public function test_create_hook_reaction_invalid_reaction_settings() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_create_nonce( $reactor );
		$_POST['reactor'] = $reactor->get_slug();
		$_POST['event']   = 'invalid';
		$_POST['target']  = array( $this->factory->wordpoints->entity->create() );

		$response = $this->assertJSONErrorResponse(
			'wordpoints_admin_create_hook_reaction'
		);

		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'errors', $response->data );
		$this->assertEquals(
			array(
				(object) array(
					'message' => 'Event is invalid.',
					'field' => array( 'event' ),
				),
			)
			, $response->data->errors
		);
	}

	/**
	 * Test updating a hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$response = $this->assertJSONSuccessResponse(
			'wordpoints_admin_update_hook_reaction'
		);

		$this->assertObjectNotHasAttribute( 'data', $response );

		$this->assertEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires the correct capabilities.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_not_admin() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires a valid nonce.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_no_nonce() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires a valid nonce.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_invalid_nonce() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = 'invalid';
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires a valid reaction ID.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_no_reaction_id() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires a valid reaction ID.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_invalid_reaction_id() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['id']      = $reaction->ID + 1;
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires a valid reactor slug.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_no_reactor_slug() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires a valid reactor slug.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_invalid_reactor_slug() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = 'invalid';
		$_POST['event']   = $reaction->get_event_slug();
		$_POST['target']  = array( 'current:test_entity' );

		$this->assertJSONErrorResponse( 'wordpoints_admin_update_hook_reaction' );

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test updating a hook reaction requires valid reaction settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_hook_reaction_invalid_reaction_settings() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		wordpoints_hooks()->events->args->register(
			$reaction->get_event_slug()
			, 'current:test_entity'
			, 'WordPoints_Hook_Arg'
		);

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_update_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reaction->get_reactor_slug();
		$_POST['event']   = 'invalid';
		$_POST['target']  = array( 'current:test_entity' );

		$response = $this->assertJSONErrorResponse(
			'wordpoints_admin_update_hook_reaction'
		);

		$this->assertObjectHasAttribute( 'data', $response );
		$this->assertObjectHasAttribute( 'errors', $response->data );
		$this->assertEquals(
			array(
				(object) array(
					'message' => 'Event is invalid.',
					'field' => array( 'event' ),
				),
			)
			, $response->data->errors
		);

		$this->assertNotEquals(
			array( 'current:test_entity' )
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test deleting a hook reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_delete_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reactor_slug;

		$this->assertJSONSuccessResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertFalse( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires the correct capabilities.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_not_admin() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_delete_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reactor_slug;

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires a valid nonce.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_no_nonce() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reactor_slug;

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires a valid nonce.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_invalid_nonce() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = 'invalid';
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = $reactor_slug;

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires a valid reaction ID.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_no_reaction_id() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_delete_nonce( $reaction );
		$_POST['reactor'] = $reactor_slug;

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires a valid reaction ID.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_invalid_reaction_id() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_delete_nonce( $reaction );
		$_POST['id']      = $reaction->ID + 1;
		$_POST['reactor'] = $reactor_slug;

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires a valid reactor slug.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_no_reactor_slug() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_delete_nonce( $reaction );
		$_POST['id']      = $reaction->ID;

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}

	/**
	 * Test deleting a hook reaction requires a valid reactor slug.
	 *
	 * @since 1.0.0
	 */
	public function test_delete_hook_reaction_invalid_reactor_slug() {

		$this->_setRole( 'administrator' );

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor_slug = $reaction->get_reactor_slug();

		$_POST['nonce']   = WordPoints_Admin_Ajax_Hooks::get_delete_nonce( $reaction );
		$_POST['id']      = $reaction->ID;
		$_POST['reactor'] = 'invalid';

		$this->assertJSONErrorResponse( 'wordpoints_admin_delete_hook_reaction' );

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $reactor_slug );

		$this->assertTrue( $reactor->reactions->reaction_exists( $reaction->ID ) );
	}
}

// EOF
