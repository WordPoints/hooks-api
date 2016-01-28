<?php

/**
 * Mock hook firer class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook firer for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Firer extends WordPoints_Hook_Firer {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'test_firer';

	/**
	 * A list of times this firer has been fired.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $fires = array();

	/**
	 * Fire an event.
	 *
	 * @since 1.0.0
	 *
	 * @param string                     $event_slug The event slug.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 */
	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args ) {
		$this->fires[] = array(
			'event_args' => $event_args,
			'event_slug' => $event_slug,
		);
	}
}

// EOF
