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
	 * @param WordPoints_Hook_Firer      $firer      The firer.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 * @param WordPoints_Hook_ReactionI  $reaction   The reaction.
	 */
	public function __construct(
		WordPoints_Hook_Firer $firer,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_ReactionI $reaction
	) {

		$this->firer      = $firer;
		$this->event_args = $event_args;
		$this->reaction   = $reaction;
		$this->hit_logger = new WordPoints_Hook_Hit_Logger( $this );
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
}

// EOF
