<?php

/**
 * Hook fire class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Holds the data for a hook fire.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Fire {

	/**
	 * The firer that is firing the hook.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_FirerI
	 */
	public $firer;

	/**
	 * The args for the event that is being fired.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Event_Args
	 */
	public $event_args;

	/**
	 * The reaction that is being fired at.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_ReactionI
	 */
	public $reaction;

	/**
	 * The hit logger for this fire.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Hit_Logger
	 */
	public $hit_logger;

	/**
	 * The ID of the hit (if this fire has hit).
	 *
	 * @since 1.0.0
	 *
	 * @var int|false
	 */
	public $hit_id = false;

	/**
	 * The hit this fire supersedes, if any.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected $supersedes;

	/**
	 * @param WordPoints_Hook_Firer      $firer      The firer.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 * @param WordPoints_Hook_ReactionI  $reaction   The reaction.
	 * @param object                     $supersedes The hit superseded by this fire.
	 */
	public function __construct(
		WordPoints_Hook_Firer $firer,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_ReactionI $reaction,
		$supersedes = null
	) {

		$this->firer      = $firer;
		$this->event_args = $event_args;
		$this->reaction   = $reaction;
		$this->hit_logger = new WordPoints_Hook_Hit_Logger( $this );
		$this->supersedes = $supersedes;
	}

	/**
	 * Make this fire a hit.
	 *
	 * @since 1.0.0
	 *
	 * @return int|false The ID of the hit, or false if it failed to be logged.
	 */
	public function hit() {

		if ( ! $this->hit_id ) {

			$this->hit_id = $this->hit_logger->log_hit();

			if ( ! $this->hit_id ) {
				return false;
			}
		}

		return $this->hit_id;
	}

	/**
	 * Get the hit being superseded by this fire.
	 *
	 * The fire will only supersede the hit if it ends up being a hit as well.
	 *
	 * @since 1.0.0
	 *
	 * @return object|false The hit being superseded by this fire, or false.
	 */
	public function get_superseded_hit() {

		if ( isset( $this->supersedes ) ) {
			return $this->supersedes;
		}

		global $wpdb;

		$hit = $wpdb->get_row(
			$wpdb->prepare(
				"
					SELECT *
					FROM `{$wpdb->wordpoints_hook_hits}`
					WHERE `firer` != %s
					AND `primary_arg_guid` = %s
					AND `event` = %s
					AND `reactor` = %s
					AND `reaction_type` = %s
					AND `reaction_context_id` = %s
					AND `reaction_id` = %d
					AND `superseded_by` IS NULL
				"
				, $this->firer->get_slug()
				, wordpoints_hooks_get_event_primary_arg_guid_json( $this->event_args )
				, $this->reaction->get_event_slug()
				, $this->reaction->get_reactor_slug()
				, $this->reaction->get_storage_group_slug()
				, wp_json_encode( $this->reaction->get_context_id() )
				, $this->reaction->ID
			)
		);

		$this->supersedes = ( $hit ) ? $hit : false;

		return $this->supersedes;
	}
}

// EOF
