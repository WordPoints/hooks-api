<?php

/**
 * Test case for WordPoints_Hook_Events.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Events.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Events
 */
class WordPoints_Hook_Events_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test that it calls an action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hook_events_init', array( $mock, 'action' ) );

		$events = new WordPoints_Hook_Events;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $events === $mock->calls[0][0] );
	}

	/**
	 * Test that it provides the expected sub-apps..
	 *
	 * @since 1.0.0
	 */
	public function test_sub_apps() {

		$events = new WordPoints_Hook_Events;

		$this->assertInstanceOf( 'WordPoints_Class_Registry_Children', $events->args );
	}

	/**
	 * Test registering an event requires the action and reverse_action args.
	 *
	 * @since 1.0.0
	 */
	public function test_register_requires_actions() {

		$events = new WordPoints_Hook_Events;

		$this->assertFalse(
			$events->register(
				'test'
				, 'WordPoints_PHPUnit_Mock_Hook_Event'
			)
		);

		$this->assertTrue(
			$events->register(
				'test'
				, 'WordPoints_PHPUnit_Mock_Hook_Event'
				, array(
					'actions' => array(
						'fire' => 'test_action',
					),
				)
			)
		);
	}

	/**
	 * Test registering an event registers the event with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_register_registers_event_with_router() {

		$hooks = $this->mock_apps()->hooks;

		$hooks->events->register(
			'test_event'
			, 'WordPoints_PHPUnit_Mock_Hook_Event'
			, array(
				'action' => 'test_action',
				'reverse_action' => 'test_reverse_action',
			)
		);

		$this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->factory->wordpoints->hook_reaction->create();

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );
	}

	/**
	 * Test deregistering an event deregisters the event with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister_deregisters_event_with_router() {

		$hooks = $this->mock_apps()->hooks;

		$hooks->events->register(
			'test_event'
			, 'WordPoints_PHPUnit_Mock_Hook_Event'
			, array(
				'action' => 'test_action',
				'reverse_action' => 'test_reverse_action',
			)
		);

		$this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->factory->wordpoints->hook_reaction->create();

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$hooks->events->deregister( 'test_event' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );
	}
}

// EOF
