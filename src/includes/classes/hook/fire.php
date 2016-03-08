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
	 * The type of action being fired.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $action_type;

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
	 * @param string                     $action_type The type of action.
	 * @param WordPoints_Hook_Event_Args $event_args  The event args.
	 * @param WordPoints_Hook_ReactionI  $reaction    The reaction.
	 */
	public function __construct(
		$action_type,
		WordPoints_Hook_Event_Args $event_args,
		WordPoints_Hook_ReactionI $reaction
	) {

		$this->action_type = $action_type;
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
