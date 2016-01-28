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

	/**
	 * Assert that one or more hits were logged.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data  The hit data.
	 * @param int   $count The number of expected logs.
	 */
	public function assertHitsLogged( array $data, $count = 1 ) {

		global $wpdb;

		$now = current_time( 'timestamp' );

		$superseded_by = null;

		if ( isset( $data['superseded_by'] ) ) {
			$superseded_by = $data['superseded_by'];
			unset( $data['superseded_by'] );
		}

		$data = array_merge(
			array(
				'firer' => 'test_firer',
				'signature' => str_repeat( '-', 64 ),
				'event' => 'test_event',
				'reactor' => 'test_reactor',
				'reaction_type' => 'standard',
				'reaction_id' => 1,
				'superseded_by' => null,
			)
			, $data
		);

		ksort( $data );

		$hits = $wpdb->get_results(
			$wpdb->prepare(
				"
					SELECT *
					FROM `{$wpdb->wordpoints_hook_hits}`
					WHERE `event` = %s
					AND `firer` = %s
					AND `reaction_id` = %d
					AND `reaction_type` = %s
					AND `reactor` = %s
					AND `signature` = %s
				"
				, $data
			)
		);

		$hits = wp_list_filter( $hits, array( 'superseded_by' => $superseded_by ) );

		$this->assertCount( $count, $hits );

		foreach ( $hits as $hit ) {
			$this->assertEquals( $superseded_by, $hit->superseded_by );
			$this->assertLessThanOrEqual( 2, $now - strtotime( $hit->date, $now ) );
		}
	}
}

// EOF
