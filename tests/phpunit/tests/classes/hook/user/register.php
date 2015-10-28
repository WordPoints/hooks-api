<?php

/**
 * Test case for the User Register hook event.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the User Register hook event.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Event_User_Register
 */
class WordPoints_User_Register_Hook_Event_Test extends WordPoints_PHPUnit_TestCase_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	protected $event_class = 'WordPoints_Hook_Event_User_Register';

	/**
	 * @since 1.0.0
	 */
	protected $event_slug = 'user_register';

	/**
	 * Test getting the title.
	 *
	 * @since 1.0.0
	 */
	public function test_get_title() {
		$this->assertNotEmpty( $this->event->get_title() );
	}

	/**
	 * Test getting the description.
	 *
	 * @since 1.0.0
	 */
	public function test_get_description() {
		$this->assertNotEmpty( $this->event->get_description() );
	}

	/**
	 * Test getting the retroactive description.
	 *
	 * @since 1.0.0
	 */
	public function test_get_retroactive_description() {
		if ( $this->event instanceof WordPoints_Hook_Event_RetroactiveI ) {
			$this->assertNotEmpty( $this->event->get_retroactive_description() );
		}
	}

	/**
	 * Test that the event fires when a new user registers.
	 *
	 * @since 1.0.0
	 */
	public function test_fires_user_register() {

		$reactor = $this->hooks->reactors->get( 'points' );

		$this->create_points_type();

		$reaction = $reactor->reactions->create_reaction(
			array(
				'event' => $this->event_slug,
				'description' => 'Test Description',
				'log_text' => 'Test Log Text',
				'target' => array( 'user' ),
				'points' => 10,
				'points_type' => 'points',
			)
		);

		$this->assertIsReaction( $reaction );

		$user_id = $this->factory->user->create();

		$this->assertEquals( 10, wordpoints_get_points( $user_id, 'points' ) );
	}

	/**
	 * Test firing the event.
	 *
	 * @since 1.0.0
	 */
	public function _test_fire() {

		// Unit test with custom reactor.
		$reactors = new WordPoints_Class_Registry_Persistent();
		$reactors->register( 'test_reactor', 'WordPoints_PHPUnit_Mock_Hook_Reactor' );

		$this->hooks->reactors = $reactors;

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $reactors->get( 'test_reactor' );

		$this->hooks->events->register(
			'test_event'
			, $this->event_class
			, array(
				'action' => 'test_action',
				'reverse_action' => 'test_reverse_action',
			)
		);

		$reaction = $reactor->reactions->create_reaction(
			array(
				'event' => 'test_event',
				'description' => 'Test Description',
				'target' => array(),
			)
		);

		$this->assertIsReaction( $reaction );

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test_action', array( 1 ) );

		$this->event = new $this->event_class( 'test_event' );
		$this->event->fire( $action );

		$this->assertCount( 1, $reactor->hits );
	}
}

// EOF
