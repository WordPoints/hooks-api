<?php

/**
 * Hook firer class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Fires a hook event.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Firer implements WordPoints_Hook_FirerI {

	/**
	 * @since 1.0.0
	 */
	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$hooks = wordpoints_hooks();

		/** @var WordPoints_Hook_Reactor $reactor */
		foreach ( $hooks->reactors->get_all() as $reactor ) {

			foreach ( $reactor->get_all_reactions_to_event( $event_slug ) as $reaction ) {

				$validator = new WordPoints_Hook_Reaction_Validator(
					$reaction
					, $reactor
					, true
				);

				$validator->validate();

				if ( $validator->had_errors() ) {
					continue;
				}

				unset( $validator );

				/** @var WordPoints_Hook_Extension[] $extensions */
				$extensions = $hooks->extensions->get_all();

				foreach ( $extensions as $extension ) {
					if ( ! $extension->should_hit( $reaction, $event_args ) ) {
						continue 2;
					}
				}

				$reactor->hit( $event_args, $reaction );

				foreach ( $extensions as $extension ) {
					$extension->after_hit( $reaction, $event_args );
				}
			}
		}
	}
}

// EOF
