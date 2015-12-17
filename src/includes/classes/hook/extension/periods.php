<?php

/**
 * Periods hook extension.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Limits the number of times that targets can be hit in a given time period.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Extension_Periods extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'periods';

	/**
	 * @since 1.0.0
	 */
	public function get_ui_script_data() {

		$periods = array(
			MINUTE_IN_SECONDS   => __( 'Minute', 'wordpoints' ),
			HOUR_IN_SECONDS     => __( 'Hour',   'wordpoints' ),
			DAY_IN_SECONDS      => __( 'Day',    'wordpoints' ),
			WEEK_IN_SECONDS     => __( 'Week',   'wordpoints' ),
			30 * DAY_IN_SECONDS => __( 'Month',  'wordpoints' ),
		);

		return array(
			'periods' => $periods,
			'l10n' => array(
				// TODO this should be supplied per-reactor
				'label' => __( 'Award each user no more than once per:', 'wordpoints' ),
			),
		);
	}

	/**
	 * Validate the periods.
	 *
	 * @since 1.0.0
	 *
	 * @param array $periods The periods.
	 *
	 * @return array The validated periods.
	 */
	protected function validate_periods( $periods ) {

		if ( ! is_array( $periods ) ) {

			$this->validator->add_error(
				__( 'Periods do not match expected format.', 'wordpoints' )
			);

			return array();
		}

		foreach ( $periods as $index => $period ) {

			$this->validator->push_field( $index );

			$period = $this->validate_period( $period );

			if ( $period ) {
				$periods[ $index ] = $period;
			} else {
				unset( $periods[ $index ] );
			}

			$this->validator->pop_field();
		}

		return $periods;
	}

	/**
	 * Validate the settings for a period.
	 *
	 * @since 1.0.0
	 *
	 * @param array $period The period.
	 *
	 * @return array|false The validated period, or false if invalid.
	 */
	protected function validate_period( $period ) {

		if ( ! is_array( $period ) ) {
			$this->validator->add_error(
				__( 'Period does not match expected format.', 'wordpoints' )
			);

			return false;
		}

		if ( isset( $period['args'] ) ) {

			if ( ! is_array( $period['args'] ) ) {
				return false;
			}

			if ( ! $this->event_args->get_from_hierarchy( $period['args'] ) ) {
				$this->validator->add_error(
					__( 'Invalid period.', 'wordpoints' )
					// TODO better error message
					,
					'args'
				);

				return false;
			}
		}

		if ( ! isset( $period['settings'] ) ) {
			$this->validator->add_error(
				__( 'Period settings are missing.', 'wordpoints' )
			);

			return false;
		}

		if ( ! isset( $period['settings']['length'] ) ) {
			$this->validator->add_error(
				__( 'Period length setting is missing.', 'wordpoints' )
				, 'settings'
			);

			return false;
		}

		$this->validator->push_field( 'settings' );

		if ( false === wordpoints_posint( $period['settings']['length'] ) ) {
			$this->validator->add_error(
				__( 'Period length must be a positive integer.', 'wordpoints' )
				, 'length'
			);

			$period = false;
		}

		$this->validator->pop_field();

		return $period;
	}

	/**
	 * @since 1.0.0
	 */
	public function should_hit( WordPoints_Hook_Reaction_Validator $reaction, WordPoints_Hook_Event_Args $event_args ) {

		$periods = $reaction->get_meta( 'periods' );

		if ( empty( $periods ) ) {
			return true;
		}

		$reaction_id = $reaction->get_id();

		$this->event_args = $event_args;

		foreach ( $periods as $period ) {
			if ( ! $this->has_period_ended( $period, $reaction ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check whether a period has ended.
	 *
	 * TODO should these be final?
	 *
	 * @since 1.0.0
	 *
	 * @param array                              $settings The period's settings.
	 * @param WordPoints_Hook_Reaction_Validator $reaction The reaction object.
	 *
	 * @return bool Whether the period has ended.
	 */
	final private function has_period_ended(
		array $settings,
		WordPoints_Hook_Reaction_Validator $reaction
	) {

		if ( isset( $settings['args'] ) ) {
			$period_args = $settings['args'];
		} else {
			$period_args = array( $reaction->get_meta( 'target' ) );
		}

		$period = $this->get_period_by_reaction(
			$this->get_arg_values( $period_args )
			, $reaction
		);

		// If the period isn't found, we know that we can still fire.
		if ( ! $period ) {
			return true;
		}

		if ( ! empty( $settings['settings']['absolute'] ) ) {
			return ( $period->expiration < current_time( 'timestamp' ) );
		} else {
			return false;
		}
	}

	/**
	 * Get the values of the args that a period relates to.
	 *
	 * @since 1.0.0
	 *
	 * @param array $period_args The args this period relates to.
	 *
	 * @return array The arg values.
	 */
	final private function get_arg_values( array $period_args ) {

		$values = array();

		foreach ( $period_args as $arg_hierarchy ) {

			$arg = $this->event_args->get_from_hierarchy(
				$arg_hierarchy
			);

			if ( ! $arg instanceof WordPoints_EntityishI ) {
				continue;
			}

			$values[ implode( '.', $arg_hierarchy ) ] = $arg->get_the_value();
		}

		return $values;
	}

	/**
	 * Get a a period from the database by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param int $period_id The ID of a period.
	 *
	 * @return object|false The period data, or false if not found.
	 */
	final private function get_period( $period_id ) {

		$period = wp_cache_get( $period_id, 'wordpoints_hook_period' );

		if ( ! $period ) {

			global $wpdb;

			$period = $wpdb->get_row(
				$wpdb->prepare(
					"
					SELECT `id`, `hook_id`, `arg_hash`, `expiration`, `meta`
					FROM {$wpdb->wordpoints_hook_periods}
					WHERE `id` = %d
					"
					, $period_id
				)
			);

			if ( ! $period ) {
				return false;
			}

			wp_cache_set( $period->hook_id . $period->arg_hash, $period->id, 'wordpoints_hook_period_ids' );
			wp_cache_set( $period->id, $period, 'wordpoints_hook_periods' );
		}

		return $period;
	}

	/**
	 * Get a period from the database by args reaction ID.
	 *
	 * @since 1.0.0
	 *
	 * @param array                              $args     The values of the args
	 *                                                     this period relates to.
	 * @param WordPoints_Hook_Reaction_Validator $reaction The reaction object.
	 *
	 * @return object|false The period data, or false if not found.
	 */
	final private function get_period_by_reaction(
		array $args,
		WordPoints_Hook_Reaction_Validator $reaction
	) {

		// The periods for a reaction are differentiated by a hash of specific args.
		$arg_hash = md5( serialize( $args ) );

		$reaction_id = $reaction->get_id();
		$event_slug = $reaction->get_event_slug();

		$cache_key = "{$event_slug}-{$reaction_id}-{$arg_hash}";

		// Before we run the query, we try to lookup the ID in the cache.
		$period_id = wp_cache_get( $cache_key, 'wordpoints_hook_period_ids' );

		// If we found it, we can retrieve the period by ID instead.
		if ( $period_id ) {
			return $this->get_period( $period_id );
		}

		global $wpdb;

		// Otherwise, we have to run this query.
		$period = $wpdb->get_row(
			$wpdb->prepare(
				"
					SELECT `id`, `hook_id`, `arg_hash`, `expiration`, `meta`
					FROM {$wpdb->wordpoints_hook_periods}
					WHERE `hook_id` = %s
						AND `arg_hash` = %s
				"
				, "{$event_slug}-{$reaction_id}"
				, $arg_hash
			)
		);

		if ( ! $period ) {
			return false;
		}

		wp_cache_set( $cache_key, $period->id, 'wordpoints_hook_period_ids' );
		wp_cache_set( $period->id, $period, 'wordpoints_hook_periods' );

		return $period;
	}
}

// EOF
