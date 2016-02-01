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

		foreach ( $this->get_hits( $event_slug, $event_args ) as $hit ) {

			$reactor = $hooks->reactors->get( $hit->reactor );

			if ( ! $reactor instanceof WordPoints_Hook_Reactor ) {
				continue;
			}

			$reactions = $reactor->get_reaction_group( $hit->reaction_type );

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

			$fire = new WordPoints_Hook_Fire( $this, $event_args, $reaction, $hit );

			$fire->hit();

			$reactor->reverse_hit( $fire );

			/** @var WordPoints_Hook_Extension $extension */
			foreach ( $hooks->extensions->get_all() as $extension ) {
				$extension->after_reverse( $fire );
			}
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

		global $wpdb;

		$hits = $wpdb->get_results(
			$wpdb->prepare(
				"
					SELECT *
					FROM `{$wpdb->wordpoints_hook_hits}`
					WHERE `firer` = 'fire'
					AND `primary_arg_guid` = %s
					AND `event` = %s
					AND `superseded_by` IS NULL
				"
				, wordpoints_hooks_get_event_primary_arg_guid_json( $event_args )
				, $event_slug
			)
		);

		if ( ! is_array( $hits ) ) {
			return array();
		}

		return $hits;
	}
}

// EOF
