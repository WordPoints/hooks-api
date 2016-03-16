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
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
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
			'none' => array( array( 'test_fire' => array() ) ),
			'empty' => array(
				array( 'periods' => array( 'test_fire' => array() ) ),
			),
			'no_args' => array(
				array(
					'periods' => array(
						'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ),
					),
				),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'relative' => array(
				array(
					'periods' => array(
						'test_fire' => array(
							array(
								'length' => MINUTE_IN_SECONDS,
								'relative' => true,
							),
						),
					),
				),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'entity' => array(
				array(
					'periods' => array(
						'test_fire' => array(
							array(
								'length' => MINUTE_IN_SECONDS,
								'args' => array( array( 'test_entity' ) ),
							),
						),
					),
				),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'child' => array(
				array(
					'periods' => array(
						'test_fire' => array(
							array(
								'length' => MINUTE_IN_SECONDS,
								'args' => array( array( 'test_entity', 'child' ) ),
							),
						),
					),
				),
				array( array( 'signature' => '8fd6eba3474b89832e4f275bba0345d8943ac8ef71ce54863a0f675738834828' ) ),
			),
			'both' => array(
				array(
					'periods' => array(
						'test_fire' => array(
							array(
								'length' => MINUTE_IN_SECONDS,
								'args' => array(
									array( 'test_entity' ),
									array( 'test_entity', 'child' ),
								),
							),
						),
					),
				),
				array( array( 'signature' => '390fbb79aab5e1ba1f07e96cddddbaee0f7afa2754c6e5877ae94668bf468e63' ) ),
			),
			'multiple' => array(
				array(
					'periods' => array(
						'test_fire' => array(
							array(
								'length' => MINUTE_IN_SECONDS,
								'args' => array( array( 'test_entity', 'child' ) ),
							),
							array(
								'length' => HOUR_IN_SECONDS,
							),
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
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
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

		if ( is_array( $settings['periods']['test_fire'] ) ) {
			$this->assertEquals( $settings, $result );
		} else {
			$this->assertSame( array(), $result['periods']['test_fire'] );
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
				array( 'periods' => array( 'test_fire' => 'not_array' ) ),
				array( 'periods', 'test_fire' ),
			),
			'period_not_array' => array(
				array( 'periods' => array( 'test_fire' => array( 'not_array' ) ) ),
				array( 'periods', 'test_fire', 0 ),
			),
			'missing_length' => array(
				array( 'periods' => array( 'test_fire' => array( array() ) ) ),
				array( 'periods', 'test_fire', 0 ),
			),
			'invalid_length' => array(
				array( 'periods' => array( 'test_fire' => array( array( 'length' => 'invalid' ) ) ) ),
				array( 'periods', 'test_fire', 0, 'length' ),
			),
			'negative_length' => array(
				array( 'periods' => array( 'test_fire' => array( array( 'length' => -MINUTE_IN_SECONDS ) ) ) ),
				array( 'periods', 'test_fire', 0, 'length' ),
			),
			'args_not_array' => array(
				array( 'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS, 'args' => 'not_array' ) ) ) ),
				array( 'periods', 'test_fire', 0, 'args' ),
			),
			'arg_not_array' => array(
				array( 'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS, 'args' => array( 'not_array' ) ) ) ) ),
				array( 'periods', 'test_fire', 0, 'args', 0 ),
			),
			'invalid_entity' => array(
				array( 'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS, 'args' => array( array( 'invalid_entity' ) ) ) ) ) ),
				array( 'periods', 'test_fire', 0, 'args', 0 ),
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

		$fire = new WordPoints_Hook_Fire( 'test_fire', $event_args, $reaction );

		$this->assertTrue( $extension->should_hit( $fire ) );

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

		$fire = new WordPoints_Hook_Fire( 'test_fire', $event_args, $reaction );

		$fire->hit();

		$extension->after_hit( $fire );

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
			"
				SELECT `hit`.`reaction_id`, `hit`.`date`, `period`.`signature`
				FROM `{$wpdb->wordpoints_hook_periods}` AS `period`
				INNER JOIN `{$wpdb->wordpoints_hook_hits}` AS `hit`
					ON `hit`.`id` = period.`hit_id`
				ORDER BY `period`.`id`
			"
		);

		$this->assertCount( count( $periods ), $results );

		foreach ( $periods as $index => $period ) {

			$this->assertArrayHasKey( $index, $results );

			$now = current_time( 'timestamp' );

			$this->assertEquals( $reaction->ID, $results[ $index ]->reaction_id );
			$this->assertLessThanOrEqual( 2, $now - strtotime( $results[ $index ]->date, $now ) );
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertPeriodsExist( $periods, $reaction );

		$this->assertCount( 1, wordpoints_hooks()->reactors->get( 'test_reactor' )->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertPeriodsExist( $periods, $reaction );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$this->fast_forward( $test_reactor->hits[0]->hit_id, MINUTE_IN_SECONDS + 1 );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
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
				array( 'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ) ),
				array( array( 'signature' => '7228811153da11efc20245206d55935d4face04940fa8c80c0ad1b1f0cb52932' ) ),
			),
			'relative' => array(
				array( 'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS, 'relative' => true ) ) ) ),
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
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$reaction->update_meta( 'target', array( 'another:test_entity' ) );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

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
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that the periods are per-reactor.
	 *
	 * @since 1.0.0
	 */
	public function test_periods_per_reactor() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Create another reaction for another reactor.
		$settings['reactor'] = 'another_reactor';
		$settings['reaction_store'] = 'another_store';
		$other_reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $other_reaction );

		$this->assertEquals( $reaction->ID, $other_reaction->ID );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$other_reactor = wordpoints_hooks()->reactors->get( 'another_reactor' );

		$this->assertCount( 1, $other_reactor->hits );
	}

	/**
	 * Test that the periods are per-reaction store.
	 *
	 * @since 1.0.0
	 */
	public function test_periods_per_reaction_store() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Create another reaction for a different reaction store.
		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reaction_store' => 'test_store' )
		);

		$this->assertEquals( 'test_store', $other_reaction->get_store_slug() );
		$this->assertEquals( $reaction->ID, $other_reaction->ID );
		$this->assertEquals(
			$reaction->get_context_id()
			, $other_reaction->get_context_id()
		);

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that the periods are per-reaction context ID.
	 *
	 * @since 1.0.0
	 */
	public function test_periods_per_reaction_context_id() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
			'target'  => array( 'test_entity' ),
		);

		$this->factory->wordpoints->hook_reaction_store->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted' )
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Then perform the event with a different reaction context ID.
		WordPoints_PHPUnit_Mock_Entity_Context::$current_id = 5;

		$router->fire_event( 'test_fire', 'test_event', $event_args );

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
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$entity->set_the_value( 6 );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

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
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		$this->fast_forward( $test_reactor->hits[0]->hit_id, MINUTE_IN_SECONDS + 3 );

		// Increase the length.
		$reaction->update_meta(
			'periods'
			, array( 'test_fire' => array( array( 'length' => HOUR_IN_SECONDS ) ) )
		);

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Back to the shorter length.
		$reaction->update_meta(
			'periods'
			, array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) )
		);

		$router->fire_event( 'test_fire', 'test_event', $event_args );

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
			'periods' => array( 'test_fire' => array( array( 'length' => HOUR_IN_SECONDS ) ) ),
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

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$this->fast_forward( $test_reactor->hits[0]->hit_id, MINUTE_IN_SECONDS + 3 );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 1, $test_reactor->hits );

		// Decrease the length.
		$reaction->update_meta(
			'periods'
			, array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) )
		);

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertCount( 2, $test_reactor->hits );
	}

	/**
	 * Test that caching of the periods is used.
	 *
	 * @since 1.0.0
	 */
	public function test_caching() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
			'target'  => array( 'test_entity' ),
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args(
			array( new WordPoints_Hook_Arg( 'test_entity' ) )
		);

		$get_period_queries = new WordPoints_Mock_Filter();
		$get_period_queries->count_callback = array( $this, 'is_get_period_query' );
		add_filter( 'query', array( $get_period_queries, 'filter' ) );

		$get_by_reaction_queries = new WordPoints_Mock_Filter();
		$get_by_reaction_queries->count_callback = array( $this, 'is_get_period_by_reaction_query' );
		add_filter( 'query', array( $get_by_reaction_queries, 'filter' ) );

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 0, $get_period_queries->call_count );
		$this->assertEquals( 1, $get_by_reaction_queries->call_count );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 1, $get_period_queries->call_count );
		$this->assertEquals( 1, $get_by_reaction_queries->call_count );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 1, $get_period_queries->call_count );
		$this->assertEquals( 1, $get_by_reaction_queries->call_count );

		$this->assertCount( 1, $test_reactor->hits );
	}

	/**
	 * Test should_hit() when the period isn't cached.
	 *
	 * @since 1.0.0
	 */
	public function test_should_hit_uncached() {

		$this->mock_apps();

		wordpoints_hooks()->extensions->register(
			'periods'
			, 'WordPoints_Hook_Extension_Periods'
		);

		$settings = array(
			'periods' => array( 'test_fire' => array( array( 'length' => MINUTE_IN_SECONDS ) ) ),
			'target'  => array( 'test_entity' ),
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create( $settings );

		$this->assertIsReaction( $reaction );

		$event_args = new WordPoints_Hook_Event_Args(
			array( new WordPoints_Hook_Arg( 'test_entity' ) )
		);

		$get_period_queries = new WordPoints_Mock_Filter();
		$get_period_queries->count_callback = array( $this, 'is_get_period_query' );
		add_filter( 'query', array( $get_period_queries, 'filter' ) );

		$get_by_reaction_queries = new WordPoints_Mock_Filter();
		$get_by_reaction_queries->count_callback = array( $this, 'is_get_period_by_reaction_query' );
		add_filter( 'query', array( $get_by_reaction_queries, 'filter' ) );

		$router = wordpoints_hooks()->router;
		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 0, $get_period_queries->call_count );
		$this->assertEquals( 1, $get_by_reaction_queries->call_count );

		$test_reactor = wordpoints_hooks()->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $test_reactor->hits );

		$this->flush_cache();

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 0, $get_period_queries->call_count );
		$this->assertEquals( 2, $get_by_reaction_queries->call_count );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 0, $get_period_queries->call_count );
		$this->assertEquals( 2, $get_by_reaction_queries->call_count );

		$this->assertCount( 1, $test_reactor->hits );

		$router->fire_event( 'test_fire', 'test_event', $event_args );

		$this->assertEquals( 0, $get_period_queries->call_count );
		$this->assertEquals( 2, $get_by_reaction_queries->call_count );

		$this->assertCount( 1, $test_reactor->hits );
	}

	/**
	 * Travel forward in time by modifying the hit time of a period.
	 *
	 * @since 1.0.0
	 *
	 * @param int $hit_id  The ID of the reaction the period is for.
	 * @param int $seconds The number of seconds to travel forward.
	 */
	protected function fast_forward( $hit_id, $seconds ) {

		global $wpdb;

		$updated = $wpdb->update(
			$wpdb->wordpoints_hook_hits
			, array( 'date' => gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - $seconds ) )
			, array( 'id' => $hit_id )
			, array( '%s' )
			, array( '%d' )
		);

		$this->assertEquals( 1, $updated );

		// The periods cache will still hold the old date.
		$this->flush_cache();
	}

	/**
	 * Check whether a database query is to retrieve a period.
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql The database query string.
	 *
	 * @return bool Whether the query is to retrieve a period.
	 */
	public function is_get_period_query( $sql ) {

		global $wpdb;

		return false !== strpos( $sql, "SELECT *, `period`.`id` AS `id`
						FROM `{$wpdb->wordpoints_hook_periods}` AS `period`
						INNER JOIN `{$wpdb->wordpoints_hook_hits}` AS `hit`
							ON `hit`.`id` = `period`.`hit_id`
						WHERE `period`.`id` "
		);
	}

	/**
	 * Check whether a database query is to retrieve a period by reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string $sql The database query string.
	 *
	 * @return bool Whether the query is to get a period by reaction.
	 */
	public function is_get_period_by_reaction_query( $sql ) {

		global $wpdb;

		return false !== strpos( $sql, "SELECT *, `period`.`id` AS `id`
					FROM `{$wpdb->wordpoints_hook_periods}` AS `period`
					INNER JOIN `{$wpdb->wordpoints_hook_hits}` AS `hit`
						ON `hit`.`id` = period.`hit_id`
					WHERE `period`.`signature` "
		);
	}
}

// EOF
