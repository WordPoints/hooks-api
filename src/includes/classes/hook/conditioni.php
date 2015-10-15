<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

interface WordPoints_Hook_ConditionI {

	public function get_title();

	public function get_supported_types();

	public function get_settings_fields();

	public function validate_settings(
		$arg,
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator
	);

	public function is_met( $settings, WordPoints_Entity_Hierarchy $args );
}

class WordPoints_Hook_Condition_Array_Count extends WordPoints_Hook_Condition {

	protected $supported_types = array(
		'array' => true,
		'entity_array' => true,
	);

	public function get_settings_fields() {
		return array(
			'count' => array(
				'type' => 'number',
				'label' => _x( 'Count', 'form label', 'wordpoints' ),
			),
		);
	}

	public function is_met( $settings, WordPoints_Entity_Hierarchy $args ) {

		return count( $args->get_current()->get_the_value() ) === $settings['count'];
	}

	public function validate_settings(
		$arg,
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator
	) {

		if ( ! isset( $settings['count'] ) ) {

			$settings_fields = $this->get_settings_fields();

			$validator->add_error(
				sprintf(
					__( '%s is required.', 'wordpoints' )
					, $settings_fields['count']['label']
				)
				, 'count'
			);

			return false;
		}

		if ( ! wordpoints_posint( $settings['count'] ) ) {

			$settings_fields = $this->get_settings_fields();

			$validator->add_error(
				sprintf(
					__( '%s count must be a positive integer.', 'wordpoints' )
					, $settings_fields['count']['label']
				)
				, 'count'
			);

			return false;
		}

		return $settings;
	}

	public function get_title() {
		// TODO: Implement get_title() method.
	}
}

class WordPoints_Hook_Condition_Array_Contains extends WordPoints_Hook_Condition {

	protected $supported_types = array( 'array' => true );

	public function is_met( $settings, WordPoints_Entity_Hierarchy $args ) {

		return in_array(
			$settings['value']
			, $args->get_current()->get_the_value()
			, true
		);
	}

	public function get_settings_fields() {
		// TODO: Implement get_settings_fields() method.
	}

	public function get_title() {
		// TODO: Implement get_title() method.
	}
}

// EOF
