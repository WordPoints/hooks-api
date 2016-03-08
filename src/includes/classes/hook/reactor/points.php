<?php

/**
 * Points hook reactor class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Hook reactor to award user points.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Reactor_Points extends WordPoints_Hook_Reactor {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'points';

	/**
	 * @since 1.0.0
	 */
	protected $arg_types = 'user';

	/**
	 * @since 1.0.0
	 */
	protected $action_types = array( 'fire', 'reverse' );

	/**
	 * @since 1.0.0
	 */
	protected $settings_fields = array(
		'description' => array(
			'type'     => 'text',
			'required' => true,
		),
		'log_text'    => array(
			'type'     => 'text',
			'required' => true,
		),
		'points'      => array(
			'default'  => 0,
			'type'     => 'number',
			'required' => true,
		),
		'points_type' => array(
			'default'  => '',
			'type'     => 'hidden',
			'required' => true,
		),
	);

	/**
	 * @since 1.0.0
	 */
	public function get_settings_fields() {

		$this->settings_fields['points']['label'] = _x( 'Points', 'form label', 'wordpoints' );
		$this->settings_fields['log_text']['label'] = _x( 'Log Text', 'form label', 'wordpoints' );
		$this->settings_fields['description']['label'] = _x( 'Description', 'form label', 'wordpoints' );

		return parent::get_settings_fields();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_ui_script_data() {

		$data = parent::get_ui_script_data();

		$data['target_label'] = __( 'Award To', 'wordpoints' );
		$data['periods_label'] = __( 'Award each user no more than once per:', 'wordpoints' );

		return $data;
	}

	/**
	 * @since 1.0.0
	 */
	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	) {

		if ( ! isset( $settings['points'] ) || false === wordpoints_int( $settings['points'] ) ) {
			$validator->add_error( __( 'Points must be an integer.', 'wordpoints' ), 'points' );
		}

		if ( ! isset( $settings['points_type'] ) || ! wordpoints_is_points_type( $settings['points_type'] ) ) {
			$validator->add_error( __( 'Invalid points type.', 'wordpoints' ), 'points_type' );
		}

		if ( ! isset( $settings['description'] ) ) {
			$validator->add_error( __( 'Description is required.', 'wordpoints' ), 'description' );
		}

		if ( ! isset( $settings['log_text'] ) ) {
			$validator->add_error( __( 'Log Text is required.', 'wordpoints' ), 'log_text' );
		}

		return parent::validate_settings( $settings, $validator, $event_args );
	}

	/**
	 * @since 1.0.0
	 */
	public function update_settings(
		WordPoints_Hook_ReactionI $reaction,
		array $settings
	) {

		parent::update_settings( $reaction, $settings );

		$reaction->update_meta( 'points', $settings['points'] );
		$reaction->update_meta( 'points_type', $settings['points_type'] );
		$reaction->update_meta( 'description', $settings['description'] );
		$reaction->update_meta( 'log_text', $settings['log_text'] );
	}

	/**
	 * @since 1.0.0
	 */
	public function hit( WordPoints_Hook_Fire $fire ) {

		$reaction = $fire->reaction;

		$target = $fire->event_args->get_from_hierarchy(
			$reaction->get_meta( 'target' )
		);

		if ( ! $target instanceof WordPoints_Entity ) {
			return;
		}

		$meta = array();

		foreach ( $fire->event_args->get_entities() as $entity ) {
			$meta[ $entity->get_slug() ] = $entity->get_the_id();
		}

		wordpoints_alter_points(
			$target->get_the_id()
			, $reaction->get_meta( 'points' )
			, $reaction->get_meta( 'points_type' )
			, $reaction->get_event_slug()
			, $meta
			, $reaction->get_meta( 'log_text' )
		);
	}

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

		global $wpdb;

		add_filter( 'wordpoints_points_log', '__return_false' );

		foreach ( $logs as $log ) {

			wordpoints_alter_points(
				$log->user_id
				, -$log->points
				, $log->points_type
				, "reverse-{$log->log_type}"
				, array( 'original_log_id' => $log->id )
			);

			wordpoints_points_log_delete_all_metadata( $log->id );

			// Now delete the log.
			$wpdb->delete(
				$wpdb->wordpoints_points_logs
				, array( 'id' => $log->id )
				, '%d'
			); // WPCS: cache OK, cleaned by wordpoints_alter_points().
		}

		remove_filter( 'wordpoints_points_log', '__return_false' );
	}
}

// EOF
