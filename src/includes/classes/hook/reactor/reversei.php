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
	 * @param string                     $event_slug The event slug.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 */
	public function reverse_hits( $event_slug, WordPoints_Hook_Event_Args $event_args );
}

// EOF
