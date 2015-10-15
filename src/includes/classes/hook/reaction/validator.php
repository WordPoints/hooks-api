<?php

/**
 * Hook reaction validator class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Validator for hook reaction settings.
 *
 * @since 1.0.0
 */
final class WordPoints_Hook_Reaction_Validator {

	protected $reaction = false;
	protected $settings;
	protected $fail_fast = false;
	protected $field_stack = array();
	protected $errors = array();

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_Event_Args
	 */
	protected $event_args;

	protected $hooks;

	/**
	 * @since 1.0.0
	 *
	 * @param bool $fail_fast Whether to fail as soon as the first error is found.
	 */
	public function __construct( $settings, $fail_fast = false ) {

		$this->fail_fast = $fail_fast;
		$this->hooks = wordpoints_apps()->hooks;

		if ( $settings instanceof WordPoints_Hook_ReactionI ) {
			$this->reaction = $settings;
			$this->settings = $this->reaction->get_all_meta();
		} else {
			$this->settings = $settings;
		}
	}

	/**
	 * Validates the settings for a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array                  $settings
	 * @param WordPoints_Hook_Reactor $reactor
	 *
	 * @return array The validated settings.
	 */
	public function validate() {

		$this->errors = $this->field_stack = array();

		try {

			// We have to bail early if we don't have a valid event.
			$fail_fast = $this->fail_fast;
			$this->fail_fast = true;

			if ( ! isset( $this->settings['event'] ) ) {
				$this->add_error( __( 'Event type is missing.', 'wordpoints' ), 'event' );
			} elseif ( ! $this->hooks->events->is_registered( $this->settings['event'] ) ) {
				$this->add_error( __( 'Event type is invalid.', 'wordpoints' ), 'event' );
			}

			$event_args = $this->hooks->events->args->get( $this->settings['event'] );

			if ( false === $event_args ) {
				$this->add_error( __( 'Event type is invalid.', 'wordpoints' ), 'event' );
			}

			// From here on out we can collect errors as they come (unless we are
			// supposed to fail fast).
			$this->fail_fast = $fail_fast;

			$this->event_args = new WordPoints_Hook_Event_Args( $event_args );
			$this->event_args->set_validator( $this );

			if ( ! isset( $this->settings['reactor'] ) ) {
				$this->add_error( __( 'Reactor type is missing.', 'wordpoints' ), 'reactor' );
			} elseif ( ! $this->hooks->reactors->is_registered( $this->settings['reactor'] ) ) {
				$this->add_error( __( 'Reactor type is invalid.', 'wordpoints' ), 'reactor' );
			} else {

				$reactor = $this->hooks->reactors->get( $this->settings['reactor'] );

				$this->settings = $reactor->validate_settings( $this->settings, $this, $this->event_args );
			}

			/** @var WordPoints_Hook_Extension $extension */
			foreach ( $this->hooks->extensions->get() as $extension ) {
				$this->settings = $extension->validate_settings( $this->settings, $this, $this->event_args );
			}

			/**
			 * A hook reaction's settings are being validated.
			 *
			 * @param array                              $settings  The settings.
			 * @param WordPoints_Hook_Reaction_Validator $validator The validator object.
			 * @param WordPoints_Hook_Event_Args         $args      The event args object.
			 */
			$this->settings = apply_filters( 'wordpoints_hook_reaction_validate', $this->settings, $this, $this->event_args );

		} catch ( WordPoints_Hook_Validator_Exception $e ) {

			// Do nothing.
			unset( $e );
		}

		return $this->settings;
	}

	public function validate_arg_hierarchy( $hierarchy, $tip = null ) {

		if ( empty( $hierarchy ) || ! is_array( $hierarchy ) ) {
			return false;
		}
//
//		$entities = wordpoints_apps()->entities;
//
//		$entity_slug = reset( $hierarchy );
//
//		$parts = explode( ':', $entity_slug, 2 );
//
//		if ( isset( $parts[1] ) ) {
//			$entity_slug = $parts[1];
//			$hierarchy[ key( $hierarchy ) ] = $entity_slug;
//		}
//
//		$entity = $entities->get( $entity_slug );
//
//		if ( ! $entity ) {
//			return false;
//		}
//
//		$entity_hierarchy = new WordPoints_Entity_Hierarchy( $entity );

		$entity = $this->event_args->get_from_hierarchy( $hierarchy );

		if ( ! $entity ) {
			return false;
		}

//		foreach ( $hierarchy as $arg_slug ) {
//
//			if ( ! $entity_hierarchy->descend( $arg_slug ) ) {
//				return false;
//			}
//		}

//		$entity = $entity_hierarchy->get_current();

		if ( isset( $tip ) && $entity->get_slug() !== $tip ) {
			return false;
		}

		return true;
	}

	public function get_event_args() {
		return $this->event_args;
	}

	/**
	 * Adds an error to the stack.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message The message to add.
	 *
	 * @throws WordPoints_Hook_Validator_Exception If the validator is configured to
	 *                                             fail as soon as an error is found.
	 */
	public function add_error( $message, $field = null ) {

		$field_stack = $this->field_stack;

		if ( $field ) {
			$field_stack[] = $field;
		}

		$this->errors[] = array( 'message' => $message, 'field' => $field_stack );

		if ( $this->fail_fast ) {
			throw new WordPoints_Hook_Validator_Exception;
		}
	}

	/**
	 * Checks if any settings were invalid, giving errors.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the validator found any errors.
	 */
	public function had_errors() {
		return ! empty( $this->errors );
	}

	public function get_errors() {
		return $this->errors;
	}

	public function push_field( $field ) {
		$this->field_stack[] = $field;
	}

	public function pop_field() {
		array_pop( $this->field_stack );
	}

	public function get_field_stack() {
		return $this->field_stack;
	}

	public function get_reaction() {
		return $this->reaction;
	}

	public function get_ID() {

		if ( ! $this->reaction ) {
			return false;
		}

		return $this->reaction->ID;
	}

	/**
	 * Get the slug of the event this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @return string The event slug.
	 */
	public function get_event_slug() {
		return $this->get_meta( 'event' );
	}

	public function get_reactor_slug() {
		return $this->get_meta( 'reactor' );
	}

	/**
	 * Get a piece of metadata for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The meta key.
	 *
	 * @return mixed The meta value.
	 */
	public function get_meta( $key ) {

		if ( ! isset( $this->settings[ $key ] ) ) {
			return null;
		}

		return $this->settings[ $key ];
	}

	public function get_all_meta() {
		return $this->settings;
	}
}

// EOF
