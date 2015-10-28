<?php

/**
 * Hook event args class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents the args associated with a hook event.
 *
 * This is similar to the regular entity hierarchy, except that it handles errors
 * with the reaction validators, and accepts an array of hook args instead of
 * requiring entity objects to be passed directly.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Event_Args extends WordPoints_Entity_Hierarchy {

	/**
	 * The validator associated with the current hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	/**
	 * Construct the object with the arg objects.
	 *
	 * @param WordPoints_Hook_Arg[] $args The hook args.
	 */
	public function __construct( array $args ) {

		parent::__construct();

		foreach ( $args as $arg ) {

			$entity = $arg->get_entity();

			if ( $entity instanceof WordPoints_Entity ) {
				$this->entities[ $arg->get_slug() ] = $entity;
			}
		}
	}

	/**
	 * Set the validator for the current reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Reaction_Validator $validator The validator.
	 */
	public function set_validator( WordPoints_Hook_Reaction_Validator $validator ) {
		$this->validator = $validator;
	}

	/**
	 * @since 1.0.0
	 */
	public function descend( $child_slug ) {

		$result = parent::descend( $child_slug );

		if ( ! $result ) {

			if ( ! ( $this->current instanceof WordPoints_Entity_ParentI ) ) {

				$this->validator->add_error( 'Current arg is not a parent.' );

			} else {

				$child_arg = $this->current->get_child( $child_slug );

				if ( ! $child_arg ) {
					$this->validator->add_error( 'Invalid child.', $child_slug );
				}
			}

		} else {

			$this->validator->push_field( $child_slug );
		}

		return $result;
	}

	/**
	 * @since 1.0.0
	 */
	public function ascend() {

		parent::ascend();

		$this->validator->pop_field();
	}
}

// EOF
