<?php

/**
 * Test case for WordPoints_Hook_Reaction_Store.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reaction_Store.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reaction_Store
 */
class WordPoints_Hook_Reaction_Store_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test constructing class.
	 *
	 * @since 1.0.0
	 */
	public function test_construct() {

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$this->assertEquals( 'test_store', $store->get_slug() );
	}

	/**
	 * Test getting the context defaults to the current site.
	 *
	 * @since 1.0.0
	 */
	public function test_get_context_id() {

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$this->assertEquals(
			array( 'network' => 1, 'site' => 1 )
			, $store->get_context_id()
		);
	}

	/**
	 * Test getting a nonexistent reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_nonexistent() {

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$this->assertFalse( $store->get_reaction( 1 ) );
	}

	/**
	 * Test creating a reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction() {

		$this->mock_apps();

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$settings = array(
			'event' => 'test_event',
			'reactor' => $this->factory->wordpoints->hook_reactor->create(),
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $store->create_reaction( $settings );

		$this->assertIsReaction( $result );

		$reaction = $store->get_reaction( $result->ID );

		$this->assertEquals( $result, $reaction );

		$this->assertEquals( $settings['event'], $reaction->get_event_slug() );
		$this->assertEquals( $settings['reactor'], $reaction->get_reactor_slug() );
		$this->assertEquals( $settings['target'], $reaction->get_meta( 'target' ) );
	}

	/**
	 * Test creating a reaction with invalid settings.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_invalid_settings() {

		$this->mock_apps();

		$this->factory->wordpoints->hook_reactor->create();

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		// Event is missing.
		$settings = array(
			'reactor' => $this->factory->wordpoints->hook_reactor->create(),
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $store->create_reaction( $settings );

		$this->assertInstanceOf( 'WordPoints_Hook_Reaction_Validator', $result );
	}

	/**
	 * Test creating a reaction saves the reactor settings.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_saves_reactor_settings() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$settings = array(
			'event' => 'test_event',
			'reactor' => $reactor->get_slug(),
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $store->create_reaction( $settings );

		$this->assertIsReaction( $result );

		$this->assertCount( 1, $reactor->updates );
		$this->assertEquals( $result, $reactor->updates[0]['reaction'] );
		$this->assertArrayNotHasKey( 'event', $reactor->updates[0]['settings'] );
	}

	/**
	 * Test creating a reaction saves the extension settings.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_saves_extension_settings() {

		$this->mock_apps();

		$extensions = wordpoints_hooks()->extensions;
		$extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$settings = array(
			'event' => 'test_event',
			'reactor' => $this->factory->wordpoints->hook_reactor->create(),
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $store->create_reaction( $settings );

		$this->assertIsReaction( $result );

		$extension = $extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->updates );
		$this->assertEquals( $result, $extension->updates[0]['reaction'] );
		$this->assertArrayNotHasKey( 'event', $extension->updates[0]['settings'] );
	}

	/**
	 * Test creating a reaction calls an action hook.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction_calls_hook() {

		$this->mock_apps();

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$settings = array(
			'event' => 'test_event',
			'reactor' => $this->factory->wordpoints->hook_reactor->create(),
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$mock = new WordPoints_Mock_Filter();
		add_action( 'wordpoints_hook_reaction_save', array( $mock, 'action' ), 10, 5 );

		$result = $store->create_reaction( $settings );

		$this->assertIsReaction( $result );

		$this->assertEquals( 1, $mock->call_count );

		unset( $settings['event'] );

		$this->assertEquals( array( $result, $settings, true ), $mock->calls[0] );
	}

	/**
	 * Test updating a reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $reaction );

		$settings = array(
			'event' => 'test_event_2',
			'reactor' => $reaction->get_reactor_slug(),
			'target' => array( 'alias:test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create(
			array(
				'slug' => 'test_event_2',
				'args' => array(
					'alias:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$result = $reaction->store->update_reaction( $reaction->ID, $settings );

		$this->assertIsReaction( $result );

		$reaction = $reaction->store->get_reaction( $reaction->ID );

		$this->assertEquals( $result, $reaction );

		$this->assertEquals( $settings['event'], $reaction->get_event_slug() );
		$this->assertEquals( $settings['reactor'], $reaction->get_reactor_slug() );
		$this->assertEquals( $settings['target'], $reaction->get_meta( 'target' ) );
	}

	/**
	 * Test updating a nonexistent reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_not_exists() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $reaction );

		$reaction->store->delete_reaction( $reaction->ID );

		$settings = array(
			'event' => 'test_event_2',
			'reactor' => $reaction->get_reactor_slug(),
			'target' => array( 'alias:test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create(
			array(
				'slug' => 'test_event_2',
				'args' => array(
					'alias:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$result = $reaction->store->update_reaction( $reaction->ID, $settings );

		$this->assertFalse( $result );
	}

	/**
	 * Test updating a reaction with invalid settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_invalid_settings() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $reaction );

		// Event is missing.
		$settings = array(
			'reactor' => $reaction->get_reactor_slug(),
			'target' => array( 'alias:test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create(
			array(
				'slug' => 'test_event_2',
				'args' => array(
					'alias:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$result = $reaction->store->update_reaction( $reaction->ID, $settings );

		$this->assertInstanceOf( 'WordPoints_Hook_Reaction_Validator', $result );

		$this->assertEquals( 'test_event', $reaction->get_event_slug() );
		$this->assertEquals( array( 'test_entity' ), $reaction->get_meta( 'target' ) );
	}

	/**
	 * Test updating a reaction saves the reactor settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_saves_reactor_settings() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $reaction );

		$settings = array(
			'event' => 'test_event_2',
			'reactor' => $reaction->get_reactor_slug(),
			'target' => array( 'alias:test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create(
			array(
				'slug' => 'test_event_2',
				'args' => array(
					'alias:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$result = $reaction->store->update_reaction( $reaction->ID, $settings );

		$this->assertIsReaction( $result );

		$this->assertCount( 2, $reactor->updates );
		$this->assertEquals( $result, $reactor->updates[1]['reaction'] );
		$this->assertArrayNotHasKey( 'event', $reactor->updates[1]['settings'] );
	}

	/**
	 * Test updating a reaction saves the extension settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_saves_extension_settings() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $reaction );

		$extensions = wordpoints_hooks()->extensions;
		$extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$settings = array(
			'event' => 'test_event_2',
			'reactor' => $reaction->get_reactor_slug(),
			'target' => array( 'alias:test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create(
			array(
				'slug' => 'test_event_2',
				'args' => array(
					'alias:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$result = $reaction->store->update_reaction( $reaction->ID, $settings );

		$this->assertIsReaction( $result );

		$extension = $extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->updates );
		$this->assertEquals( $result, $extension->updates[0]['reaction'] );
		$this->assertArrayNotHasKey( 'event', $extension->updates[0]['settings'] );
	}

	/**
	 * Test updating a reaction calls an action hook.
	 *
	 * @since 1.0.0
	 */
	public function test_update_reaction_calls_hook() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $reaction );

		$settings = array(
			'event' => 'test_event_2',
			'reactor' => $reaction->get_reactor_slug(),
			'target' => array( 'alias:test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create(
			array(
				'slug' => 'test_event_2',
				'args' => array(
					'alias:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$mock = new WordPoints_Mock_Filter();
		add_action( 'wordpoints_hook_reaction_save', array( $mock, 'action' ), 10, 5 );

		$result = $reaction->store->update_reaction( $reaction->ID, $settings );

		$this->assertIsReaction( $result );

		$this->assertEquals( 1, $mock->call_count );

		unset( $settings['event'] );

		$this->assertEquals( array( $result, $settings, false ), $mock->calls[0] );
	}
}

// EOF
