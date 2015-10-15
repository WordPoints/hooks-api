<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Hook_Condition_Equals extends WordPoints_Hook_Condition {

	protected $supported_types = array(
		'array'  => true,
		'float'  => true,
		'int'    => true,
		'string' => true,
	);

	public function get_title() {
		return __( 'Equals', 'wordpoints' );
	}

	public function get_settings_fields() {

		return array(
			'value' => array(
				'type' => 'text',
				'label' => __( 'Equals', 'wordpoints' ), // TODO
			),
		);
	}

	public function is_met( $settings, WordPoints_Entity_Hierarchy $args ) {

		return $settings['value'] === $args->get_current()->get_the_value();
	}
}

// EOF
