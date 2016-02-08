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

		/**
		 * Filter the list of periods displayed in the hooks UI.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $periods The period titles, indexed by length in seconds.
		 */
		$periods = apply_filters( 'wordpoints_hooks_ui_data_periods', $periods );

		return array(
			'periods' => $periods,
			'l10n' => array(
				'label' => __( 'Trigger reaction no more than once per:', 'wordpoints' ),
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
			$this->validate_period_args( $period['args'] );
		}

		if ( ! isset( $period['length'] ) ) {

			$this->validator->add_error(
				__( 'Period length setting is missing.', 'wordpoints' )
			);

		} elseif ( false === wordpoints_posint( $period['length'] ) ) {

			$this->validator->add_error(
				__( 'Period length must be a positive integer.', 'wordpoints' )
				, 'length'
			);

			return false;
		}

		return $period;
	}

	/**
	 * Validate the period args.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $args The args the period is related to.
	 */
	protected function validate_period_args( $args ) {

		if ( ! is_array( $args ) ) {

			$this->validator->add_error(
				__( 'Period does not match expected format.', 'wordpoints' )
				, 'args'
			);

			return;
		}

		$this->validator->push_field( 'args' );

		foreach ( $args as $index => $hierarchy ) {

			$this->validator->push_field( $index );

			if ( ! is_array( $hierarchy ) ) {

				$this->validator->add_error(
					__( 'Period does not match expected format.', 'wordpoints' )
				);

			} elseif ( ! $this->event_args->get_from_hierarchy( $hierarchy ) ) {

				$this->validator->add_error(
					__( 'Invalid period.', 'wordpoints' ) // TODO better error message
				);
			}

			$this->validator->pop_field();
		}

		$this->validator->pop_field();
	}

	/**
	 * @since 1.0.0
	 */
	public function should_hit( WordPoints_Hook_Fire $fire ) {

		$periods = $fire->reaction->get_meta( 'periods' );

		if ( empty( $periods ) ) {
			return true;
		}

		$this->event_args = $fire->event_args;

		foreach ( $periods as $period ) {
			if ( ! $this->has_period_ended( $period, $fire->reaction ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check whether a period has ended.
	 *
	 * @since 1.0.0
	 *
	 * @param array                     $settings The period's settings.
	 * @param WordPoints_Hook_ReactionI $reaction The reaction object.
	 *
	 * @return bool Whether the period has ended.
	 */
	protected function has_period_ended(
		array $settings,
		WordPoints_Hook_ReactionI $reaction
	) {

		$period = $this->get_period_by_reaction(
			$this->get_period_signature( $settings, $reaction )
			, $reaction
		);

		// If the period isn't found, we know that we can still fire.
		if ( ! $period ) {
			return true;
		}

		$now = current_time( 'timestamp' );
		$hit_time = strtotime( $period->date, $now );

		if ( ! empty( $settings['relative'] ) ) {
			return ( $hit_time < $now - $settings['length'] );
		} else {
			return (
				(int) ( $hit_time / $settings['length'] )
				< (int) ( $now / $settings['length'] )
			);
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
	protected function get_arg_values( array $period_args ) {

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

		ksort( $values );

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
	protected function get_period( $period_id ) {

		$period = wp_cache_get( $period_id, 'wordpoints_hook_periods' );

		if ( ! $period ) {

			global $wpdb;

			$period = $wpdb->get_row(
				$wpdb->prepare(
					"
						SELECT *, `period`.`id` AS `id`
						FROM `{$wpdb->wordpoints_hook_periods}` AS `period`
						INNER JOIN `{$wpdb->wordpoints_hook_hits}` AS `hit`
							ON `hit`.`id` = `period`.`hit_id`
						WHERE `period`.`id` = %d
					"
					, $period_id
				)
			);

			if ( ! $period ) {
				return false;
			}

			wp_cache_set( $period->id, $period, 'wordpoints_hook_periods' );
		}

		return $period;
	}

	/**
	 * Get a period from the database by reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string                    $signature The values of the args this period
	 *                                             relates to.
	 * @param WordPoints_Hook_ReactionI $reaction  The reaction object.
	 *
	 * @return object|false The period data, or false if not found.
	 */
	protected function get_period_by_reaction(
		$signature,
		WordPoints_Hook_ReactionI $reaction
	) {

		$reaction_guid = $reaction->get_guid();

		$cache_key = wp_json_encode( $reaction_guid ) . "-{$signature}";

		// Before we run the query, we try to lookup the ID in the cache.
		$period_id = wp_cache_get( $cache_key, 'wordpoints_hook_period_ids_by_reaction' );

		// If we found it, we can retrieve the period by ID instead.
		if ( $period_id ) {
			return $this->get_period( $period_id );
		}

		global $wpdb;

		// Otherwise, we have to run this query.
		$period = $wpdb->get_row(
			$wpdb->prepare(
				"
					SELECT *, `period`.`id` AS `id`
					FROM `{$wpdb->wordpoints_hook_periods}` AS `period`
					INNER JOIN `{$wpdb->wordpoints_hook_hits}` AS `hit`
						ON `hit`.`id` = period.`hit_id`
					WHERE `period`.`signature` = %s
						AND `hit`.`reactor` = %s
						AND `hit`.`reaction_store` = %s
						AND `hit`.`reaction_context_id` = %s
						AND `hit`.`reaction_id` = %d
					ORDER BY `hit`.`date`
					LIMIT 1
				"
				, $signature
				, $reaction_guid['reactor']
				, $reaction_guid['store']
				, wp_json_encode( $reaction_guid['context_id'] )
				, $reaction_guid['id']
			)
		);

		if ( ! $period ) {
			return false;
		}

		wp_cache_set( $cache_key, $period->id, 'wordpoints_hook_period_ids_by_reaction' );
		wp_cache_set( $period->id, $period, 'wordpoints_hook_periods' );

		return $period;
	}

	/**
	 * @since 1.0.0
	 */
	public function after_hit( WordPoints_Hook_Fire $fire ) {

		$periods = $fire->reaction->get_meta( 'periods' );

		if ( empty( $periods ) ) {
			return;
		}

		$this->event_args = $fire->event_args;

		foreach ( $periods as $settings ) {

			$this->add_period(
				$this->get_period_signature( $settings, $fire->reaction )
				, $fire
			);
		}
	}

	/**
	 * Get the signature for a period.
	 *
	 * The period signature is a hash value calculated based on the values of the
	 * event args to which that period is related. This is calculated as a hash so
	 * that it can be easily stored and queried at a fixed length.
	 *
	 * @since 1.0.0
	 *
	 * @param array                     $settings The period settings.
	 * @param WordPoints_Hook_ReactionI $reaction The reaction.
	 *
	 * @return string The period signature.
	 */
	protected function get_period_signature(
		array $settings,
		WordPoints_Hook_ReactionI $reaction
	) {

		if ( isset( $settings['args'] ) ) {
			$period_args = $settings['args'];
		} else {
			$period_args = array( $reaction->get_meta( 'target' ) );
		}

		return wordpoints_hash(
			wp_json_encode( $this->get_arg_values( $period_args ) )
		);
	}

	/**
	 * Add a period to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $signature The period signature.
	 * @param WordPoints_Hook_Fire $fire      The fire object.
	 *
	 * @return false|object The period data, or false if not found.
	 */
	protected function add_period( $signature, WordPoints_Hook_Fire $fire ) {

		global $wpdb;

		$inserted = $wpdb->insert(
			$wpdb->wordpoints_hook_periods
			, array(
				'hit_id' => $fire->hit_id,
				'signature' => $signature,
			)
			, array( '%d', '%s' )
		);

		if ( ! $inserted ) {
			return false;
		}

		$period_id = $wpdb->insert_id;

		wp_cache_set(
			wp_json_encode( $fire->reaction->get_guid() ) . "-{$signature}"
			, $period_id
			, 'wordpoints_hook_period_ids_by_reaction'
		);

		return $period_id;
	}
}

// EOF
