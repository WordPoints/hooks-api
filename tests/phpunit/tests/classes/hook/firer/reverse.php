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
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse' )
		);

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Reverse( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->after_reverse );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );
	}

	/**
	 * Test firing an event with no reactors.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_reactors() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Reverse( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_reverse );
	}

	/**
	 * Test firing an event with no extensions registered.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_no_extensions() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse' )
		);

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Reverse( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );
	}

	/**
	 * Test firing an event when one reactor doesn't react to reverse.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_non_reverse_reactor() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$this->factory->wordpoints->hook_reactor->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Reverse( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_reverse );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_reverse );

		// The reverse reactor should have been hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );
	}

	/**
	 * Test firing an event when an extension doesn't listen for reverse hits.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_non_reverse_extension() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Reverse'
		);

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse' )
		);

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Reverse( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The reverse extension should have been called.
		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->after_reverse );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->reverse_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->reverse_hits );
	}
}

// EOF
