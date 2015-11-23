<?php

/**
 * Test case for WordPoints_Hook_Extension_Conditions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Extension_Conditions.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Extension_Conditions
 */
class WordPoints_Hook_Extension_Conditions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

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

		$this->mock_apps();

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'test', 'data_type' => 'entity' )
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'test', 'data_type' => 'text' )
		);

		$extension = new WordPoints_Hook_Extension_Conditions();
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
	 * Provides several different sets of valid settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_valid_settings() {

		$conditions = array(
			'_conditions' => array(
				array(
					'type'     => 'test',
					'settings' => array( 'value' => 'a' ),
				),
			),
		);

		$entity = array( 'test_entity' => $conditions );
		$child = $both = array( 'test_entity' => array( 'child' => $conditions ) );

		$both['test_entity']['_conditions'] = $conditions['_conditions'];

		return array(
			'none' => array( array() ),
			'empty' => array( array( 'conditions' => array() ) ),
			'entity' => array( array( 'conditions' => $entity ) ),
			'child' => array( array( 'conditions' => $child ) ),
			'both' => array( array( 'conditions' => $both ) ),
			'two_entities' => array(
				array(
					'conditions' => array(
						'test_entity' => $conditions,
						'another' => $conditions,
					),
				),
			),
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

		$this->mock_apps();

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'test', 'data_type' => 'entity' )
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'data_type' => 'text' )
		);

		$extension = new WordPoints_Hook_Extension_Conditions();
		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$this->create_points_type();

		$result = $extension->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 1, $errors );
		$this->assertEquals( $invalid, $errors[0]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		if ( is_array( $settings['conditions'] ) ) {
			$this->assertEquals( $settings, $result );
		} else {
			$this->assertSame( array(), $result['conditions'] );
		}
	}

	/**
	 * Provides an array of possible settings, each with one invalid item.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Every possible set of settings with one invalid item.
	 */
	public function data_provider_invalid_settings() {

		$conditions = array(
			'_conditions' => array(
				array(
					'type'     => 'test',
					'settings' => array( 'value' => 'a' ),
				),
			),
		);

		$invalid_settings = array(
			'not_array' => array(
				array( 'conditions' => 'not_array' ),
				array( 'conditions' ),
			),
			'invalid_entity' => array(
				array( 'conditions' => array( 'invalid_entity' => $conditions ) ),
				array( 'conditions' ),
			),
			'incorrect_data_type' => array(
				array( 'conditions' => array( 'test_entity' => array( 'child' => $conditions ) ) ),
				array( 'conditions', 'test_entity', 'child', '_conditions', 0 ),
			),
		);

		$invalid_setting_fields = array(
			'type' => 'invalid',
			'settings' => array(),
		);

		foreach ( $conditions['_conditions'][0] as $slug => $value ) {

			$invalid_conditions = $conditions;

			unset( $invalid_conditions['_conditions'][0][ $slug ] );

			$field = array( 'conditions', 'test_entity', '_conditions', 0 );

			$invalid_settings[ "no_{$slug}" ] = array(
				array( 'conditions' => array( 'test_entity' => $invalid_conditions ) ),
				$field,
			);

			if ( isset( $invalid_setting_fields[ $slug ] ) ) {
				$invalid_conditions['_conditions'][0][ $slug ] = $invalid_setting_fields[ $slug ];

				$field[] = $slug;

				if ( 'settings' === $slug ) {
					$field[] = 'value';
				}

				$invalid_settings[ "invalid_{$slug}" ] = array(
					array( 'conditions' => array( 'test_entity' => $invalid_conditions ) ),
					$field,
				);
			}
		}

		return $invalid_settings;
	}

	/**
	 * Test checking whether an event should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_valid_settings
	 *
	 * @param array $settings Reaction settings.
	 */
	public function test_should_hit( array $settings ) {

		$this->mock_apps();

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'test', 'data_type' => 'entity' )
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'test', 'data_type' => 'text' )
		);

		$extension = new WordPoints_Hook_Extension_Conditions();
		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( $settings, $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another' )
		);

		$event_args->set_validator( $validator );

		$this->assertTrue( $extension->should_hit( $validator, $event_args ) );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );
	}

	/**
	 * Test checking whether an event should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @dataProvider data_provider_should_not_hit
	 *
	 * @param array $settings Reaction settings.
	 */
	public function test_should_hit_not( array $settings ) {

		$this->mock_apps();

		wordpoints_entities()->children->register(
			'test_entity'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Attr'
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'unmet', 'data_type' => 'text' )
		);

		$this->factory->wordpoints->hook_condition->create(
			array( 'slug' => 'unmet', 'data_type' => 'entity' )
		);

		$extension = new WordPoints_Hook_Extension_Conditions();
		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( $settings, $reactor );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$this->assertFalse( $extension->should_hit( $validator, $event_args ) );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );
	}

	/**
	 * Provides an array of possible settings which should not hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Settings that should not cause the target to be hit.
	 */
	public function data_provider_should_not_hit() {

		$conditions = array(
			'_conditions' => array(
				array(
					'type'     => 'unmet',
					'settings' => array( 'value' => 'a' ),
				),
			),
		);

		$settings = array(
			'unmet_condition' => array(
				array( 'conditions' => array( 'test_entity' => $conditions ) ),
			),
			'unmet_child_condition' => array(
				array(
					'conditions' => array(
						'test_entity' => array( 'child' => $conditions ),
					),
				),
			),
		);

		return $settings;
	}

}

// EOF
