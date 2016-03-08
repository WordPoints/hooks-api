<?php

/**
 * Test case for WordPoints_Hook_Reactor_Points.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reactor_Points.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reactor_Points
 */
class WordPoints_Hook_Reactor_Points_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the settings fields.
	 *
	 * @since 1.0.0
	 */
	public function test_get_settings_fields() {

		$reactor = new WordPoints_Hook_Reactor_Points();

		$settings_fields = $reactor->get_settings_fields();

		$this->assertInternalType( 'array', $settings_fields );

		$this->assertArrayHasKey( 'points', $settings_fields );
		$this->assertArrayHasKey( 'label', $settings_fields['points'] );

		$this->assertArrayHasKey( 'log_text', $settings_fields );
		$this->assertArrayHasKey( 'label', $settings_fields['log_text'] );

		$this->assertArrayHasKey( 'description', $settings_fields );
		$this->assertArrayHasKey( 'label', $settings_fields['description'] );

	}

	/**
	 * Test validating the settings.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings
	 *
	 * @param array $settings An array of valid settings.
	 */
	public function test_validate_settings( array $settings ) {

		$reactor = new WordPoints_Hook_Reactor_Points();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'user' )
		);

		$event_args->set_validator( $validator );

		$this->create_points_type();

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertFalse( $validator->had_errors() );
		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Provides several different sets of valid settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_valid_settings() {

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$alt = $settings;
		$alt['points'] = -20;

		return array(
			'positive_points' => array( $settings ),
			'negative_points' => array( $alt ),
		);
	}

	/**
	 * Test validating the settings they are invalid.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_invalid_settings
	 *
	 * @param array  $settings The settings, with one invalid or missing.
	 * @param string $invalid  The slug of the setting that is invalid or missing.
	 */
	public function test_validate_settings_invalid( array $settings, $invalid ) {

		$reactor = new WordPoints_Hook_Reactor_Points();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'user' )
		);

		$event_args->set_validator( $validator );

		$this->create_points_type();

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 1, $errors );
		$this->assertEquals( array( $invalid ), $errors[0]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Provides an array of possible settings, each with one invalid item.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Every possible set of settings with one invalid item.
	 */
	public function data_provider_invalid_settings() {

		$invalid_settings = array();

		$invalid_setting_fields = array(
			'points_type' => 'invalid',
			'points'      => false,
		);

		$all_settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		foreach ( $all_settings as $slug => $value ) {

			$invalid_settings[ "no_{$slug}" ] = array( $all_settings, $slug );

			unset( $invalid_settings[ "no_{$slug}" ][0][ $slug ] );

			if ( isset( $invalid_setting_fields[ $slug ] ) ) {
				$invalid_settings[ "invalid_{$slug}" ] = array( $all_settings, $slug );
				$invalid_settings[ "invalid_{$slug}" ][0][ $slug ] = $invalid_setting_fields[ $slug ];
			}
		}

		return $invalid_settings;
	}

	/**
	 * Test updating the settings.
	 *
	 * @since 1.0.0
	 */
	public function test_update_settings() {

		$reactor = new WordPoints_Hook_Reactor_Points();

		$this->create_points_type();

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$create = $settings;
		$create['reactor'] = 'points';
		$create['event'] = 'user_register';

		$reaction = $reactor->reactions->create_reaction( $create );

		$this->assertIsReaction( $reaction );

		$this->assertEquals( $settings['target'], $reaction->get_meta( 'target' ) );
		$this->assertEquals( $settings['points'], $reaction->get_meta( 'points' ) );
		$this->assertEquals( $settings['points_type'], $reaction->get_meta( 'points_type' ) );
		$this->assertEquals( $settings['description'], $reaction->get_meta( 'description' ) );
		$this->assertEquals( $settings['log_text'], $reaction->get_meta( 'log_text' ) );
	}

	/**
	 * Test hitting the target.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings
	 *
	 * @param array $settings Reaction settings.
	 */
	public function test_hit( array $settings ) {

		$settings['event'] = 'user_register';

		$reactor = new WordPoints_Hook_Reactor_Points();
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = $reactor->reactions->create_reaction( $settings );
		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( 'test_fire', $event_args, $reaction );

		$reactor->hit( $fire );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'log_type' => 'user_register' )
		);

		$log = $query->get( 'row' );

		$this->assertEquals( $user_id, $log->user_id );
		$this->assertEquals( $settings['points'], $log->points );
		$this->assertEquals( $settings['points_type'], $log->points_type );
		$this->assertEquals( $settings['event'], $log->log_type );
		$this->assertEquals( $settings['log_text'], $log->text );

		$this->assertEquals(
			$user_id
			, wordpoints_get_points_log_meta( $log->id, 'user', true )
		);
	}

	/**
	 * Test reversing an event.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings
	 *
	 * @param array $settings Reaction settings.
	 */
	public function test_reverse_hits( array $settings ) {

		$settings['event'] = 'user_register';

		$reactor = new WordPoints_Hook_Reactor_Points();
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = $reactor->reactions->create_reaction( $settings );
		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( 'test_fire', $event_args, $reaction );

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

		$this->assertEquals( 0, $query->count() );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$query = new WordPoints_Points_Logs_Query(
			array( 'log_type' => 'reverse-user_register' )
		);

		$this->assertEquals( 0, $query->count() );
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

		$reactor = new WordPoints_Hook_Reactor_Points();
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = $reactor->reactions->create_reaction( $settings );
		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( 'test_fire', $event_args, $reaction );

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

		$settings = array(
			'target'      => array( 'user' ),
			'points'      => 10,
			'points_type' => 'points',
			'description' => 'Testing.',
			'log_text'    => 'Testing.',
		);

		$settings['event'] = 'user_register';

		$reactor = new WordPoints_Hook_Reactor_Points();
		$event_args = new WordPoints_Hook_Event_Args( array() );

		/** @var WordPoints_Entity_User $entity */
		$entity = wordpoints_entities()->get( 'user' );

		$user_id = $this->factory->user->create();

		$entity->set_the_value( $user_id );

		$event_args->add_entity( $entity );

		$this->create_points_type();

		wordpoints_set_points( $user_id, 100, 'points', 'test' );

		$this->assertEquals( 100, wordpoints_get_points( $user_id, 'points' ) );

		$reaction = $reactor->reactions->create_reaction( $settings );
		$this->assertIsReaction( $reaction );

		$fire = new WordPoints_Hook_Fire( 'test_fire', $event_args, $reaction );

		$reactor->hit( $fire );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);

		$query = new WordPoints_Points_Logs_Query(
			array( 'fields' => 'id', 'log_type' => 'user_register' )
		);

		$this->assertEquals( 1, $query->count() );

		// A different user ID for the user arg.
		$entity->set_the_value( $this->factory->user->create() );

		$reactor->reverse_hit( $fire );

		$this->assertEquals( 1, $query->count() );

		$this->assertEquals(
			100 + $settings['points']
			, wordpoints_get_points( $user_id, 'points' )
		);
	}
}

// EOF
