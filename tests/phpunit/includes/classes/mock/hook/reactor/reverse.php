<?php

/**
 * Mock reverse hook reactor class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock reverse hook reactor for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Reactor_Reverse
	extends WordPoints_PHPUnit_Mock_Hook_Reactor
	implements WordPoints_Hook_Reactor_ReverseI {

	/**
	 * A list of spam hits this reactor has received.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $reverse_hits = array();

	/**
	 * @since 1.0.0
	 */
	public function reverse_hits( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$this->reverse_hits[] = array(
			'event_slug' => $event_slug,
			'event_args' => $event_args,
		);
	}
}

// EOF
