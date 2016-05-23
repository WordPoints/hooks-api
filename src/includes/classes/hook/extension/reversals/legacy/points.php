<?php

/**
 * Legacy points reversals hook extension class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Makes actions of one type behave as reversals of actions of another type.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Extension_Reversals_Legacy_Points
	extends WordPoints_Hook_Extension_Reversals {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'reversals_legacy_points';

	/**
	 * @since 1.0.0
	 */
	public function should_hit( WordPoints_Hook_Fire $fire ) {

		if ( ! parent::should_hit( $fire ) ) {

			$logs = $this->get_points_logs_to_be_reversed( $fire );

			return count( $logs ) > 0;
		}

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function after_miss( WordPoints_Hook_Fire $fire ) {

		parent::after_miss( $fire );

		if ( ! $this->get_settings_from_fire( $fire ) ) {
			return;
		}

		foreach ( $this->get_points_logs_to_be_reversed( $fire ) as $log ) {
			wordpoints_add_points_log_meta( $log->id, 'auto_reversed', 0 );
		}
	}

	/**
	 * Get a list of points logs to be reversed by a fire.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The fire object.
	 *
	 * @return array The points logs to be reversed.
	 */
	protected function get_points_logs_to_be_reversed( WordPoints_Hook_Fire $fire ) {

		if ( isset( $fire->data[ $this->slug ]['points_logs'] ) ) {
			return $fire->data[ $this->slug ]['points_logs'];
		}

		$entity = $fire->event_args->get_primary_arg();

		if ( ! $entity ) {
			$fire->data[ $this->slug ]['points_logs'] = array();
			return array();
		}

		$slug = $entity->get_slug();

		if ( ( $pos = strpos( $slug, '\\' ) ) ) {
			$slug = substr( $slug, 0, $pos );
		}

		$meta_queries = array(
			array(
				// This is needed for back-compat with the way the points hooks
				// reversed transactions, so we don't re-reverse them.
				'key'     => 'auto_reversed',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'   => $slug,
				'value' => $entity->get_the_id(),
			),
		);

		$log_type = $fire->reaction->get_meta( 'legacy_log_type' );

		if ( ! $log_type ) {
			$log_type = $fire->reaction->get_event_slug();
		}

		$query = new WordPoints_Points_Logs_Query(
			array(
				'log_type'   => $log_type,
				'meta_query' => $meta_queries,
			)
		);

		$logs = $query->get();

		if ( ! $logs ) {
			$logs = array();
		}

		$fire->data[ $this->slug ]['points_logs'] = $logs;

		return $logs;
	}
}

// EOF
