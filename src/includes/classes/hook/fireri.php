<?php

/**
 * Hook firer interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Defines the API for a hook event firer.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_FirerI {

	/**
	 * Fire an event.
	 *
	 * @since 1.0.0
	 *
	 * @param string                     $event_slug The event slug.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 */
	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args );
}

// EOF
