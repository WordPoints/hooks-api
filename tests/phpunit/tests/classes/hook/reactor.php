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
	 * Test accessing the reactions storage object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->reactions_class = 'WordPoints_Hook_Reaction_Storage_Options';

		$this->assertInstanceOf( $reactor->reactions_class, $reactor->reactions );
		$this->assertEquals( 'test_reactor', $reactor->reactions->get_reactor_slug() );
		$this->assertFalse( $reactor->reactions->is_network_wide() );
		$this->assertTrue( $reactor->reactions === $reactor->reactions );
	}

	/**
	 * Test accessing the network reactions storage object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_network_reactions() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->network_reactions_class = 'WordPoints_Hook_Reaction_Storage_Options_Network';

		$this->assertInstanceOf( $reactor->network_reactions_class, $reactor->network_reactions );
		$this->assertEquals( 'test_reactor', $reactor->network_reactions->get_reactor_slug() );
		$this->assertTrue( $reactor->network_reactions->is_network_wide() );
		$this->assertTrue( $reactor->network_reactions === $reactor->network_reactions );
	}

	/**
	 * Test accessing the network reactions storage object when no class is specified.
	 *
	 * @since 1.0.0
	 */
	public function test_get_network_reactions_not() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();

		$reactor->network_reactions_class = null;

		$this->assertNull( $reactor->network_reactions );
	}

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
	 * Test getting all reactions to an event.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPoints network-active
	 */
	public function test_get_all_reactions_to_event() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$network_reaction = $reactor->network_reactions->create_reaction(
			array( 'event' => 'test_event', 'target' => array( 'test_entity' ) )
		);

		$this->assertIsReaction( $network_reaction );

		$this->assertEquals(
			array( $reaction, $network_reaction )
			, $reactor->get_all_reactions_to_event( 'test_event' )
		);
	}

	/**
	 * Test getting all reactions to an event when network reactions aren't supported.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPoints network-active
	 */
	public function test_get_all_reactions_to_event_no_network() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$reactor->network_reaction_class = null;

		$this->assertEquals(
			array( $reaction )
			, $reactor->get_all_reactions_to_event( 'test_event' )
		);
	}

	/**
	 * Test getting all reactions to an event when WordPoints isn't network-active.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_get_all_reactions_to_event_not_network_active() {

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$network_reaction = $reactor->network_reactions->create_reaction(
			array( 'event' => 'test_event', 'target' => array( 'test_entity' ) )
		);

		$this->assertIsReaction( $network_reaction );

		$this->assertEquals(
			array( $reaction )
			, $reactor->get_all_reactions_to_event( 'test_event' )
		);
	}

	/**
	 * Test validating the settings.
	 *
	 * @since 1.0.0
	 */
	public function test_validate_settings() {

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor();
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
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
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
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
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
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
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
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
		$validator = new WordPoints_Hook_Reaction_Validator( array(), $reactor );
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

		$this->mock_apps();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		/** @var WordPoints_PHPUnit_Mock_Hook_Reaction $reaction */
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
}

// EOF