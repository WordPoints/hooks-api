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

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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

		$this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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
	 * Test firing an event when one reaction group is no longer registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_reaction_group_no_longer_registered() {

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

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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

		// Deregister one reaction group.
		$hooks->reaction_groups->deregister( 'test_reactor', 'standard' );

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

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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
		$reactor->get_reaction_group( 'standard' )->delete_reaction( $reaction->ID );

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

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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
	 * Test firing an event only reverses fires with the same signature.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_different_signature() {

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

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
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

		// Change the event args.
		$event_args = new WordPoints_Hook_Event_Args(
			array(
				new WordPoints_Hook_Arg(
					$this->factory->wordpoints->entity->create()
				)
			)
		);

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
			array( 'firer' => 'reverse', 'reaction_id' => $reaction->ID )
			, 0
		);

		$this->assertHitsLogged(
			array(
				'firer' => 'reverse',
				'reaction_id' => $reaction->ID,
				'signature' => wordpoints_hooks_get_event_signature( $event_args ),
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
				'signature' => wordpoints_hooks_get_event_signature( $event_args ),
			)
			, 0
		);
	}
}

// EOF
