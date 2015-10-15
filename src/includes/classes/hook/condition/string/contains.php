<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Hook_Condition_String_Contains extends WordPoints_Hook_Condition {

	public function get_title() {
		return __( 'Contains', 'wordpoints' );
	}

	public function get_settings_fields() {
		return array(
			'value' => array(
				'type'     => 'text',
				'label'    => __( 'Contains', 'wordpoints' ),
				'required' => true,
			),
		);
	}

	public function is_met( $settings, WordPoints_Entity_Hierarchy $args ) {

		return false !== strpos(
			$args->get_current()->get_the_value()
			, $settings['value']
		);
	}
}

// EOF
