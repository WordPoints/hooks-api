<?php

/**
 * Hooks test case class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case for testing the hooks API.
 *
 * @since 1.0.0
 */
abstract class WordPoints_PHPUnit_TestCase_Hooks extends WordPoints_PHPUnit_TestCase {

	/**
	 * Provides several different sets of valid condition settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Sets of valid settings.
	 */
	public function data_provider_valid_condition_settings() {

		$conditions = array(
			'_conditions' => array(
				array(
					'type'     => 'test',
					'settings' => array( 'value' => 'a' ),
				),
			),
		);

		$entity = array( 'test_entity' => $conditions );
		$child = $both = array( 'test_entity' => array( 'child' => $conditions ) );

		$both['test_entity']['_conditions'] = $conditions['_conditions'];

		return array(
			'none' => array( array() ),
			'empty' => array( array( 'conditions' => array() ) ),
			'entity' => array( array( 'conditions' => $entity ) ),
			'child' => array( array( 'conditions' => $child ) ),
			'both' => array( array( 'conditions' => $both ) ),
			'two_entities' => array(
				array(
					'conditions' => array(
						'test_entity' => $conditions,
						'another' => $conditions,
					),
				),
			),
		);
	}

	/**
	 * Provides an array of possible condition settings, each with one invalid item.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Every possible set of settings with one invalid item.
	 */
	public function data_provider_invalid_condition_settings() {

		$conditions = array(
			'_conditions' => array(
				array(
					'type'     => 'test',
					'settings' => array( 'value' => 'a' ),
				),
			),
		);

		$invalid_settings = array(
			'not_array' => array(
				array( 'conditions' => 'not_array' ),
				array( 'conditions' ),
			),
			'invalid_entity' => array(
				array( 'conditions' => array( 'invalid_entity' => $conditions ) ),
				array( 'conditions' ),
			),
			'incorrect_data_type' => array(
				array( 'conditions' => array( 'test_entity' => array( 'child' => $conditions ) ) ),
				array( 'conditions', 'test_entity', 'child', '_conditions', 0 ),
			),
		);

		$invalid_setting_fields = array(
			'type' => 'invalid',
			'settings' => array(),
		);

		foreach ( $conditions['_conditions'][0] as $slug => $value ) {

			$invalid_conditions = $conditions;

			unset( $invalid_conditions['_conditions'][0][ $slug ] );

			$field = array( 'conditions', 'test_entity', '_conditions', 0 );

			$invalid_settings[ "no_{$slug}" ] = array(
				array( 'conditions' => array( 'test_entity' => $invalid_conditions ) ),
				$field,
			);

			if ( isset( $invalid_setting_fields[ $slug ] ) ) {
				$invalid_conditions['_conditions'][0][ $slug ] = $invalid_setting_fields[ $slug ];

				$field[] = $slug;

				if ( 'settings' === $slug ) {
					$field[] = 'value';
				}

				$invalid_settings[ "invalid_{$slug}" ] = array(
					array( 'conditions' => array( 'test_entity' => $invalid_conditions ) ),
					$field,
				);
			}
		}

		return $invalid_settings;
	}


	/**
	 * Provides an array of possible settings settings which are not met.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Condition settings that are unmet.
	 */
	public function data_provider_unmet_conditions() {

		$conditions = array(
			'_conditions' => array(
				array(
					'type'     => 'unmet',
					'settings' => array( 'value' => 'a' ),
				),
			),
		);

		$settings = array(
			'unmet_condition' => array(
				array( 'conditions' => array( 'test_entity' => $conditions ) ),
			),
			'unmet_child_condition' => array(
				array(
					'conditions' => array(
						'test_entity' => array( 'child' => $conditions ),
					),
				),
			),
		);

		return $settings;
	}

	/**
	 * Assert that a value is a hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $reaction The reaction.
	 */
	public function assertIsReaction( $reaction ) {

		if ( $reaction instanceof WP_Error ) {
			$reaction = $reaction->get_error_data();
		}

		if ( $reaction instanceof WordPoints_Hook_Reaction_Validator ) {

			$message = '';

			foreach ( $reaction->get_errors() as $error ) {
				$message .= PHP_EOL . 'Field: ' . implode( '.',  $error['field'] );
				$message .= PHP_EOL . 'Error: ' . $error['message'];

			}

			$this->fail( $message );
		}

		$this->assertInstanceOf( 'WordPoints_Hook_ReactionI', $reaction );
	}
}

// EOF
