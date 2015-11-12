<?php

/**
 * Mock spam hook extension class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock spam hook extension class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Extension_Spam
	extends WordPoints_PHPUnit_Mock_Hook_Extension
	implements WordPoints_Hook_Extension_SpamI {

	/**
	 * The args passed to after_spam() each time it was called.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $after_spam = array();

	/**
	 * @since 1.0.0
	 */
	public function after_spam(
		$event_slug,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_Reactor_SpamI $reactor
	) {
		$this->after_spam[] = array(
			'event_slug' => $event_slug,
			'event_args' => $event_args,
			'reactor'    => $reactor,
		);
	}
}

// EOF
