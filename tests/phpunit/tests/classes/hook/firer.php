<?php

/**
 * Test case for WordPoints_Hook_Firer.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Firer.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Firer
 */
class WordPoints_Hook_Firer_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug() {

		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$this->assertEquals( 'test_firer', $firer->get_slug() );
	}

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

		$this->factory->wordpoints->hook_reactor->create();
		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 3, $extension->hit_checks );
		$this->assertCount( 3, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 3, $extension->hit_checks );
		$this->assertCount( 3, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reactions[0]->ID ) );
		$this->assertHitsLogged( array( 'reaction_id' => $reactions[1]->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged(
			array( 'reactor' => 'another', 'reaction_id' => $other_reaction->ID )
		);
	}

	/**
	 * Test firing an event when no reactors are registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_reactors() {

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

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );
	}

	/**
	 * Test firing an event when one reactor doesn't have any reactions for it.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_reactions() {

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

		$this->factory->wordpoints->hook_reactor->create();
		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reactions[0]->ID ) );
		$this->assertHitsLogged( array( 'reaction_id' => $reactions[1]->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->hits );
	}

	/**
	 * Test firing an event when no extensions.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_extensions() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$this->factory->wordpoints->hook_reactor->create();
		$reactions = $this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reactions[0]->ID ) );
		$this->assertHitsLogged( array( 'reaction_id' => $reactions[1]->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $other_reaction->ID ) );
	}

	/**
	 * Test firing an event.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_invalid_reaction() {

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

		$this->factory->wordpoints->hook_reactor->create();

		$this->factory->wordpoints->hook_reaction->create(
			array( 'test_extension' => array( 'fail' => true ) )
		);

		$reaction = $this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$other_reaction = $this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->hit_checks );
		$this->assertCount( 2, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );

		$this->assertHitsLogged( array( 'reaction_id' => $other_reaction->ID ) );
	}

	/**
	 * Test firing an event that an extension aborts.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_extension_aborted() {

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

		$this->factory->wordpoints->hook_reactor->create();
		$this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$extension = $hooks->extensions->get( 'test_extension' );
		$extension->should_hit = false;

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 3, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->hit_checks );
		$this->assertCount( 0, $extension->hits );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->hits );
	}
}

// EOF
