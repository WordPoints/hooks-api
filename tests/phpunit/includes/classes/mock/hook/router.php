<?php

/**
 * Mock hook router class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook router for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Router extends WordPoints_Hook_Router {

	/**
	 * A list of event fires.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	public $fires = array();

	/**
	 * @since 1.0.0
	 */
	public function fire_event(
		$action_type,
		$event_slug,
		WordPoints_Hook_Event_Args $event_args
	) {

		$this->fires[] = array(
			'action_type' => $action_type,
			'event_args'  => $event_args,
			'event_slug'  => $event_slug,
		);

		parent::fire_event( $action_type, $event_slug, $event_args );
	}
}

// EOF
