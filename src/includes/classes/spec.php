<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */


interface WordPoints_SpecI {
	public function get_title();
	public function get_settings_fields();
	public function validate_settings( $settings );
	public function is_met();
	public function get_error( $name );
}

abstract class WordPoints_Spec implements  WordPoints_SpecI {

	protected $value;
	protected $expected;

	/**
	 * @param $value
	 * @param $expected
	 */
	public function __construct( $value, $expected ) {
		$this->value = $value;
		$this->expected = $expected;
	}

	public function validate_settings( $settings ) {

		$data_types = wordpoints_apps()->data_types;

		foreach ( $this->get_settings_fields() as $setting => $data ) {

			if ( ! isset( $settings[ $setting ] ) ) {

				if ( ! empty( $data['required'] ) ) {
					return 'error'; // TODO
				}

			} else {

				$data_type = $data_types->get( $data['type'] );

				if ( ! $data_type instanceof WordPoints_Data_TypeI ) {
					continue;
				}

				$value = $data_type->validate_value( $settings[ $setting ] );

				if ( is_wp_error( $value ) ) {
					return $value;
				}

				$settings[ $setting ] = $value;
			}
		}

		return $settings;
	}
}

class WordPoints_Spec_Number_Max extends WordPoints_Spec {

	public function get_title() {
		return __( 'Maximum', 'wordpoints' );
	}

	public function get_settings_fields() {
		// TODO the data for the expected value can be deduced.
//		return array(
//			'value' => array(
//				'type'     => 'text',
//				'label'    => __( 'Contains', 'wordpoints' ),
//				'required' => true,
//			),
//		);
	}

	public function is_met() {
		return $this->value <= $this->expected;
	}

	public function get_error( $name ) {

		return sprintf(
			__( '%1$s must not be more than %2$s.', 'wordpoints' )
			, $name
			, $this->expected
		);
	}
}

class WordPoints_Spec_String_Length_Max extends WordPoints_Spec {

	public function get_title() {
		return __( 'Maximum Length', 'wordpoints' );
	}

	public function get_settings_fields() {
//		return array(
//			'value' => array(
//				'type'     => 'text',
//				'label'    => __( 'Maximum', 'wordpoints' ),
//				'required' => true,
//			),
//		);
	}

	public function is_met() {
		return strlen( $this->value ) <= $this->expected;
	}

	public function get_error( $name ) {

		return sprintf(
			__( '%1$s must not be longer than %2$s characters.', 'wordpoints' )
			, $name
			, $this->expected
		);
	}
}


class WordPoints_Spec_String_Contains extends WordPoints_Spec {

	public function get_title() {
		return __( 'Contains', 'wordpoints' );
	}

	public function get_settings_fields() {
	}

	public function is_met() {
		return false !== strpos( $this->value, $this->expected );
	}

	public function get_error( $name ) {

		return sprintf(
			__( '%1$s must contain "%2$s".', 'wordpoints' ) // TODO quotes
			, $name
			, $this->expected
		);
	}
}



class WordPoints_Spec_Entity_Array_Contains extends WordPoints_Spec {

	protected $settings;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	public function get_title() {
		return __( 'Contains', 'wordpoints' );
	}

	public function get_settings_fields() {
		return array(
			'min' => array(
				'slug'  => 'min',
				'label' => __( 'Minimum number of items', 'wordpoints' ),
				'type'  => 'number',
			),
			'max' => array(
				'slug'  => 'max',
				'label' => __( 'Maximum number of items', 'wordpoints' ),
				'type'  => 'number',
			),
		);
	}

	public function validate_settings(
		$settings,
		$arg = null,
		WordPoints_Hook_Reaction_Validator $validator = null
//		$arg,
//		array $settings,
//		WordPoints_Hook_Reaction_Validator $validator
	) {

		if ( ! $this->arg_type_supported( $arg, $validator ) ) {
			return false;
		}

		$this->settings = $settings;
		$this->validator = $validator;

		if ( ! $this->validate_count() ) {
			return false;
		}

		if ( isset( $settings['conditions'] ) ) {
			$this->validate_conditions();
		}

		return $this->settings;
	}

	protected function validate_count() {

		if (
			! empty( $this->settings['max'] )
			&& ! wordpoints_posint( $this->settings['max'] )
		) {
			$this->validator->add_error(
				__( 'The maximum must be a positive integer.', 'wordpoints' )
				, 'max'
			);
		}

		if (
			! empty( $this->settings['min'] )
			&& ! wordpoints_posint( $this->settings['min'] )
		) {
			$this->validator->add_error(
				__( 'The minimum must be a positive integer.', 'wordpoints' )
				, 'min'
			);
		}

		return true;
	}

	protected function validate_conditions() {

		$conditions_extension = wordpoints_hooks()->extensions->get(
			'conditions'
		);

		$conditions = $conditions_extension->validate_settings(
			array( 'conditions' => $this->settings['conditions'] )
			, $this->validator
			, $this->validator->get_event_args()
		);

		$this->settings['conditions'] = $conditions['conditions'];
	}

	public function is_met() {

		$entities = $this->filter_entities( $this->value );

		return $this->check_count( count( $entities ) );
	}

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @param WordPoints_Entity[] $entities
	 *
	 * @return WordPoints_Entity[]
	 */
	protected function filter_entities( $entities ) {

		$conditions_extension = wordpoints_hooks()->extensions->get(
			'conditions'
		);

		foreach ( $entities as $index => $entity ) {

			$matches = $conditions_extension->conditions_are_met(
				$this->expected['conditions']
				, new WordPoints_Hook_Event_Args( array( $entity ) )
			);

			if ( ! $matches ) {
				unset( $entities[ $index ] );
			}
		}

		return $entities;
	}

	protected function check_count( $count ) {

		if ( isset( $this->settings['max'] ) && $count > $this->settings['max'] ) {
			return false;
		}

		if ( isset( $this->settings['min'] ) && $count < $this->settings['min'] ) {
			return false;
		}

		return true;
	}

	public function get_error( $name ) {
		// TODO: Implement get_error() method.
	}
}
// EOF
