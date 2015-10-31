<?php

/**
 * Reverse hook extension class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Interface for hook extensions that need to listen to reverse actions.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Extension_ReverseI {

	/**
	 * Called after a reverse action is called.
	 *
	 * @since 1.0.0
	 *
	 * @param string                           $event_slug The event slug.
	 * @param WordPoints_Hook_Event_Args       $event_args The event args.
	 * @param WordPoints_Hook_Reactor_ReverseI $reactor    The reactor object.
	 */
	public function after_reverse(
		$event_slug,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_Reactor_ReverseI $reactor
	);
}

// EOF