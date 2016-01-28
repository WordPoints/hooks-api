<?php

/**
 * Hook hit logger class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Logs hook hits.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Hit_Logger {

	/**
	 * The fire for which a hit might occur.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Fire
	 */
	protected $fire;

	/**
	 * @param WordPoints_Hook_Fire $fire The fire that might be logged as a hit.
	 */
	public function __construct( WordPoints_Hook_Fire $fire ) {

		$this->fire = $fire;
	}

	/**
	 * Logs a hit for this fire.
	 *
	 * @since 1.0.0
	 *
	 * @return int|false The hit ID, or false if logging the hit failed.
	 */
	public function log_hit() {

		global $wpdb;

		$signature = wordpoints_hooks_get_event_signature( $this->fire->event_args );

		$inserted = $wpdb->insert(
			$wpdb->wordpoints_hook_hits
			, array(
				'firer' => $this->fire->firer->get_slug(),
				'signature' => $signature,
				'event' => $this->fire->reaction->get_event_slug(),
				'reactor' => $this->fire->reaction->get_reactor_slug(),
				'reaction_type' => $this->fire->reaction->get_storage_group_slug(),
				'reaction_id' => $this->fire->reaction->ID,
				'date' => current_time( 'mysql' ),
			)
		);

		if ( ! $inserted ) {
			return false;
		}

		$hit_id = $wpdb->insert_id;

		$supersedes = $this->fire->get_superseded_hit();

		if ( $supersedes ) {
			$wpdb->update(
				$wpdb->wordpoints_hook_hits
				, array( 'superseded_by' => $hit_id )
				, array( 'id' => $supersedes->id )
				, array( '%d' )
				, array( '%d' )
			);
		}

		return $hit_id;
	}
}

// EOF
