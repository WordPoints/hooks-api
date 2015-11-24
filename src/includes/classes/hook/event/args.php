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
	 * Whether to push fields onto the validator when we descend into the hierarchy.
	 *
	 * This will usually be true, however, when we are getting a value from the
	 * hierarchy, we aren't actually descending into a sub-field, but descending
	 * down the arg hierarchy stored within a field.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $push_on_descend = true;

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

		// Just in case no validator has been set.
		if ( ! $this->validator ) {
			return $result;
		}

		if ( ! $result ) {

			if ( ! isset( $this->current ) ) {

				$this->validator->add_error(
					sprintf(
						__( 'The %s arg is not registered for this event.', 'wordpoints' ) // TODO message
						, $child_slug
					)
				);

			} elseif ( ! ( $this->current instanceof WordPoints_Entity_ParentI ) ) {

				$this->validator->add_error(
					__( 'Cannot get descendant of %s: not a parent.', 'wordpoints' ) // TODO message
				);

			} else {

				$child_arg = $this->current->get_child( $child_slug );

				if ( ! $child_arg ) {
					$this->validator->add_error(
						__( '%s does not have a child "%s".', 'wordpoints' ) // TODO message
						, $this->push_on_descend ? $child_slug : null
					);
				}
			}

		} elseif ( $this->push_on_descend ) {

			$this->validator->push_field( $child_slug );
		}

		return $result;
	}

	/**
	 * @since 1.0.0
	 */
	public function ascend() {

		$ascended = parent::ascend();

		if ( $ascended && $this->validator ) {
			$this->validator->pop_field();
		}

		return $ascended;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_from_hierarchy( array $hierarchy ) {

		$this->push_on_descend = false;
		$entityish = parent::get_from_hierarchy( $hierarchy );
		$this->push_on_descend = true;

		return $entityish;
	}
}

// EOF
