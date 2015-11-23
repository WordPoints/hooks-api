<?php

/**
 * Entity array contains hook condition class.
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

/**
 * Represents a contains condition on an entity array.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Condition_Entity_Array_Contains
	extends WordPoints_Hook_Condition {

	/**
	 * The condition's settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * The conditions extension.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Extension_Conditions
	 */
	protected $conditions_extension;

	/**
	 * The validator for the reaction the condition belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	/**
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->conditions_extension = wordpoints_hooks()->extensions->get(
			'conditions'
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Contains', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
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

	/**
	 * @since 1.0.0
	 */
	public function validate_settings(
		$arg,
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator
	) {

		$this->settings = $settings;
		$this->validator = $validator;

		$this->validate_count();

		if ( isset( $settings['conditions'] ) ) {
			$this->validate_conditions();
		}

		return $this->settings;
	}

	/**
	 * Validate the count setting for the condition.
	 *
	 * @since 1.0.0
	 */
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
	}

	/**
	 * Validate the sub conditions for the condition.
	 *
	 * @since 1.0.0
	 */
	protected function validate_conditions() {

		$current_arg = $this->validator->get_event_args()->get_current();

		$args = new WordPoints_Hook_Event_Args( array() );

		if ( $current_arg instanceof WordPoints_Entity_Array ) {

			$entity = wordpoints_entities()->get(
				$current_arg->get_entity_slug()
			);

			if ( $entity instanceof WordPoints_Entity ) {
				$args->add_entity( $entity );
			}
		}

		$args->set_validator( $this->validator );

		$conditions = $this->conditions_extension->validate_settings(
			array( 'conditions' => $this->settings['conditions'] )
			, $this->validator
			, $args
		);

		$this->settings['conditions'] = $conditions['conditions'];
	}

	/**
	 * @since 1.0.0
	 */
	public function is_met( array $settings, WordPoints_Hook_Event_Args $args ) {

		$this->settings = $settings;

		/** @var WordPoints_Entity_Array $arg */
		$arg = $args->get_current();

		$entities = $this->filter_entities( $arg->get_the_entities() );

		return $this->check_count( count( $entities ) );
	}

	/**
	 * Filter an array of entities based on the sub-conditions of this condition.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Entity[] $entities The array of entities.
	 *
	 * @return WordPoints_Entity[] The entities that matched the sub-conditions.
	 */
	protected function filter_entities( $entities ) {

		foreach ( $entities as $index => $entity ) {

			$event_args = new WordPoints_Hook_Event_Args( array() );
			$event_args->add_entity( $entity );

			$matches = $this->conditions_extension->conditions_are_met(
				$this->settings['conditions']
				, $event_args
			);

			if ( ! $matches ) {
				unset( $entities[ $index ] );
			}
		}

		return $entities;
	}

	/**
	 * Check whether a count matches the count settings.
	 *
	 * @since 1.0.0
	 *
	 * @param int $count The number of entities that matched the sub-conditions.
	 *
	 * @return bool Whether the count met the requirements.
	 */
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
