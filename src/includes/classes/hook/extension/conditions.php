<?php

/**
 * Conditions hook extension class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Requires the event args to meet certain conditions for the target to be hit.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Extension_Conditions extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'conditions';

	/**
	 * The conditions registry.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Class_Registry_Children
	 */
	protected $conditions;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->conditions = wordpoints_hooks()->conditions;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_ui_script_data() {

		$conditions_data = array();

		foreach ( $this->conditions->get_all() as $data_type => $conditions ) {
			foreach ( $conditions as $slug => $condition ) {

				if ( ! ( $condition instanceof WordPoints_Hook_Condition ) ) {
					continue;
				}

				$conditions_data[ $data_type ][ $slug ] = array(
					'slug'      => $slug,
					'data_type' => $data_type,
					'title'     => $condition->get_title(),
					'fields'    => $condition->get_settings_fields(),
				);
			}
		}

		return array( 'conditions' => $conditions_data );
	}

	/**
	 * Validate the conditions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The args and their conditions.
	 *
	 * @return array The validated settings.
	 */
	protected function validate_conditions( $args ) {

		if ( ! is_array( $args ) ) {

			$this->validator->add_error(
				__( 'Conditions do not match expected format.', 'wordpoints' )
			);

			return array();
		}

		foreach ( $args as $arg_slug => $sub_args ) {

			if ( '_conditions' === $arg_slug ) {

				$this->validator->push_field( $arg_slug );

				foreach ( $sub_args as $index => $settings ) {

					$this->validator->push_field( $index );

					$condition = $this->validate_condition( $settings );

					if ( $condition ) {
						$sub_args[ $index ] = $condition;
					}

					$this->validator->pop_field();
				}

				$this->validator->pop_field();

			} else {

				if ( ! $this->event_args->descend( $arg_slug ) ) {
					continue;
				}

				$sub_args = $this->validate_conditions( $sub_args );

				$args[ $arg_slug ] = $sub_args;

				$this->event_args->ascend();
			}

			$args[ $arg_slug ] = $sub_args;
		}

		return $args;
	}

	/**
	 * Validate a condition's settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The condition settings.
	 *
	 * @return array|false The validated conditions settings, or false if unable to
	 *                     validate.
	 */
	protected function validate_condition( $settings ) {

		if ( ! isset( $settings['type'] ) ) {
			$this->validator->add_error( __( 'Condition type is missing.', 'wordpoints' ) );
			return false;
		}

		$arg = $this->event_args->get_current();

		$data_type = $this->get_data_type( $arg );

		if ( ! $data_type ) {
			$this->validator->add_error(
				__( 'This type of condition does not work for the selected attribute.', 'wordpoints' )
			);

			return false;
		}

		$condition = wordpoints_hooks()->conditions->get( $data_type, $settings['type'] );

		if ( ! $condition ) {

			$this->validator->add_error(
				sprintf(
					__( 'Unknown condition type &#8220;%s&#8221;.', 'wordpoints' )
					, $settings['type']
				)
				, 'type'
			);

			return false;
		}

		if ( ! isset( $settings['settings'] ) ) {
			$this->validator->add_error( __( 'Condition settings are missing.', 'wordpoints' ) );
			return false;
		}

		$this->validator->push_field( 'settings' );

		// The condition may call this object's validate_settings() method to
		// validate some sub-conditions. When that happens, these properties will be
		// reset, so we need to back up their values and then restore them below.
		$backup = array( $this->validator, $this->event_args );

		$settings['settings'] = $condition->validate_settings(
			$arg
			, $settings['settings']
			, $this->validator
		);

		list( $this->validator, $this->event_args ) = $backup;

		$this->validator->pop_field();

		return $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function should_hit(
		WordPoints_Hook_ReactionI $reaction,
		WordPoints_Hook_Event_Args $event_args
	) {

		$conditions = $reaction->get_meta( 'conditions' );

		if ( $conditions && ! $this->conditions_are_met( $conditions, $event_args ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the event args meet the conditions.
	 *
	 * @since 1.0.0
	 *
	 * @param array                      $conditions The conditions.
	 * @param WordPoints_Hook_Event_Args $event_args The event args.
	 *
	 * @return bool Whether the conditions are met.
	 */
	public function conditions_are_met(
		$conditions,
		WordPoints_Hook_Event_Args $event_args
	) {

		foreach ( $conditions as $arg_slug => $sub_args ) {

			$event_args->descend( $arg_slug );

			if ( isset( $sub_args['_conditions'] ) ) {

				foreach ( $sub_args['_conditions'] as $settings ) {

					$condition = $this->conditions->get(
						$this->get_data_type( $event_args->get_current() )
						, $settings['type']
					);

					$is_met = $condition->is_met( $settings['settings'], $event_args );

					if ( ! $is_met ) {
						$event_args->ascend();
						return false;
					}
				}

				unset( $sub_args['_conditions'] );
			}

			$are_met = $this->conditions_are_met( $sub_args, $event_args );

			$event_args->ascend();

			if ( ! $are_met ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the data type of an entity.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_EntityishI $arg An entity object.
	 *
	 * @return string|false The data type, or false.
	 */
	protected function get_data_type( $arg ) {

		if ( $arg instanceof WordPoints_Entity_Attr ) {
			$data_type = $arg->get_data_type();
		} elseif ( $arg instanceof WordPoints_Entity_Array ) {
			$data_type = 'entity_array';
		} elseif ( $arg instanceof WordPoints_Entity ) {
			$data_type = 'entity';
		} else {
			$data_type = false;
		}

		return $data_type;
	}
}

// EOF
