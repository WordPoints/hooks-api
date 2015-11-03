<?php

/**
 * Test case for WordPoints_Hook_Router.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Router.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Router
 */
class WordPoints_Hook_Router_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test registering an action with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action() {

		$router = new WordPoints_Hook_Router;

		$router->add_action( 'test', array( 'action' => __METHOD__ ) );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test registering an action with a specific priority.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_priority() {

		$router = new WordPoints_Hook_Router;

		$router->add_action(
			'test'
			, array( 'action' => __METHOD__, 'priority' => 15 )
		);

		$this->assertEquals(
			15
			, has_action( __METHOD__, array( $router, __METHOD__ . ',15' ) )
		);
	}

	/**
	 * Test deregistering an action.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_action() {

		$router = new WordPoints_Hook_Router;

		$router->add_action( 'test', array( 'action' => __METHOD__ ) );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);

		$router->remove_action( 'test' );

		$this->assertFalse(
			has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test deregistering an action with a specific priority.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_action_priority() {

		$router = new WordPoints_Hook_Router;

		$router->add_action(
			'test'
			, array( 'action' => __METHOD__, 'priority' => 15 )
		);

		$this->assertEquals(
			15
			, has_action( __METHOD__, array( $router, __METHOD__ . ',15' ) )
		);

		$router->remove_action( 'test' );

		$this->assertFalse(
			has_action( __METHOD__, array( $router, __METHOD__ . ',15' ) )
		);
	}

	/**
	 * Test deregistering an action when others are still registered doesn't unhook
	 * the router.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_action_still_others() {

		$router = new WordPoints_Hook_Router;

		$router->add_action( 'test', array( 'action' => __METHOD__ ) );
		$router->add_action( 'test_2', array( 'action' => __METHOD__ ) );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);

		$router->remove_action( 'test' );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test routing an action with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$result = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $result );

		$hooks->firers->register( 'fire', 'WordPoints_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );
	}

	/**
	 * Test routing a nonexistent action with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_route_nonexistent_action() {

		$router = new WordPoints_Hook_Router;

		$this->assertNull( $router->{'action,10'}() );
	}

	/**
	 * Test that the first argument is returned, in case a filter is being routed.
	 *
	 * @since 1.0.0
	 */
	public function test_route_filter() {

		$router = new WordPoints_Hook_Router;

		$this->assertEquals( 'arg', $router->{'filter,10'}( 'arg' ) );
	}

	/**
	 * Test routing an action with no registered events.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_no_events() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 0, $firer->fires );
	}

	/**
	 * Test routing an action with an invalid action class.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_invalid_action() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array(
				'action' => __CLASS__,
				'class'  => 'WordPoints_PHPUnit_Mock_Object',
			)
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create();

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 0, $firer->fires );
	}

	/**
	 * Test routing an action for an unregistered event.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_unregistered_event() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$hooks->router->add_event_to_action( 'test_event', 'test_action' );

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 0, $firer->fires );
	}

	/**
	 * Test routing an action with an event with no args.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_no_event_args() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create();

		// Deregister the arg.
		$hooks->events->args->deregister_children( 'test_event' );

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 0, $firer->fires );
	}

	/**
	 * Test adding an action without specifying the action arg.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_no_action() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => null )
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create();

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 0, $firer->fires );
	}

	/**
	 * Test adding an action with a specific number of required args.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_arg_number() {

		$apps  = $this->mock_apps();
		$hooks = $apps->hooks;
		$entities = $apps->entities;

		$slug = $this->factory->wordpoints->hook_action->create(
			array(
				'action'     => __CLASS__,
				'arg_number' => 2,
				'data'   => array(
					'arg_index' => array(
						'1:test_entity' => 0,
						'2:test_entity' => 1,
					),
				),
			)
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create(
			array(
				'args' => array(
					'1:test_entity' => 'WordPoints_Hook_Arg',
					'2:test_entity' => 'WordPoints_Hook_Arg',
					'3:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$entities->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 1, $firer->fires );

		/** @var WordPoints_Entity[] $entities */
		$entities = $firer->fires[0]['event_args']->get_entities();

		$this->assertEquals( 1, $entities['1:test_entity']->get_the_value() );
		$this->assertEquals( 2, $entities['2:test_entity']->get_the_value() );
		$this->assertEquals( null, $entities['3:test_entity']->get_the_value() );
	}

	/**
	 * Test adding an action with an arg index determines the arg number from that.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_arg_number_from_index() {

		$apps  = $this->mock_apps();
		$hooks = $apps->hooks;
		$entities = $apps->entities;

		$slug = $this->factory->wordpoints->hook_action->create(
			array(
				'action' => __CLASS__,
				'data'   => array(
					'arg_index' => array(
						'1:test_entity' => 0,
						'2:test_entity' => 1,
					),
				),
			)
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create(
			array(
				'args' => array(
					'1:test_entity' => 'WordPoints_Hook_Arg',
					'2:test_entity' => 'WordPoints_Hook_Arg',
					'3:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$entities->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 1, $firer->fires );

		/** @var WordPoints_Entity[] $entities */
		$entities = $firer->fires[0]['event_args']->get_entities();

		$this->assertEquals( 1, $entities['1:test_entity']->get_the_value() );
		$this->assertEquals( 2, $entities['2:test_entity']->get_the_value() );
		$this->assertEquals( null, $entities['3:test_entity']->get_the_value() );
	}

	/**
	 * Test adding an action with requirements determines the arg number from that.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_arg_number_from_requirements() {

		$apps  = $this->mock_apps();
		$hooks = $apps->hooks;
		$entities = $apps->entities;

		$slug = $this->factory->wordpoints->hook_action->create(
			array(
				'action' => __CLASS__,
				'data'   => array(
					'arg_index' => array(
						'1:test_entity' => 0,
					),
					'requirements' => array(
						1 => 2,
					),
				),
			)
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create(
			array(
				'args' => array(
					'1:test_entity' => 'WordPoints_Hook_Arg',
					'2:test_entity' => 'WordPoints_Hook_Arg',
					'3:test_entity' => 'WordPoints_Hook_Arg',
				),
			)
		);

		$entities->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$hooks->firers->register( 'fire', 'WordPoints_PHPUnit_Mock_Hook_Firer' );

		do_action( __CLASS__, 1, 0, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Firer $firer */
		$firer = $hooks->firers->get( 'fire' );

		$this->assertCount( 0, $firer->fires );

		do_action( __CLASS__, 1, 2, 3 );

		$this->assertCount( 1, $firer->fires );

		/** @var WordPoints_Entity[] $entities */
		$entities = $firer->fires[0]['event_args']->get_entities();

		$this->assertEquals( 1, $entities['1:test_entity']->get_the_value() );
		$this->assertEquals( null, $entities['2:test_entity']->get_the_value() );
		$this->assertEquals( null, $entities['3:test_entity']->get_the_value() );
	}
}

// EOF
