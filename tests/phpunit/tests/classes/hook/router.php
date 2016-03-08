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

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$result = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $result );

		do_action( __CLASS__, 1, 2, 3 );

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

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 0, $router->fires );
	}

	/**
	 * Test routing an action with an invalid action class.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_invalid_action() {

		$this->mock_apps();
		
		$hooks = wordpoints_hooks();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

		$slug = $this->factory->wordpoints->hook_action->create(
			array(
				'action' => __CLASS__,
				'class'  => 'WordPoints_PHPUnit_Mock_Object',
			)
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create();

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 0, $router->fires );
	}

	/**
	 * Test routing an action for an unregistered event.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_unregistered_event() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$hooks->router->add_event_to_action( 'test_event', 'test_action' );

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 0, $router->fires );
	}

	/**
	 * Test routing an action with an event with no args.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action_no_event_args() {

		$this->mock_apps();
		
		$hooks = wordpoints_hooks();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create();

		// Deregister the arg.
		$hooks->events->args->deregister_children( 'test_event' );

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 0, $router->fires );
	}

	/**
	 * Test adding an action without specifying the action arg.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_no_action() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => null )
		);

		$this->assertEquals( 'test_action', $slug );

		$this->factory->wordpoints->hook_event->create();

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 0, $router->fires );
	}

	/**
	 * Test adding an action with a specific number of required args.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_arg_number() {

		$this->mock_apps();
		$hooks = wordpoints_hooks();
		$entities = wordpoints_entities();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

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

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 1, $router->fires );

		/** @var WordPoints_Hook_Event_Args $event_args */
		$event_args = $router->fires[0]['event_args'];
		$entities   = $event_args->get_entities();

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

		$this->mock_apps();
		$hooks = wordpoints_hooks();
		$entities = wordpoints_entities();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

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

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 1, $router->fires );

		/** @var WordPoints_Hook_Event_Args $event_args */
		$event_args = $router->fires[0]['event_args'];
		$entities   = $event_args->get_entities();

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

		$this->mock_apps();
		$hooks = wordpoints_hooks();
		$entities = wordpoints_entities();

		$hooks->sub_apps->register( 'router', 'WordPoints_PHPUnit_Mock_Hook_Router' );

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

		do_action( __CLASS__, 1, 0, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Router $router */
		$router = $hooks->router;

		$this->assertCount( 0, $router->fires );

		do_action( __CLASS__, 1, 2, 3 );

		$this->assertCount( 1, $router->fires );

		/** @var WordPoints_Hook_Event_Args $event_args */
		$event_args = $router->fires[0]['event_args'];
		$entities   = $event_args->get_entities();

		$this->assertEquals( 1, $entities['1:test_entity']->get_the_value() );
		$this->assertEquals( null, $entities['2:test_entity']->get_the_value() );
		$this->assertEquals( null, $entities['3:test_entity']->get_the_value() );
	}

	/**
	 * Test firing an event.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create();
		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$this->fire_event();

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 3, $extension->hit_checks );
		$this->assertCount( 3, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 3, $extension->hit_checks );
		$this->assertCount( 3, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reactions[0]->ID ) );
		$this->assertHitsLogged( array( 'reaction_id' => $reactions[1]->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'reactor' => 'another', 'reaction_id' => $other_reaction->ID )
		);
	}

	/**
	 * Test firing an event when no reactors are registered.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event_no_reactors() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->fire_event();

		// The extensions should not have been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );
	}

	/**
	 * Test firing an event when one reactor doesn't have any reactions for it.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event_no_reactions() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create();
		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$this->fire_event();

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reactions[0]->ID ) );
		$this->assertHitsLogged( array( 'reaction_id' => $reactions[1]->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->hits );
	}

	/**
	 * Test firing an event when there are no extensions.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event_no_extensions() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$this->factory->wordpoints->hook_reactor->create();
		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$this->fire_event();

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reactions[0]->ID ) );
		$this->assertHitsLogged( array( 'reaction_id' => $reactions[1]->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $other_reaction->ID ) );
	}

	/**
	 * Test firing an event when a reaction has invalid settings.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event_invalid_reaction() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create();

		$this->factory->wordpoints->hook_reaction->create(
			array( 'test_extension' => array( 'fail' => true ) )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$this->fire_event();

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $other_reaction->ID ) );
	}

	/**
	 * Test firing an event that an extension aborts.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event_extension_aborted() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create();
		$this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$extension = $hooks->extensions->get( 'test_extension' );
		$extension->should_hit = false;

		$this->fire_event();

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 3, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->hits );
	}

	/**
	 * Test firing an event twice will hit twice.
	 *
	 * @since 1.0.0
	 */
	public function test_fire_event_twice() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create();
		$this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->entity->create();

		$this->fire_event();

		// The extensions should have been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->hit_checks );
		$this->assertCount( 1, $extension->hits );

		$another_extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $another_extension->hit_checks );
		$this->assertCount( 1, $another_extension->hits );

		// The reactor should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		// Fire the event again.
		$this->fire_event();

		// The extension should have been checked a second time.
		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		$this->assertCount( 2, $another_extension->hit_checks );
		$this->assertCount( 2, $another_extension->hits );

		// The reactor should have been hit a second time.
		$this->assertCount( 2, $reactor->hits );
	}

	/**
	 * Fire an event.
	 *
	 * @since 1.0.0
	 */
	public function fire_event() {

		$args = new WordPoints_Hook_Event_Args( array() );

		$router = new WordPoints_PHPUnit_Mock_Hook_Router;
		$router->fire_event( 'test_firer', 'test_event', $args );
	}
}

// EOF
