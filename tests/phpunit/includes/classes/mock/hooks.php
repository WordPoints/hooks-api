<?php

/**
 * Mock hooks app class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hooks app class for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hooks extends WordPoints_Hooks {

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
	public function fire(
		$action_type,
		$event_slug,
		WordPoints_Hook_Event_Args $event_args
	) {

		$this->fires[] = array(
			'action_type' => $action_type,
			'event_args'  => $event_args,
			'event_slug'  => $event_slug,
		);

		parent::fire( $action_type, $event_slug, $event_args );
	}
}

// EOF
