<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Hook_Condition_Entity_Array_Contains extends WordPoints_Hook_Condition {

	protected $settings;

	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Extension_Conditions
	 */
	protected $conditions_extension;

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	public function __construct() {

		$this->conditions_extension = wordpoints_apps()->hooks->extensions->get(
			'conditions'
		);
	}

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
		$arg,
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator
	) {

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

		$conditions = $this->conditions_extension->validate_settings(
			array( 'conditions' => $this->settings['conditions'] )
			, $this->validator
			, $this->validator->get_event_args()
		);

		$this->settings['conditions'] = $conditions['conditions'];
	}

	public function is_met( $settings, WordPoints_Entity_Hierarchy $args ) {

		$this->settings = $settings;

		/** @var WordPoints_Entity_Array $arg */
		$arg = $args->get_current();

		$entities = $this->filter_entities( $arg->get_the_entities() );

		return $this->check_count( count( $entities ) );
	}

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @param WordPoints_EntityishI[] $entities
	 *
	 * @return WordPoints_EntityishI[]
	 */
	protected function filter_entities( $entities ) {

		foreach ( $entities as $index => $entity ) {

			$matches = $this->conditions_extension->conditions_are_met(
				$this->settings['conditions']
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
}

// EOF
