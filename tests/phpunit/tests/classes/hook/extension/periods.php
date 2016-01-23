<?php

/**
 * Test case for WordPoints_Hook_Extension_Periods.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Extension_Periods.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Extension_Periods
 */
class WordPoints_Hook_Extension_Periods_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test validating the settings.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_period_settings
	 *
	 * @param array $settings An array of valid settings.
	 */
	public function test_validate_settings( array $settings ) {

		$this->mock_apps();

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$extension = new WordPoints_Hook_Extension_Periods();
		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another' )
		);

		$event_args->set_validator( $validator );

		$result = $extension->validate_settings( $settings, $validator, $event_args );

		$this->assertFalse( $validator->had_errors() );
		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Provides several different sets of valid period settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_valid_period_settings() {

		return array(
			'none' => array( array() ),
			'empty' => array( array( 'periods' => array() ) ),
			'no_args' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'relative' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS, 'relative' => true ) ) ),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'entity' => array(
				array(
					'periods' => array(
						array(
							'length' => MINUTE_IN_SECONDS,
							'args' => array( array( 'test_entity' ) ),
						),
					),
				),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'child' => array(
				array(
					'periods' => array(
						array(
							'length' => MINUTE_IN_SECONDS,
							'args' => array( array( 'test_entity', 'child' ) ),
						),
					),
				),
				array( array( 'signature' => '8fd6eba3474b89832e4f275bba0345d8943ac8ef71ce54863a0f675738834828' ) ),
			),
			'both' => array(
				array(
					'periods' => array(
						array(
							'length' => MINUTE_IN_SECONDS,
							'args' => array(
								array( 'test_entity' ),
								array( 'test_entity', 'child' ),
							),
						),
					),
				),
				array( array( 'signature' => '390fbb79aab5e1ba1f07e96cddddbaee0f7afa2754c6e5877ae94668bf468e63' ) ),
			),
			'multiple' => array(
				array(
					'periods' => array(
						array(
							'length' => MINUTE_IN_SECONDS,
							'args' => array( array( 'test_entity', 'child' ) ),
						),
						array(
							'length' => HOUR_IN_SECONDS,
						),
					),
				),
				array(
					array( 'signature' => '8fd6eba3474b89832e4f275bba0345d8943ac8ef71ce54863a0f675738834828' ),
					array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ),
				),
			),
		);
	}

	/**
	 * Test validating the settings when they are invalid.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_invalid_period_settings
	 *
	 * @param array  $settings        The settings, with one invalid or missing.
	 * @param string $invalid         The slug of the setting that is invalid.
	 * @param int    $expected_errors The number of errors expected.
	 */
	public function test_validate_settings_invalid( array $settings, $invalid, $expected_errors = 1 ) {

		$this->mock_apps();

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$extension = new WordPoints_Hook_Extension_Periods();
		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$result = $extension->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( $expected_errors, $errors );
		$this->assertEquals( $invalid, $errors[0]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		if ( is_array( $settings['periods'] ) ) {
			$this->assertEquals( $settings, $result );
		} else {
			$this->assertSame( array(), $result['periods'] );
		}
	}

	/**
	 * Provides an array of possible period settings, each with one invalid item.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Every possible set of settings with one invalid item.
	 */
	public function data_provider_invalid_period_settings() {

		return array(
			'not_array' => array(
				array( 'periods' => 'not_array' ),
				array( 'periods' ),
			),
			'period_not_array' => array(
				array( 'periods' => array( 'not_array' ) ),
				array( 'periods', 0 ),
			),
			'missing_length' => array(
				array( 'periods' => array( array() ) ),
				array( 'periods', 0 ),
			),
			'invalid_length' => array(
				array( 'periods' => array( array( 'length' => 'invalid' ) ) ),
				array( 'periods', 0, 'length' ),
			),
			'negative_length' => array(
				array( 'periods' => array( array( 'length' => -MINUTE_IN_SECONDS ) ) ),
				array( 'periods', 0, 'length' ),
			),
			'args_not_array' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS, 'args' => 'not_array' ) ) ),
				array( 'periods', 0, 'args' ),
			),
			'arg_not_array' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS, 'args' => array( 'not_array' ) ) ) ),
				array( 'periods', 0, 'args', 0 ),
			),
			'invalid_entity' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS, 'args' => array( array( 'invalid_entity' ) ) ) ) ),
				array( 'periods', 0, 'args', 0 ),
				2,
			),
		);
	}

	/**
	 * Test checking whether an event should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_period_settings
	 *
	 * @param array $settings Reaction settings.
	 */
	public function test_should_hit( array $settings ) {

		$this->mock_apps();

		$extensions = wordpoints_hooks()->extensions;
		$extensions->register( 'conditions', 'WordPoints_Hook_Extension_Periods' );
		$extension = $extensions->get( 'conditions' );

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$settings['target'] = array( 'test_entity' );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );
		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another' )
		);

		$this->assertTrue( $extension->should_hit( $reaction, $event_args ) );

		$this->assertNull( $event_args->get_current() );
	}

	/**
	 * Test adding a period to the DB after hitting the target.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_period_settings
	 *
	 * @param array $settings An array of valid settings.
	 * @param array $periods  Data for the periods expected to be created.
	 */
	public function test_after_hit( $settings, $periods = array() ) {

		$this->mock_apps();

		$extensions = wordpoints_hooks()->extensions;
		$extensions->register( 'periods', 'WordPoints_Hook_Extension_Periods' );

		$extension = $extensions->get( 'periods' );

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$settings['target'] = array( 'test_entity' );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another' )
		);

		$extension->after_hit( $reaction, $event_args );

		$this->assertNull( $event_args->get_current() );

		$this->assertPeriodsExist( $periods, $reaction );
	}

	/**
	 * Assert that periods exist in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array                     $periods  Data for the periods.
	 * @param WordPoints_Hook_ReactionI $reaction The reaction object the periods
	 *                                            relate to.
	 */
	public function assertPeriodsExist( $periods, $reaction ) {

		global $wpdb;

		$results = $wpdb->get_results(
			"SELECT * FROM `{$wpdb->wordpoints_hook_periods}`"
		);

		$this->assertCount( count( $periods ), $results );

		foreach ( $periods as $index => $period ) {

			$this->assertArrayHasKey( $index, $results );

			$now = current_time( 'timestamp' );

			$this->assertEquals( $reaction->ID, $results[ $index ]->reaction_id );
			$this->assertLessThanOrEqual( 2, $now - strtotime( $results[ $index ]->hit_time, $now ) );
			$this->assertEquals( $period['signature'], $results[ $index ]->signature );
		}
	}

	/**
	 * Test checking that an event will hit the target only once in a period.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_non_empty_period_settings
	 *
	 * @param array $settings An array of valid settings.
	 * @param array $periods  Data for the periods expected to be created.
	 */
	public function test_should_hit_period_started( $settings, $periods = array() ) {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$settings['target'] = array( 'test_entity' );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another' )
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$this->assertPeriodsExist( $periods, $reaction );

		$this->assertCount( 1, wordpoints_hooks()->reactors->get( 'test_reactor' )->hits );

		$firer->do_event( 'test_event', $event_args );

		$this->assertPeriodsExist( $periods, $reaction );

		$this->assertCount( 1, wordpoints_hooks()->reactors->get( 'test_reactor' )->hits );
	}

	/**
	 * Provides several different sets of valid period settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_non_empty_period_settings() {

		$settings = $this->data_provider_valid_period_settings();

		unset( $settings['none'], $settings['empty'] );

		return $settings;
	}

	/**
	 * Test checking that an event will hit the target once the period has ended.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_period_settings_period_over
	 *
	 * @param array $settings An array of valid settings.
	 * @param array $periods  Data for the periods expected to be created.
	 */
	public function test_should_hit_period_over( $settings, $periods = array() ) {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$settings['target'] = array( 'test_entity' );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another' )
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$this->assertPeriodsExist( $periods, $reaction );

		$this->assertCount( 1, wordpoints_hooks()->reactors->get( 'test_reactor' )->hits );

		$this->fast_forward( $reaction->ID, MINUTE_IN_SECONDS + 1 );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 2, wordpoints_hooks()->reactors->get( 'test_reactor' )->hits );
	}

	/**
	 * Provides several different sets of valid period settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_period_settings_period_over() {

		return array(
			'absolute' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'relative' => array(
				array( 'periods' => array( array( 'length' => MINUTE_IN_SECONDS, 'relative' => true ) ) ),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
		);
	}

	/**
	 * Test that the periods are "reset" when the target changes.
	 *
	 * @since 1.0.0
	 */
	public function test_should_hit_target_changed() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( array( 'length' => MINUTE_IN_SECONDS ) ),
			'target'  => array( 'test_entity' ),
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another:test_entity' )
		);

		wordpoints_hooks()->events->args->register(
			'test_event'
			, 'another:test_entity'
			, 'WordPoints_PHPUnit_Mock_Hook_Arg'
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$reaction->update_meta( 'target', array( 'another:test_entity' ) );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that the periods are per-reaction.
	 *
	 * @since 1.0.0
	 */
	public function test_periods_per_reaction() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( array( 'length' => MINUTE_IN_SECONDS ) ),
			'target'  => array( 'test_entity' ),
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		wordpoints_hooks()->events->args->register(
			'test_event'
			, 'another:test_entity'
			, 'WordPoints_PHPUnit_Mock_Hook_Arg'
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that the periods are per arg value.
	 *
	 * @since 1.0.0
	 */
	public function test_periods_per_arg_value() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( array( 'length' => MINUTE_IN_SECONDS ) ),
			'target'  => array( 'test_entity' ),
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$entity->set_the_value( 5 );

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		wordpoints_hooks()->events->args->register(
			'test_event'
			, 'another:test_entity'
			, 'WordPoints_PHPUnit_Mock_Hook_Arg'
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$entity->set_the_value( 6 );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that when the period length increases expired periods are considered.
	 *
	 * @since 1.0.0
	 */
	public function test_period_length_increases() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( array( 'length' => MINUTE_IN_SECONDS ) ),
			'target'  => array( 'test_entity' ),
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		wordpoints_hooks()->events->args->register(
			'test_event'
			, 'another:test_entity'
			, 'WordPoints_PHPUnit_Mock_Hook_Arg'
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$this->fast_forward( $reaction->ID, MINUTE_IN_SECONDS + 1 );

		// Increase the length.
		$reaction->update_meta(
			'periods'
			, array( array( 'length' => HOUR_IN_SECONDS ) )
		);

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Back to the shorter length.
		$reaction->update_meta(
			'periods'
			, array( array( 'length' => MINUTE_IN_SECONDS ) )
		);

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that when the period length decreases expired periods are considered.
	 *
	 * @since 1.0.0
	 */
	public function test_period_length_decreases() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( array( 'length' => HOUR_IN_SECONDS ) ),
			'target'  => array( 'test_entity' ),
		);
		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test_entity' );
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$event_args->add_entity( $entity );

		wordpoints_hooks()->events->args->register(
			'test_event'
			, 'another:test_entity'
			, 'WordPoints_PHPUnit_Mock_Hook_Arg'
		);

		$firer = new WordPoints_Hook_Firer();
		$firer->do_event( 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$this->fast_forward( $reaction->ID, MINUTE_IN_SECONDS + 1 );

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Decrease the length.
		$reaction->update_meta(
			'periods'
			, array( array( 'length' => MINUTE_IN_SECONDS ) )
		);

		$firer->do_event( 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Travel forward in time by modifying the hit time of a period.
	 *
	 * @since 1.0.0
	 *
	 * @param int $reaction_id The ID of the reaction the period is for.
	 * @param int $seconds     The number of seconds to travel forward.
	 */
	protected function fast_forward( $reaction_id, $seconds ) {

		global $wpdb;

		$updated = $wpdb->update(
			$wpdb->wordpoints_hook_periods
			, array( 'hit_time' => gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - $seconds ) )
			, array( 'reaction_id' => $reaction_id )
			, array( '%s' )
			, array( '%d' )
		);

		$this->assertEquals( 1, $updated );
	}
}

// EOF
