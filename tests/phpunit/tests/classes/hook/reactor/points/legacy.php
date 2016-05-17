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
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

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
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

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
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

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
		$entity->set_the_value( $user_not_awarded_id );

		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );
		$this->assertEquals( 0, $reverse_query->count() );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);
	}
}

// EOF
