<?php

/**
 * Spam hook extension interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Interface for hook extensions that need to listen to spam actions.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Extension_SpamI {

	/**
	 * Called after a spam action has occurred.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_EventI        $event      The event object.
	 * @param WordPoints_Hook_Event_Args    $event_args The event args.
	 * @param WordPoints_Hook_Reactor_SpamI $reactor    The reactor.
	 */
	public function after_spam(
		WordPoints_Hook_EventI $event,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_Reactor_SpamI $reactor
	);
}

// EOF
