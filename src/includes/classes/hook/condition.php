<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Hook_Condition implements WordPoints_Hook_ConditionI {

	protected $supported_types;

	public function get_supported_types() {
		return $this->supported_types;
	}
//
//	protected function arg_type_supported( $arg, WordPoints_Hook_Reaction_Validator $validator ) {
//
//		$supported_types = $this->get_supported_types();
//
//		$is_supported = false;
//
//		if ( $arg instanceof WordPoints_Entity_Attr ) {
//			$is_supported = isset( $supported_types[ $arg->get_data_type() ] );
//		} elseif ( $arg instanceof WordPoints_Entity_Array ) {
//			$is_supported = isset( $supported_types['entity_array'] );
//		}
//
//		if ( ! $is_supported ) {
//			$validator->add_error(
//				__( 'This type of condition does not work for the selected attribute.', 'wordpoints' )
//			);
//		}
//
//		return $is_supported;
//	}

	public function validate_settings(
		$arg,
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator
	) {

		if ( ! isset( $settings['value'] ) || '' === $settings['value'] ) {

			$settings_fields = $this->get_settings_fields();

			$validator->add_error(
				sprintf(
					__( '%s is required.', 'wordpoints' )
					, $settings_fields['value']['label']
				)
				, 'value'
			);

			return false;
		}

		if ( $arg instanceof WordPoints_Entity_Attr ) {

			$data_types = wordpoints_apps()->data_types;

			$data_type = $data_types->get( $arg->get_data_type() );

			// If this data type isn't recognized, that's probably OK. Validation is
			// just to help the user know that they've made a mistake anyway.
			if ( ! $data_type ) {
				return $settings;
			}

			$validated_value = $data_type->validate_value( $settings['value'] );

			if ( is_wp_error( $validated_value ) ) {

				$settings_fields = $this->get_settings_fields();

				$validator->add_error(
					sprintf(
						__( '%s does not match the correct format.', 'wordpoints' )
						, $settings_fields['value']['label']
					)
					, 'value'
				);

				return false;
			}

			$settings['value'] = $validated_value;
		}

		return $settings;
	}
}

// EOF
