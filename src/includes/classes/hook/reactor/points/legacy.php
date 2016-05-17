<?php

/**
 * Legacy points hook reactor class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Hook reactor to award user points on legacy sites.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Reactor_Points_Legacy extends WordPoints_Hook_Reactor_Points {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'points_legacy';

	/**
	 * @since 1.0.0
	 */
	public function reverse_hit( WordPoints_Hook_Fire $fire ) {

		$meta_queries = array(
			array(
				// This is needed for back-compat with the way the points hooks
				// reversed transactions, so we don't re-reverse them.
				'key'     => 'auto_reversed',
				'compare' => 'NOT EXISTS',
			),
		);

		foreach ( $fire->event_args->get_entities() as $slug => $entity ) {

			$meta_queries[] = array(
				'key'   => $slug,
				'value' => $entity->get_the_id(),
			);
		}

		$query = new WordPoints_Points_Logs_Query(
			array(
				'log_type'   => $fire->reaction->get_event_slug(),
				'meta_query' => $meta_queries,
			)
		);

		$logs = $query->get();

		if ( ! $logs ) {
			return;
		}

		$this->reverse_logs( $logs, $fire );
	}
}

// EOF
