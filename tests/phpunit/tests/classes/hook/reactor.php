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
	 * Test getting a reaction store.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_store() {

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reaction_store = $reactor->get_reaction_store( 'standard' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Hook_Reaction_Store'
			, $reaction_store
		);

		$this->assertEquals( 'test_reactor', $reaction_store->get_reactor_slug() );
	}

	/**
	 * Test getting an unregistered reaction store.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_store_unregistered() {

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array( 'stores' => array() )
		);

		$this->assertFalse( $reactor->get_reaction_store( 'standard' ) );
	}

	/**
	 * Test getting a reaction store when out of context.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_store_out_of_context() {

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted',
				),
			)
		);

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context_OutOfState'
		);

		$this->assertFalse( $reactor->get_reaction_store( 'standard' ) );
	}

	/**
	 * Test accessing the $reactions property.
	 *
	 * @since 1.0.0
	 */
	public function test_reactions() {

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get();

		$reaction_store = $reactor->reactions;

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Hook_Reaction_Store'
			, $reaction_store
		);

		$this->assertEquals( 'test_reactor', $reaction_store->get_reactor_slug() );
	}

	/**
	 * Test accessing the $reactions property when no store is registered for the
	 * current mode.
	 *
	 * @since 1.0.0
	 */
	public function test_reactions_unregistered() {

		$this->mock_apps();

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array( 'stores' => array() )
		);

		$this->assertNull( $reactor->reactions );
	}

	/**
	 * Test accessing the $reactions property when the store for the current mode is
	 * out of context.
	 *
	 * @since 1.0.0
	 */
	public function test_reactions_out_of_context() {

		$this->mock_apps();

		wordpoints_hooks()->set_current_mode( 'standard' );

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted',
				),
			)
		);

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context_OutOfState'
		);

		$this->assertNull( $reactor->reactions );
	}

	/**
	 * Test getting all reactions.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions() {

		$this->mock_apps();

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context'
		);

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_Hook_Reaction_Store_Options',
					'custom' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted',
				),
			)
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$another = $this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$custom_store = $reactor->get_reaction_store( 'custom' );

		$custom_reaction = $custom_store->create_reaction(
			array( 'event' => 'test_event', 'target' => array( 'test_entity' ) )
		);

		$another_custom = $custom_store->create_reaction(
			array( 'event' => 'another', 'target' => array( 'test_entity' ) )
		);

		$this->assertIsReaction( $another_custom );

		$this->assertEquals(
			array( $reaction, $custom_reaction )
			, $reactor->get_all_reactions_to_event( 'test_event' )
		);

		$this->assertEquals(
			array( $reaction, $another, $custom_reaction, $another_custom )
			, $reactor->get_all_reactions()
		);
	}

	/**
	 * Test getting all reactions when some stores have none registered.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_unregistered() {

		$this->mock_apps();

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context'
		);

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_Hook_Reaction_Store_Options',
					'custom' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted',
				),
			)
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$another = $this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$this->assertEquals(
			array( $reaction )
			, $reactor->get_all_reactions_to_event( 'test_event' )
		);

		$this->assertEquals(
			array( $reaction, $another )
			, $reactor->get_all_reactions()
		);
	}

	/**
	 * Test getting reactions when a reaction store is out of context.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reactions_out_of_context() {

		$this->mock_apps();

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context'
		);

		$reactor = $this->factory->wordpoints->hook_reactor->create_and_get(
			array(
				'stores' => array(
					'standard' => 'WordPoints_Hook_Reaction_Store_Options',
					'custom' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted',
				),
			)
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$another = $this->factory->wordpoints->hook_reaction->create(
			array( 'event' => 'another' )
		);

		$this->assertIsReaction( $another );

		$custom_store = $reactor->get_reaction_store( 'custom' );

		$custom_store->create_reaction(
			array( 'event' => 'test_event', 'target' => array( 'test_entity' ) )
		);

		$another_custom = $custom_store->create_reaction(
			array( 'event' => 'another', 'target' => array( 'test_entity' ) )
		);

		$this->assertIsReaction( $another_custom );

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context_OutOfState'
		);

		$this->assertEquals(
			array( $reaction )
			, $reactor->get_all_reactions_to_event( 'test_event' )
		);

		$this->assertEquals(
			array( $reaction, $another )
			, $reactor->get_all_reactions()
		);
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
}

// EOF
