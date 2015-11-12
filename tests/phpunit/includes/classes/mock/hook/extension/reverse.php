<?php

/**
 * Mock reverse hook extension class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock reverse hook extension class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Extension_Reverse
	extends WordPoints_PHPUnit_Mock_Hook_Extension
	implements WordPoints_Hook_Extension_ReverseI {

	/**
	 * The args passed to after_reverse() each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $after_reverse = array();

	/**
	 * @since 1.0.0
	 */
	public function after_reverse(
		$event_slug,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_Reactor_ReverseI $reactor
	) {
		$this->after_reverse[] = array(
			'event_slug' => $event_slug,
			'event_args' => $event_args,
			'reactor'    => $reactor,
		);
	}
}

// EOF
