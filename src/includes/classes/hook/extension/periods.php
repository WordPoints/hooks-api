<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

array(
	'args' => array(),
	'settings' => array(
		'length' => '',
		'absolute' => true,
	)
);

class WordPoints_Hook_Extension_Periods extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'periods';

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

	protected function validate_period( $period ) {

		if ( ! is_array( $period ) ) {
			$this->validator->add_error(
				__( 'Period does not match expected format.', 'wordpoints' )
			);

			return false;
		}

		if ( ! isset( $period['args'] ) ) {
			// TODO is there a default period args?
		}

		if ( ! $this->validator->validate_arg_hierarchy( $period['args'] ) ) {
			$this->validator->add_error(
				__( 'Invalid period.', 'wordpoints' ) // TODO better error message
				, 'args'
			);

			return false;
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

	public function should_hit( WordPoints_Hook_Reaction_Validator $reaction, WordPoints_Hook_Event_Args $event_args ) {

		$periods = $reaction->get_meta( 'periods' );

		if ( empty( $periods ) ) {
			return true;
		}

		$reaction_id = $reaction->get_ID();

		$this->event_args = $event_args;

		foreach ( $periods as $period ) {
			if ( ! $this->check_period( $period, $reaction_id ) ) {
				return false;
			}
		}

		return true;
	}

	final private function check_period( $settings, $reaction_id ) {

		$period = $this->get_period_by_reaction(
			$this->get_arg_values( $settings['args'] )
			, $reaction_id
		);

		// If the period isn't found, we know that we can still fire.
		if ( ! $settings ) {
			return true;
		}

		if ( ! empty( $settings['settings']['absolute'] ) ) {
			return ( $period->expiration < current_time( 'timestamp' ) );
		} else {
			return false;
		}
	}

	final private function get_arg_values( $period_args ) {

		$values = array();

		foreach ( $period_args as $arg_slug => $sub_args ) {

			$this->event_args->descend( $arg_slug );

			if ( is_array( $sub_args ) ) {
				$values = array_merge( $values, $this->get_arg_values( $sub_args ) );
			} else {
				$values[ $arg_slug ] = $this->event_args->get_current()->get_the_value();
			}

			$this->event_args->ascend();
		}

		return $values;
	}

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

	final private function get_period_by_reaction( $args, $reaction_id ) {

		// The periods for a reaction are differentiated by a hash of specific args.
		$arg_hash = md5( serialize( $args ) );

		$cache_key = "{$this->slug}-{$reaction_id}-{$arg_hash}";

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
				, "{$this->slug}-{$reaction_id}"
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
