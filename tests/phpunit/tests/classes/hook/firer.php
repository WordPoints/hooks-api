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
		$this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$this->listen_for_filter( 'wordpoints_hook_event_hit' );

		$firer->do_event( 'test_event', $event_args );

		$this->assertEquals(
			3
			, $this->filter_was_called( 'wordpoints_hook_event_hit' )
		);

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 3, $extension->hit_checks );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 3, $extension->hit_checks );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );
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

		$this->listen_for_filter( 'wordpoints_hook_event_hit' );

		$firer->do_event( 'test_event', $event_args );

		$this->assertEquals(
			0
			, $this->filter_was_called( 'wordpoints_hook_event_hit' )
		);

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->hit_checks );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->hit_checks );
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
		$this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$this->listen_for_filter( 'wordpoints_hook_event_hit' );

		$firer->do_event( 'test_event', $event_args );

		$this->assertEquals(
			2
			, $this->filter_was_called( 'wordpoints_hook_event_hit' )
		);

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->hit_checks );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->hit_checks );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

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
		$this->factory->wordpoints->hook_reaction->create_many( 2 );

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$this->listen_for_filter( 'wordpoints_hook_event_hit' );

		$firer->do_event( 'test_event', $event_args );

		$this->assertEquals(
			3
			, $this->filter_was_called( 'wordpoints_hook_event_hit' )
		);

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 2, $reactor->hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );
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

		/** @var WordPoints_Hook_ReactionI $reaction */
		$reaction = $this->factory->wordpoints->hook_reaction->create_and_get();
		$reaction->delete_meta( 'event' );

		$this->factory->wordpoints->hook_reaction->create();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'slug' => 'another' )
		);

		$this->factory->wordpoints->hook_reaction->create(
			array( 'reactor' => 'another' )
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$this->listen_for_filter( 'wordpoints_hook_event_hit' );

		$firer->do_event( 'test_event', $event_args );

		$this->assertEquals(
			2
			, $this->filter_was_called( 'wordpoints_hook_event_hit' )
		);

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->hit_checks );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->hit_checks );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->hits );
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

		$this->listen_for_filter( 'wordpoints_hook_event_hit' );

		$extension = $hooks->extensions->get( 'test_extension' );
		$extension->should_hit = false;

		$firer->do_event( 'test_event', $event_args );

		$this->assertEquals(
			0
			, $this->filter_was_called( 'wordpoints_hook_event_hit' )
		);

		// The extensions should have each been checked.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 3, $extension->hit_checks );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->hit_checks );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 0, $reactor->hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 0, $reactor->hits );
	}
}

// EOF
