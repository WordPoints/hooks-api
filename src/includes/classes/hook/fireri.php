<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

interface WordPoints_Hook_FirerI {
	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args );
}

// EOF
