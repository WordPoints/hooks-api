<?php

/**
 * Spam hook reactor interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by hook reactors that need to listen to spam actions.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Reactor_SpamI {

	/**
	 * Marks all hits matching this event and args as spam.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 * @param WordPoints_Hook_EventI     $event      The object for the event.
	 */
	public function spam_hits(
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_EventI $event
	);
}

// EOF
