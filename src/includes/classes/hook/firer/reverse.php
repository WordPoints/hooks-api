<?php

/**
 * Reverse hook firer class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Fires a reverse action for a hook event.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Firer_Reverse implements WordPoints_Hook_FirerI {

	/**
	 * @since 1.0.0
	 */
	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$hooks = wordpoints_hooks();

		foreach ( $hooks->reactors->get_all() as $reactor ) {

			if ( ! ( $reactor instanceof WordPoints_Hook_Reactor_ReverseI ) ) {
				continue;
			}

			$reactor->reverse_hits( $event_slug, $event_args );

			foreach ( $hooks->extensions->get_all() as $extension ) {
				if ( $extension instanceof WordPoints_Hook_Extension_ReverseI ) {
					$extension->after_reverse( $event_slug, $event_args, $reactor );
				}
			}
		}
	}
}

// EOF
