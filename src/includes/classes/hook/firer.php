<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Hook_Firer implements WordPoints_Hook_FirerI {

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug ) {

		$this->slug = $slug;
	}

	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$hooks = wordpoints_apps()->hooks;

		/** @var WordPoints_Hook_Reactor $reactor */
		foreach ( $hooks->reactors->get() as $reactor ) {

			foreach ( $reactor->reactions->get_reactions_to_event( $event_slug ) as $reaction ) {

				$validator = new WordPoints_Hook_Reaction_Validator( $reaction, true );

				$validator->validate();

				if ( $validator->had_errors() ) {
					continue;
				}

				$event_args->set_validator( $validator );
				$reaction = $validator;

				/** @var WordPoints_Hook_Extension $extension */
				foreach ( $hooks->extensions->get() as $extension ) {

					if ( ! $extension->should_hit( $reaction, $event_args ) ) {
						continue 2;
					}
				}

				$reactor->hit( $event_args, $reaction );

				do_action( 'wordpoints_hook_event_hit', $reaction, $event_args, $reactor );
			}
		}
	}
}

// EOF
