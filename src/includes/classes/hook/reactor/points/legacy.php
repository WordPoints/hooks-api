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

		if ( isset( $fire->data['reversals_legacy_points']['points_logs'] ) ) {

			$this->reverse_logs(
				$fire->data['reversals_legacy_points']['points_logs']
				, $fire
			);

		} else {
			parent::reverse_hit( $fire );
		}
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_hit_ids_to_be_reversed( WordPoints_Hook_Fire $fire ) {

		// We closely integrate with the legacy reversals extension to get the IDs.
		if ( ! isset( $fire->data['reversals_legacy_points']['hit_ids'] ) ) {
			return array();
		}

		return $fire->data['reversals_legacy_points']['hit_ids'];
	}
}

// EOF
