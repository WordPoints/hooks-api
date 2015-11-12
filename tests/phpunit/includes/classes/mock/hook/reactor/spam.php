<?php

/**
 * Mock spam hook reactor class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock spam hook reactor for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Reactor_Spam
	extends WordPoints_PHPUnit_Mock_Hook_Reactor
	implements WordPoints_Hook_Reactor_SpamI {

	/**
	 * A list of spam hits this reactor has received.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $spam_hits = array();

	/**
	 * @since 1.0.0
	 */
	public function spam_hits( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$this->spam_hits[] = array(
			'event_slug' => $event_slug,
			'event_args' => $event_args,
		);
	}
}

// EOF
