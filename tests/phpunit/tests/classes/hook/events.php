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
	 * Test that it provides the expected sub-apps.
	 *
	 * @since 1.0.0
	 */
	public function test_sub_apps() {

		$events = new WordPoints_Hook_Events( 'test' );

		$this->assertInstanceOf( 'WordPoints_Class_Registry_Children', $events->args );
	}

	/**
	 * Test registering an event requires the action and reverse_action args.
	 *
	 * @since 1.0.0
	 */
	public function test_register_requires_actions() {

		$events = new WordPoints_Hook_Events( 'test' );

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

		/** @var WordPoints_Hooks $hooks */
		$hooks = $this->mock_apps()->hooks;

		$this->factory->wordpoints->hook_event->create();

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

		/** @var WordPoints_Hooks $hooks */
		$hooks = $this->mock_apps()->hooks;

		$this->factory->wordpoints->hook_event->create();

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

	/**
	 * Test registering an event registers the args.
	 *
	 * @since 1.0.0
	 */
	public function test_register_registers_args() {

		/** @var WordPoints_Hooks $hooks */
		$hooks = $this->mock_apps()->hooks;

		$this->factory->wordpoints->hook_event->create(
			array(
				'args' => array(
					'test_arg' => 'WordPoints_PHPUnit_Mock_Hook_Arg',
				),
			)
		);

		$this->assertTrue(
			$hooks->events->args->is_registered( 'test_event', 'test_arg' )
		);

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Hook_Arg'
			, $hooks->events->args->get( 'test_event', 'test_arg' )
		);
	}

	/**
	 * Test deregistering an event deregisters the args.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister_deregisters_arg() {

		/** @var WordPoints_Hooks $hooks */
		$hooks = $this->mock_apps()->hooks;

		$this->factory->wordpoints->hook_event->create(
			array(
				'args' => array(
					'test_arg' => 'WordPoints_PHPUnit_Mock_Hook_Arg',
				),
			)
		);

		$this->assertTrue(
			$hooks->events->args->is_registered( 'test_event', 'test_arg' )
		);

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Hook_Arg'
			, $hooks->events->args->get( 'test_event', 'test_arg' )
		);

		$hooks->events->deregister( 'test_event' );

		$this->assertFalse(
			$hooks->events->args->is_registered( 'test_event', 'test_arg' )
		);

		$this->assertFalse( $hooks->events->args->get( 'test_event', 'test_arg' ) );
	}

	/**
	 * Test deregistering an unregistered event works without error.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister_unregistered() {

		/** @var WordPoints_Hooks $hooks */
		$hooks = $this->mock_apps()->hooks;

		$this->assertFalse(
			$hooks->events->args->is_registered( 'test_event', 'test_arg' )
		);

		$hooks->events->deregister( 'test_event' );

		$this->assertFalse(
			$hooks->events->args->is_registered( 'test_event', 'test_arg' )
		);
	}
}

// EOF
