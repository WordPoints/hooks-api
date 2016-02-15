<?php

/**
 * Test case for WordPoints_Hook_Firer_Reverse.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Firer_Reverse.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Firer_Reverse
 */
class WordPoints_Hook_Firer_Reverse_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test firing an event.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $reaction->ID )
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->after_reverse );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $reaction->ID,
				'superseded_by' => $reactor->reverse_hits[0]->hit_id,
			)
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $reaction->ID,
				'superseded_by' => $reactor->reverse_hits[0]->hit_id,
			)
		);
	}

	/**
	 * Test firing an event when there are no hits to reverse.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_hits() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer      = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should not have been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );

		// The reactors should not have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->reverse_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->reverse_hits );
	}

	/**
	 * Test firing an event when one reactor is no longer registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reactor_no_longer_registered() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $reaction->ID )
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		// Deregister one reactor.
		$hooks->reactors->deregister( 'test_reactor' );

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called only once.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
			, 0
		);

		// The other reactor should have one hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);
	}

	/**
	 * Test firing an event when one reactor is not reversible.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reactor_not_reversible() {

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

		// The reactor will not be reversible by default.
		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $reaction->ID )
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called only once.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
			, 0
		);

		// The other reactor should have one hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);
	}

	/**
	 * Test firing an event when one reaction store is no longer registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_store_no_longer_registered() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $reaction->ID )
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		// Deregister one reaction store.
		$hooks->reaction_stores->deregister( 'test_reactor', 'standard' );

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called only once.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
			, 0
		);

		// The other reactor should have one hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);
	}

	/**
	 * Test firing an event when one reaction store from a different context ID.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_store_different_context_id() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$hooks->reaction_stores->register(
			'test_reactor'
			, 'standard'
			, 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted'
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $reaction->ID,
				'reaction_context_id' => $reaction->get_context_id(),
			)
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		// Change the store's context ID.
		WordPoints_PHPUnit_Mock_Entity_Context::$current_id = 2;

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called only once.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $reaction->ID,
				'reaction_context_id' => $reaction->get_context_id(),
			)
			, 0
		);

		// The other reactor should have one hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
				'reaction_context_id' => $another_reaction->get_context_id(),
			)
		);
	}

	/**
	 * Test firing an event when one reaction no longer exists.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_no_longer_exists() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $reaction->ID )
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		// Delete one reaction
		$reactor = $hooks->reactors->get( 'test_reactor' );
		$reactor->get_reaction_store( 'standard' )->delete_reaction( $reaction->ID );

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called only once.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
			, 0
		);

		// The other reactor should have one hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);
	}

	/**
	 * Test firing an event only reverses fires from that event.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_different_event() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $reaction->ID )
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
		);

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		// Reverse a different event.
		$firer->do_event( 'another_event', $event_args );

		// The extensions should not have been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );

		// The reactors should not have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $reaction->ID,
				'event' => 'another_event',
			)
			, 0
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
			)
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
				'event' => 'another_event',
			)
			, 0
		);
	}

	/**
	 * Test firing an event only reverses fires with the same primary arg value.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_different_primary_arg_value() {

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

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$entity_slug = $this->factory->wordpoints->entity->create();

		$event_args = new WordPoints_Hook_Event_Args(
			array( new WordPoints_Hook_Arg( $entity_slug ) )
		);

		$entity = $event_args->get_from_hierarchy( array( $entity_slug ) );
		$entity->set_the_value( 1 );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $event_args ),
			)
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $event_args ),
			)
		);

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		// Change the event arg ID.
		$entity->set_the_value( 2 );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should not have been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );

		// The reactors should not have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $event_args ),
			)
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $event_args ),
			)
			, 0
		);

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $event_args ),
			)
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $another_reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $event_args ),
			)
			, 0
		);
	}
}

// EOF
