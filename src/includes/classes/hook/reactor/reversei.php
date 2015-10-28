<?php

/**
 * Reverse hook reactor interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by hook reactors that need to listen to reverse actions.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Reactor_ReverseI {

	/**
	 * Reverses all hits matching this event and args.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 * @param WordPoints_Hook_EventI     $event      The object for the event.
	 */
	public function reverse_hits(
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_EventI $event
	);
}

// EOF
