<?php

/**
 * Test case for WordPoints_Hook_Reactor_Points_Legacy.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reactor_Points_Legacy.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reactor_Points_Legacy
 */
class WordPoints_Hook_Reactor_Points_Legacy_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test updating the settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_settings() {

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();

		$this->create_points_type();

		$settings = array(
			'target'          => array( 'user' ),
			'points'          => 10,
			'points_type'     => 'points',
			'description'     => 'Testing.',
			'log_text'        => 'Testing.',
			'legacy_log_type' => 'test',
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create_and_get();

		$this->assertIsReaction( $reaction );

		$reactor->update_settings( $reaction, $settings );

		$this->assertEquals( $settings['target'], $reaction->get_meta( 'target' ) );
		$this->assertEquals( $settings['points'], $reaction->get_meta( 'points' ) );
		$this->assertEquals( $settings['points_type'], $reaction->get_meta( 'points_type' ) );
		$this->assertEquals( $settings['description'], $reaction->get_meta( 'description' ) );
		$this->assertEquals( $settings['log_text'], $reaction->get_meta( 'log_text' ) );
		$this->assertEquals( $settings['legacy_log_type'], $reaction->get_meta( 'legacy_log_type' ) );
	}

	/**
	 * Test reversing an event.
	 *
	 * @since 1.0.0
	 */
	public function test_reverse_hits() {

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$settings['event'] = 'user_register';
		$settings['reactor'] = 'points_legacy';

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();

		$user_id = $this->factory->user->create();

		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'user' );
		$arg->value = $user_id;

		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = wordpoints_hooks()
			->get_reaction_store( 'points' )
			->create_reaction( $settings );

		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor->hit( $fire );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'log_type' => 'user_register' )
		);

		$this->assertEquals( 1, $query->count() );

		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$query = new WordPoints_Points_Logs_Query(
			array( 'log_type' => 'reverse-user_register' )
		);

		$this->assertEquals( 1, $query->count() );
	}

	/**
	 * Test reversing an event doesn't undo auto-reversed transactions.
	 *
	 * @since 1.0.0
	 */
	public function test_reverse_hits_auto_reversed() {

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$settings['event'] = 'user_register';
		$settings['reactor'] = 'points_legacy';

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();

		$user_id = $this->factory->user->create();

		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'user' );
		$arg->value = $user_id;

		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = wordpoints_hooks()
			->get_reaction_store( 'points' )
			->create_reaction( $settings );

		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor->hit( $fire );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'user_register' )
		);

		$this->assertEquals( 1, $query->count() );

		$log_id = $query->get( 'var' );

		wordpoints_update_points_log_meta( $log_id, 'auto_reversed', true );

		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test reversing an event only reverses the event for the specific entities.
	 *
	 * @since 1.0.0
	 */
	public function test_reverse_hits_different_entities() {

		// We create this user before creating the reaction so that they won't be
		// awarded any points.
		$user_not_awarded_id = $this->factory->user->create();

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$settings['event'] = 'user_register';
		$settings['reactor'] = 'points_legacy';

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();

		$user_id = $this->factory->user->create();

		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'user' );
		$arg->value = $user_id;

		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = wordpoints_hooks()
			->get_reaction_store( 'points' )
			->create_reaction( $settings );

		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor->hit( $fire );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'user_register' )
		);

		$this->assertEquals( 1, $query->count() );

		$reverse_query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'reverse-user_register' )
		);

		$this->assertEquals( 0, $reverse_query->count() );

		// A different user ID for the user arg.
		$arg->value = $user_not_awarded_id;

		$fire->event_args = new WordPoints_Hook_Event_Args( array( $arg ) );

		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );
		$this->assertEquals( 0, $reverse_query->count() );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test reversing an event only takes into account the primary entity.
	 *
	 * @since 1.0.0
	 */
	public function test_reverse_hits_different_stateful_entities() {

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$settings['event'] = 'user_register';
		$settings['reactor'] = 'points_legacy';

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();

		$user_id = $this->factory->user->create();

		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'user' );
		$arg->value = $user_id;

		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );
		$stateful_entity  = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$event_args->add_entity( $stateful_entity );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = wordpoints_hooks()
			->get_reaction_store( 'points' )
			->create_reaction( $settings );

		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor->hit( $fire );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'user_register' )
		);

		$this->assertEquals( 1, $query->count() );

		$reverse_query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'reverse-user_register' )
		);

		$this->assertEquals( 0, $reverse_query->count() );

		// A different value for the stateful entity.
		$stateful_entity->set_the_value( 2 );

		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );
		$this->assertEquals( 1, $reverse_query->count() );

		$this->assertEquals(
			100
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test reversing an event when the entity is namespaced.
	 *
	 * @since 1.0.0
	 */
	public function test_reverse_hits_namespaced_entity() {

		$user_id = $this->factory->user->create();
		$post_id = $this->factory->post->create(
			array( 'post_author' => $user_id, 'post_type' => 'page' )
		);

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$points = 10;

		// Simulate a legacy points hook fire.
		wordpoints_alter_points(
			$user_id
			, $points
			, 'points'
			, 'post_publish'
			, array( 'post' => $post_id )
		);

		$this->assertEquals(
			100 + $points
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'post_publish' )
		);

		$this->assertEquals( 1, $query->count() );

		$reverse_query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'reverse-post_publish' )
		);

		$this->assertEquals( 0, $reverse_query->count() );

		// Now reverse fire.
		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'post\page' );
		$arg->value = $post_id;

		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );

		$reaction = wordpoints_hooks()
			->get_reaction_store( 'points' )
			->create_reaction(
				array(
					'event'           => 'post_publish\page',
					'reactor'         => 'points_legacy',
					'target'          => array( 'post\page', 'author', 'user' ),
					'points'          => $points,
					'points_type'     => 'points',
					'description'     => 'Testing.',
					'log_text'        => 'Testing.',
					'legacy_log_type' => 'post_publish',
				)
			);

		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();
		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );
		$this->assertEquals( 1, $reverse_query->count() );

		$this->assertEquals(
			100
			, wordpoints_get_points( $user_id, 'points' )
		);
	}

	/**
	 * Test reversing an event marks it as such so it isn't reversed a second time.
	 *
	 * @since 1.0.0
	 */
	public function test_reverse_hit_only_reverses_once() {

		$points = 10;

		$user_id = $this->factory->user->create();

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = wordpoints_hooks()
			->get_reaction_store( 'points' )
			->create_reaction(
				array(
					'event'       => 'user_register',
					'reactor'     => 'points_legacy',
					'target'      => array( 'user' ),
					'points'      => $points,
					'points_type' => 'points',
					'description' => 'Testing.',
					'log_text'    => 'Testing.',
				)
			);

		$this->assertIsReaction( $reaction );

		// Simulate a legacy points hook fire.
		wordpoints_alter_points(
			$user_id
			, $points
			, 'points'
			, 'user_register'
			, array( 'user' => $user_id )
		);

		$query = new WordPoints_Points_Logs_Query(
			array(
				'log_type' => 'user_register',
				'meta_key' => 'auto_reversed',
				'meta_compare' => 'NOT EXISTS',
			)
		);

		$this->assertEquals( 1, $query->count() );

		// Reverse fire once.
		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'user' );
		$arg->value = $user_id;

		$event_args = new WordPoints_Hook_Event_Args( array( $arg ) );

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, 'test_fire' );

		$reactor = new WordPoints_Hook_Reactor_Points_Legacy();
		$reactor->reverse_hit( $fire );

		$this->assertEquals( 0, $query->count() );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reverse_query = new WordPoints_Points_Logs_Query(
			array( 'log_type' => 'reverse-user_register' )
		);

		$this->assertEquals( 1, $reverse_query->count() );

		// Reverse fire a second time.
		$reactor->reverse_hit( $fire );

		$this->assertEquals( 0, $query->count() );

		// A second reverse should not have occurred.
		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$this->assertEquals( 1, $reverse_query->count() );
	}
}

// EOF
