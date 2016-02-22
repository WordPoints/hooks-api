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
	 * The hooks object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hooks
	 */
	protected $hooks;

	/**
	 * An event args object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Event_Args
	 */
	protected $event_args;

	/**
	 * A hook reactor.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible
	 */
	protected $reactor;

	/**
	 * Another hook reactor.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible
	 */
	protected $another_reactor;

	/**
	 * A hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_ReactionI
	 */
	protected $reaction;

	/**
	 * Another hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_ReactionI
	 */
	protected $another_reaction;

	/**
	 * Test firing an event.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event() {

		$this->fire_event();

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should have each been called.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->after_reverse );

		// The reactors should have been hit.
		$this->assertCount( 1, $this->reactor->reverse_hits );

		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $this->reaction->ID )
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $this->reaction->ID,
				'meta_query' => array(
					array(
						'key' => 'reversed_by',
						'value' => $this->reactor->reverse_hits[0]->hit_id,
					),
				),
			)
		);

		$this->assertOtherReactionReverseHit();
	}

	/**
	 * Test firing an event when there are no hits to reverse.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_hits() {

		$this->create_reactions();

		$this->event_args = new WordPoints_Hook_Event_Args( array() );
		$firer      = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should not have been called.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );

		// The reactors should not have been hit.
		$reactor = $this->hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->reverse_hits );

		$reactor = $this->hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->reverse_hits );
	}

	/**
	 * Test firing an event when one reactor is no longer registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reactor_no_longer_registered() {

		$this->fire_event();

		// Deregister one reactor.
		$this->hooks->reactors->deregister( 'test_reactor' );

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should have each been called only once.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertReactionNotReverseHit();

		// The other reactor should have one hit.
		$this->assertOtherReactionReverseHit();
	}

	/**
	 * Test firing an event when one reactor is not reversible.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reactor_not_reversible() {

		$this->fire_event();

		// Register a non-reversible reactor.
		$this->factory->wordpoints->hook_reactor->create();

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should have each been called only once.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertReactionNotReverseHit();

		// The other reactor should have one hit.
		$this->assertOtherReactionReverseHit();
	}

	/**
	 * Test firing an event when one reaction store is no longer registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_store_no_longer_registered() {

		$this->fire_event();

		// Deregister one reaction store.
		$this->hooks->reaction_stores->deregister( 'test_reactor', 'standard' );

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should have each been called only once.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertReactionNotReverseHit();

		// The other reactor should have one hit.
		$this->assertOtherReactionReverseHit();
	}

	/**
	 * Test firing an event when one reaction store from a different context ID.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_store_different_context_id() {

		$this->mock_apps();

		$this->hooks = wordpoints_hooks();

		$this->hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$this->hooks->reaction_stores->register(
			'test_reactor'
			, 'standard'
			, 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted'
		);

		$this->reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$this->another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$this->event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $this->event_args );

		// The reactors should have been hit.
		$reactor = $this->hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $this->reaction->ID,
				'reaction_context_id' => $this->reaction->get_context_id(),
			)
		);

		$another_reactor = $this->hooks->reactors->get( 'another' );

		$this->assertCount( 1, $another_reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
			)
		);

		// Change the store's context ID.
		WordPoints_PHPUnit_Mock_Entity_Context::$current_id = 2;

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should have each been called only once.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $this->reaction->ID,
				'reaction_context_id' => $this->reaction->get_context_id(),
			)
			, 0
		);

		// The other reactor should have one hit.
		$this->assertCount( 1, $another_reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
				'reaction_context_id' => $this->another_reaction->get_context_id(),
			)
		);
	}

	/**
	 * Test firing an event when one reaction no longer exists.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_no_longer_exists() {

		$this->fire_event();

		// Delete one reaction
		$this->reactor->get_reaction_store( 'standard' )->delete_reaction(
			$this->reaction->ID
		);

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should have each been called only once.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The first reactor should not have been hit.
		$this->assertReactionNotReverseHit();

		// The other reactor should have one hit.
		$this->assertOtherReactionReverseHit();
	}

	/**
	 * Test firing an event only reverses fires from that event.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_different_event() {

		$this->fire_event();

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		// Reverse a different event.
		$firer->do_event( 'another_event', $this->event_args );

		// The extensions should not have been called.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );

		// The reactors should not have been hit.
		$this->assertCount( 0, $this->reactor->reverse_hits );

		// No reverse hit should have been logged.
		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $this->reaction->ID,
				'event' => 'another_event',
			)
			, 0
		);

		// The original hit should not have been marked as non-reversed.
		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $this->reaction->ID,
				'meta_query' => array(
					array( 'key' => 'reversed_by', 'value' => 0 ),
				),
			)
			, 0
		);

		$this->assertCount( 0, $this->another_reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
			)
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
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

		$this->create_reactions();

		$entity_slug = $this->factory->wordpoints->entity->create();

		$this->event_args = new WordPoints_Hook_Event_Args(
			array( new WordPoints_Hook_Arg( $entity_slug ) )
		);

		$entity = $this->event_args->get_from_hierarchy( array( $entity_slug ) );
		$entity->set_the_value( 1 );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $this->event_args );

		// The reactors should have been hit.
		$reactor = $this->hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $this->reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args ),
			)
		);

		$another_reactor = $this->hooks->reactors->get( 'another' );

		$this->assertCount( 1, $another_reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args ),
			)
		);

		$firer = new WordPoints_Hook_Firer_Reverse( 'reverse' );

		// Change the event arg ID.
		$entity->set_the_value( 2 );

		$firer->do_event( 'test_event', $this->event_args );

		// The extensions should not have been called.
		$extension = $this->hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $this->hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );

		// The reactors should not have been hit.
		$this->assertCount( 0, $reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $this->reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args ),
			)
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $this->reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args ),
			)
			, 0
		);

		$this->assertCount( 0, $another_reactor->reverse_hits );

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args ),
			)
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args ),
			)
			, 0
		);
	}

	/**
	 * Fire the event for the reactors.
	 *
	 * @since 1.0.0
	 */
	public function fire_event() {

		$this->create_reactions();

		$this->event_args = new WordPoints_Hook_Event_Args( array() );

		$firer = new WordPoints_Hook_Firer( 'fire' );

		$firer->do_event( 'test_event', $this->event_args );

		// The reactors should have been hit.
		$this->reactor = $this->hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $this->reactor->hits );

		$this->assertHitsLogged(
			array( 'firer' => 'fire', 'reaction_id' => $this->reaction->ID )
		);

		$this->another_reactor = $this->hooks->reactors->get( 'another' );

		$this->assertCount( 1, $this->another_reactor->hits );

		$this->assertHitsLogged(
			array(
				'firer'       => 'fire',
				'reactor'     => 'another',
				'reaction_id' => $this->another_reaction->ID,
			)
		);
	}

	/**
	 * Create two reactions for two different reactors.
	 *
	 * @since 1.0.0
	 */
	public function create_reactions() {

		$this->mock_apps();

		$this->hooks = wordpoints_hooks();
		$this->hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible' )
		);

		$this->reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug'  => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reversible',
			)
		);

		$this->another_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);
	}

	/**
	 * Assert that the primary reaction wasn't hit.
	 *
	 * @since 1.0.0
	 */
	public function assertReactionNotReverseHit() {

		$this->assertCount( 0, $this->reactor->reverse_hits );

		// No hits should have been logged.
		$this->assertHitsLogged(
			array( 'firer' => 'reverse', 'reaction_id' => $this->reaction->ID )
			, 0
		);

		// The original hit should have had its reversed_by meta key set to 0.
		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reaction_id' => $this->reaction->ID,
				'meta_query' => array(
					array( 'key' => 'reversed_by', 'value' => 0 ),
				),
			)
		);
	}

	/**
	 * Assert that the other reaction was hit.
	 *
	 * @since 1.0.0
	 */
	public function assertOtherReactionReverseHit() {

		$this->assertCount( 1, $this->another_reactor->reverse_hits );

		// The reverse hit should have been logged.
		$this->assertHitsLogged(
			array(
				'firer'       => 'reverse',
				'reactor'     => 'another',
				'reaction_id' => $this->another_reaction->ID,
			)
		);

		// The original hit should have been marked as reversed.
		$this->assertHitsLogged(
			array(
				'firer' => 'fire',
				'reactor' => 'another',
				'reaction_id' => $this->another_reaction->ID,
				'meta_query' => array(
					array(
						'key' => 'reversed_by',
						'value' => $this->another_reactor->reverse_hits[0]->hit_id,
					),
				),
			)
		);
	}
}

// EOF
