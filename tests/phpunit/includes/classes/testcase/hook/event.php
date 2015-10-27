<?php

/**
 * Parent test case class for the hook event tests.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case for testing a hook event.
 *
 * @since 1.0.0
 */
abstract class WordPoints_PHPUnit_TestCase_Hook_Event extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * The class of the event being tested.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $event_class;

	/**
	 * An instance of the event being tested.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_EventI
	 */
	protected $event;

	protected $event_slug;

	protected $hooks;

	/**
	 * @since 1.0.0
	 */
	public function setUp() {

		parent::setUp();

		$this->event = new $this->event_class( $this->event_slug );
		$this->hooks = wordpoints_apps()->hooks;

		if ( ! isset( $this->factory->wordpoints ) ) {
			$this->factory->wordpoints = WordPoints_PHPUnit_Factory::$factory;
		}
	}
}

// EOF
