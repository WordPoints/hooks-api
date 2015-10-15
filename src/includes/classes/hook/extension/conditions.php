<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Hook_Extension_Conditions extends WordPoints_Hook_Extension {

	/**
	 * @since 1.0.0
	 */
	protected $slug = 'conditions';

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Class_Registry
	 */
	protected $conditions;

	public function __construct() {
		$this->conditions = wordpoints_apps()->hooks->conditions;
	}

	public function get_data() {

		$conditions = array();

//		/** @var WordPoints_Hook_ConditionI $condition */
//		foreach ( $this->conditions->get() as $slug => $condition ) {
//
//			$conditions[ $slug ] = array(
//				'slug'   => $slug,
//				'title'  => $condition->get_title(),
//				'types'  => $condition->get_supported_types(),
//				'fields' => $condition->get_settings_fields(),
//			);
//		}

		return array( 'conditions' => $conditions );
	}
//
//	protected function validate_conditions( $args ) {
//
//		if ( ! is_array( $args ) ) {
//
//			$this->validator->add_error(
//				__( 'Conditions do not match expected format.', 'wordpoints' )
//			);
//
//			return array();
//		}
//
//		foreach ( $args as $arg_slug => $sub_args ) {
//
//			if ( ! $this->event_args->descend( $arg_slug ) ) {
//				unset( $args[ $arg_slug ] );
//				continue;
//			}
//
//			$sub_args = $this->validate_sub_conditions( $sub_args );
//
//			if ( ! $sub_args ) {
//				unset( $args[ $arg_slug ] );
//			} else {
//				$args[ $arg_slug ] = $sub_args;
//			}
//
//			$this->event_args->ascend();
//		}
//
//		return $args;
//	}

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

					if ( ! $condition ) {
						unset( $sub_args['_conditions'][ $index ] );
					} else {
						$sub_args['_conditions'][ $index ] = $condition;
					}

					$this->validator->pop_field();
				}

				$this->validator->pop_field();

			} else {

				if ( ! $this->event_args->descend( $arg_slug ) ) {
					unset( $args[ $arg_slug ] );
					continue;
				}

				$sub_args = $this->validate_conditions( $sub_args );

				$args[ $arg_slug ] = $sub_args;

				$this->event_args->ascend();
			}
		}

		return $args;
	}

//	protected function validate_condition( $settings ) {
//
//		if ( ! isset( $settings['type'] ) ) {
//			$this->validator->add_error( __( 'Condition type is missing.', 'wordpoints' ) );
//			return false;
//		}
//
//		if ( ! isset( $settings['settings'] ) ) {
//			$this->validator->add_error( __( 'Condition settings are missing.', 'wordpoints' ) );
//			return false;
//		}
//
//		$condition = $this->conditions->get( $settings['type'] );
//
//		if ( ! $condition ) {
//
//			$this->validator->add_error(
//				sprintf(
//					__( 'Unknown condition type &#8220;%s&#8221;.', 'wordpoints' )
//					, $settings['type']
//				)
//			);
//
//			return false;
//		}
//
//		$this->validator->push_field( 'settings' );
//
//		$the_settings = $condition->validate_settings(
//			$this->event_args->get_current()
//			, $settings['settings']
//			, $this->validator
//		);
//
//		$this->validator->pop_field();
//
//		if ( ! $the_settings ) {
//			return false;
//		}
//
//		$settings['settings'] = $the_settings;
//
//		return $settings;
//	}

	protected function validate_condition( $settings ) {

		if ( ! isset( $settings['type'] ) ) {
			$this->validator->add_error( __( 'Condition type is missing.', 'wordpoints' ) );
			return false;
		}

		if ( ! isset( $settings['settings'] ) ) {
			$this->validator->add_error( __( 'Condition settings are missing.', 'wordpoints' ) );
			return false;
		}

		$arg = $this->event_args->get_current();

		if ( $arg instanceof WordPoints_Entity_Attr ) {
			$data_type = $arg->get_data_type();
		} elseif ( $arg instanceof WordPoints_Entity_Array ) {
			$data_type = 'entity_array';
		}

		if ( ! isset( $data_type ) ) {
			$this->validator->add_error(
				__( 'This type of condition does not work for the selected attribute.', 'wordpoints' )
			);

			return false;
		}

		$condition = wordpoints_apps()->hooks->conditions->get( $data_type, $settings['type'] );

		if ( ! $condition ) {

			$this->validator->add_error(
				sprintf(
					__( 'Unknown condition type &#8220;%s&#8221;.', 'wordpoints' )
					, $settings['type']
				)
			);

			return false;
		}

		$this->validator->push_field( 'settings' );

		$the_settings = $condition->validate_settings(
			$arg
			, $settings['settings']
			, $this->validator
		);

		$this->validator->pop_field();

		if ( ! $the_settings ) {
			return false;
		}

		$settings['settings'] = $the_settings;

		return $settings;
	}

	public function should_hit( WordPoints_Hook_Reaction_Validator $reaction, WordPoints_Hook_Event_Args $event_args ) {

		$conditions = $reaction->get_meta( 'conditions' );

		if ( $conditions && ! $this->conditions_are_met( $conditions, $event_args ) ) {
			return false;
		}

		return true;
	}

	public function conditions_are_met(
		$conditions,
		WordPoints_Hook_Event_Args $event_args
	) {

		foreach ( $conditions as $arg_slug => $sub_args ) {

			$event_args->descend( $arg_slug );

			if ( isset( $sub_args['_conditions'] ) ) {

				foreach ( $sub_args['_conditions'] as $settings ) {

					if ( ! $this->is_met( $settings, $event_args ) ) {
						return false;
					}
				}

				unset( $sub_args['_conditions'] );
			}

			if ( ! $this->conditions_are_met( $sub_args, $event_args ) ) {
				return false;
			}

			$event_args->ascend();
		}

		return true;
	}

	final private function is_met( $settings, WordPoints_Hook_Event_Args $event_args ) {

		$condition = $this->conditions->get( $settings['type'] );

		$is_met = $condition->is_met( $settings['settings'], $event_args );

		// TODO filter.

		return $is_met;
	}
}

// EOF
