<?php

/**
 * Test case for WordPoints_Hook_Firer_Spam.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Firer_Spam.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Firer_Spam
 */
class WordPoints_Hook_Firer_Spam_Test extends WordPoints_PHPUnit_TestCase_Hooks {

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
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam' )
		);

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Spam( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 2, $extension->after_spam );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->after_spam );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->spam_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->spam_hits );
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
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Spam( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 0, $extension->after_spam );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 0, $extension->after_spam );
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
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam' )
		);

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Spam( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->spam_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->spam_hits );
	}

	/**
	 * Test firing an event when one reactor doesn't react to spam.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_non_spam_reactor() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$this->factory->wordpoints->hook_reactor->create();

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Spam( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The extensions should have each been called.
		$extension = $hooks->extensions->get( 'test_extension' );

		$this->assertCount( 1, $extension->after_spam );

		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 1, $extension->after_spam );

		// The spam reactor should have been hit.
		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->spam_hits );
	}

	/**
	 * Test firing an event when an extension doesn't listen for spam hits.
	 *
	 * @since 1.0.0
	 */
	public function test_do_event_non_spam_extension() {

		$this->mock_apps();

		$hooks = wordpoints_hooks();

		$hooks->extensions->register(
			'test_extension'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension'
		);

		$hooks->extensions->register(
			'another'
			, 'WordPoints_PHPUnit_Mock_Hook_Extension_Spam'
		);

		$this->factory->wordpoints->hook_reactor->create(
			array( 'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam' )
		);

		$this->factory->wordpoints->hook_reactor->create(
			array(
				'slug' => 'another',
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reactor_Spam',
			)
		);

		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer_Spam( 'test_firer' );

		$firer->do_event( 'test_event', $event_args );

		// The spam extension should have been called.
		$extension = $hooks->extensions->get( 'another' );

		$this->assertCount( 2, $extension->after_spam );

		// The reactors should have been hit.
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->spam_hits );

		$reactor = $hooks->reactors->get( 'another' );

		$this->assertCount( 1, $reactor->spam_hits );
	}
}

// EOF
