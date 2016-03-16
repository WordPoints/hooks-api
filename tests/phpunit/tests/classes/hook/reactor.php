<?php

/**
 * Test case for WordPoints_Hook_Reactor.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reactor.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reactor
 */
class WordPoints_Hook_Reactor_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->slug = 'test_reactor';

		$this->assertEquals( 'test_reactor', $reactor->get_slug() );
	}

	/**
	 * Test getting the arg types.
	 *
	 * @since 1.0.0
	 */
	public function test_get_arg_types() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->arg_types = array( 'test_entity', 'another' );

		$this->assertEquals( $reactor->arg_types, $reactor->get_arg_types() );
	}

	/**
	 * Test getting the arg types returns an array even when the value is a string.
	 *
	 * @since 1.0.0
	 */
	public function test_get_arg_types_string() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->arg_types = 'test_entity';

		$this->assertEquals(
			array( $reactor->arg_types )
			, $reactor->get_arg_types()
		);
	}

	/**
	 * Test getting the reactor context.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_get_context() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor;

		$this->assertEquals( 'site', $reactor->get_context() );
	}

	/**
	 * Test getting the reactor context when WordPoints is network active.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPoints network-active
	 */
	public function test_get_context_network_active() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor;

		$this->assertEquals( 'network', $reactor->get_context() );
	}

	/**
	 * Test getting the settings fields.
	 *
	 * @since 1.0.0
	 */
	public function test_get_settings_fields() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->settings_fields = array( 'field' => array() );

		$this->assertEquals(
			$reactor->settings_fields
			, $reactor->get_settings_fields()
		);
	}

	/**
	 * Test getting the data for the scripts used in the UI.
	 *
	 * @since 1.0.0
	 */
	public function test_get_ui_script_data() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->settings_fields = array( 'field' => array() );
		$reactor->arg_types = 'test_arg_type';

		$this->assertEquals(
			array(
				'slug'      => 'test_reactor',
				'fields'    => $reactor->settings_fields,
				'arg_types' => array( 'test_arg_type' ),
			)
			, $reactor->get_ui_script_data()
		);
	}

	/**
	 * Test validating the settings.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$settings = array(
			'target' => array( 'test_entity' ),
			'key' => 'value',
		);

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertFalse( $validator->had_errors() );
		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Test validating the settings when the target isn't set.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings_target_not_set() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$settings = array(
			'key' => 'value',
		);

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 1, $errors );
		$this->assertEquals( array( 'target' ), $errors[0]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Test validating the settings when the target isn't an array.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings_target_not_array() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$settings = array(
			'target' => 'test_entity',
			'key' => 'value',
		);

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 1, $errors );
		$this->assertEquals( array( 'target' ), $errors[0]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Test validating the settings when the target isn't a valid hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings_target_not_valid_hierarchy() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'test_entity' )
		);

		$event_args->set_validator( $validator );

		$settings = array(
			'target' => array( 'another_entity' ),
			'key' => 'value',
		);

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 2, $errors );
		$this->assertEquals( array(), $errors[0]['field'] );
		$this->assertEquals( array( 'target' ), $errors[1]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Test validating the settings when the target isn't of the correct type(s).
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings_target_not_correct_type() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$event_args = new WordPoints_Hook_Event_Args( array() );

		$event_args->add_entity(
			new WordPoints_PHPUnit_Mock_Entity( 'another_entity' )
		);

		$event_args->set_validator( $validator );

		$settings = array(
			'target' => array( 'another_entity' ),
			'key' => 'value',
		);

		$result = $reactor->validate_settings( $settings, $validator, $event_args );

		$this->assertTrue( $validator->had_errors() );

		$errors = $validator->get_errors();

		$this->assertCount( 1, $errors );
		$this->assertEquals( array( 'target' ), $errors[0]['field'] );

		$this->assertEmpty( $validator->get_field_stack() );
		$this->assertNull( $event_args->get_current() );

		$this->assertEquals( $settings, $result );
	}

	/**
	 * Test updating the settings updates the target.
	 *
	 * @since 1.0.0
	 */
	public function test_update_settings() {

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->assertEquals(
			array( 'test_entity' )
			, $reaction->get_meta( 'target' )
		);

		$settings = array(
			'target' => array( 'another_entity' ),
			'key' => 'value',
		);

		$reactor->update_settings( $reaction, $settings );

		$this->assertEquals(
			$settings['target']
			, $reaction->get_meta( 'target' )
		);
	}

	/**
	 * Test processing a hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit() {

		$fire = new WordPoints_Hook_Fire(
			'test_fire'
			, new WordPoints_Hook_Event_Args( array() )
			, $this->factory->wordpoints->hook_reaction->create()
		);

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$reactor->hit( 'test', $fire );

		$this->assertCount( 1, $reactor->hits );
	}

	/**
	 * Test processing a hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_array_of_types() {

		$fire = new WordPoints_Hook_Fire(
			'test_fire'
			, new WordPoints_Hook_Event_Args( array() )
			, $this->factory->wordpoints->hook_reaction->create()
		);

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$reactor->hit_types = array( 'test', 'another' );

		$reactor->hit( 'test', $fire );

		$this->assertCount( 1, $reactor->hits );
	}

	/**
	 * Test processing a hit of an unknown type.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_unknown_type() {

		$fire = new WordPoints_Hook_Fire(
			'test_fire'
			, new WordPoints_Hook_Event_Args( array() )
			, $this->factory->wordpoints->hook_reaction->create()
		);

		$action = new WordPoints_Mock_Filter();
		add_action( 'wordpoints_hook_reactor_hit-test_reactor', array( $action, 'action' ), 10, 6 );

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$reactor->hit( 'unknown', $fire );

		$this->assertCount( 0, $reactor->hits );

		$this->assertEquals( 1, $action->call_count );
		$this->assertEquals( array( 'unknown', $fire ), $action->calls[0] );
	}
}

// EOF
