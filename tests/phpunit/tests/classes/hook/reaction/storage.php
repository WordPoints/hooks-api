<?php

/**
 * Test case for WordPoints_Hook_Reaction_Storage.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reaction_Storage.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reaction_Storage
 */
class WordPoints_Hook_Reaction_Storage_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test constructing class.
	 *
	 * @since 1.0.0
	 */
	public function test_construct() {

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage( 'test', false );

		$this->assertEquals( 'test', $storage->get_reactor_slug() );
		$this->assertFalse( $storage->is_network_wide() );

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage( 'test', true );

		$this->assertTrue( $storage->is_network_wide() );
	}

	/**
	 * Test getting a nonexistent reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_nonexistent() {

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage( 'test', false );

		$this->assertFalse( $storage->get_reaction( 1 ) );
	}

	/**
	 * Test creating a reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_create_reaction() {

		$this->mock_apps();

		$this->factory->wordpoints->hook_reactor->create();

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test_reactor'
			, false
		);

		$settings = array(
			'event' => 'test_event',
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $storage->create_reaction( $settings );

		$this->assertIsReaction( $result );

		$reaction = $storage->get_reaction( $result->ID );

		$this->assertEquals( $result, $reaction );

		$this->assertEquals( $settings['event'], $reaction->get_event_slug() );
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

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test_reactor'
			, false
		);

		// Event is missing.
		$settings = array(
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $storage->create_reaction( $settings );

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

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test_reactor'
			, false
		);

		$settings = array(
			'event' => 'test_event',
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $storage->create_reaction( $settings );

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

		$this->factory->wordpoints->hook_reactor->create();

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test_reactor'
			, false
		);

		$settings = array(
			'event' => 'test_event',
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$result = $storage->create_reaction( $settings );

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

		$this->factory->wordpoints->hook_reactor->create();

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test_reactor'
			, false
		);

		$settings = array(
			'event' => 'test_event',
			'target' => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_event->create();

		$mock = new WordPoints_Mock_Filter();
		add_action( 'wordpoints_hook_reaction_save', array( $mock, 'action' ), 10, 5 );

		$result = $storage->create_reaction( $settings );

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

		$result = $reaction->storage->update_reaction( $reaction->ID, $settings );

		$this->assertIsReaction( $result );

		$reaction = $reaction->storage->get_reaction( $reaction->ID );

		$this->assertEquals( $result, $reaction );

		$this->assertEquals( $settings['event'], $reaction->get_event_slug() );
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

		$reaction->storage->delete_reaction( $reaction->ID );

		$settings = array(
			'event' => 'test_event_2',
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

		$result = $reaction->storage->update_reaction( $reaction->ID, $settings );

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

		$result = $reaction->storage->update_reaction( $reaction->ID, $settings );

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

		$result = $reaction->storage->update_reaction( $reaction->ID, $settings );

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

		$result = $reaction->storage->update_reaction( $reaction->ID, $settings );

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

		$result = $reaction->storage->update_reaction( $reaction->ID, $settings );

		$this->assertIsReaction( $result );

		$this->assertEquals( 1, $mock->call_count );

		unset( $settings['event'] );

		$this->assertEquals( array( $result, $settings, false ), $mock->calls[0] );
	}
}

// EOF
