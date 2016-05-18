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
	public function update_settings(
		WordPoints_Hook_ReactionI $reaction,
		array $settings
	) {

		if ( isset( $settings['legacy_log_type'] ) ) {
			$reaction->update_meta(
				'legacy_log_type',
				$settings['legacy_log_type']
			);
		}

		parent::update_settings( $reaction, $settings );
	}

	/**
	 * @since 1.0.0
	 */
	public function reverse_hit( WordPoints_Hook_Fire $fire ) {

		$entity = $fire->event_args->get_primary_arg();

		if ( ! $entity ) {
			return;
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
			)
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
			return;
		}

		$this->reverse_logs( $logs, $fire );
	}
}

// EOF
