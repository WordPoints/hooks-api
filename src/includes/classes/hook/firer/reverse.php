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
class WordPoints_Hook_Firer_Reverse extends WordPoints_Hook_Firer {

	/**
	 * @since 1.0.0
	 */
	public function do_event( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$hooks = wordpoints_hooks();

		$hits = $this->get_hits( $event_slug, $event_args );
		$reverse_hit_ids = array();

		foreach ( $hits as $hit ) {

			/** @var WordPoints_Hook_Reactor $reactor */
			$reactor = $hooks->reactors->get( $hit->reactor );

			if ( ! $reactor instanceof WordPoints_Hook_Reactor_ReverseI ) {
				continue;
			}

			$reactions = $reactor->get_reaction_store( $hit->reaction_store );

			if (
				! $reactions
				|| wp_json_encode( $reactions->get_context_id() ) !== $hit->reaction_context_id
			) {
				continue;
			}

			$reaction = $reactions->get_reaction( $hit->reaction_id );

			if ( ! $reaction ) {
				continue;
			}

			$fire = new WordPoints_Hook_Fire( $this, $event_args, $reaction );

			$reverse_hit_ids[ $hit->id ] = $fire->hit();

			$reactor->reverse_hit( $fire );

			/** @var WordPoints_Hook_Extension $extension */
			foreach ( $hooks->extensions->get_all() as $extension ) {
				$extension->after_reverse( $fire );
			}
		}

		// Set the reversed_by meta key for all hits so that we know that they have
		// been reverse fired, even if they didn't hit.
		foreach ( $hits as $hit ) {

			if ( isset( $reverse_hit_ids[ $hit->id ] ) ) {
				$reversed_by = $reverse_hit_ids[ $hit->id ];
			} else {
				$reversed_by = 0;
			}

			add_metadata(
				'wordpoints_hook_hit'
				, $hit->id
				, 'reversed_by'
				, $reversed_by
				, true
			);
		}
	}

	/**
	 * Retrieves a list of all hits matching this event that have not been reversed.
	 *
	 * @since 1.0.0
	 *
	 * @param string                     $event_slug The slug of the event.
	 * @param WordPoints_Hook_Event_Args $event_args The args for the event.
	 *
	 * @return object[] The data for each hit from the hit logs database table.
	 */
	protected function get_hits( $event_slug, WordPoints_Hook_Event_Args $event_args ) {

		$query = new WordPoints_Hook_Hit_Query(
			array(
				'firer' => 'fire',
				'primary_arg_guid' => wordpoints_hooks_get_event_primary_arg_guid_json(
					$event_args
				),
				'event' => $event_slug,
				'meta_key' => 'reversed_by',
				'meta_compare' => 'NOT EXISTS',
			)
		);

		$hits = $query->get();

		if ( ! is_array( $hits ) ) {
			return array();
		}

		return $hits;
	}
}

// EOF
