<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Hook_Event_Args extends WordPoints_Entity_Hierarchy {

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	public function __construct( array $args ) {

		parent::__construct();

		foreach ( $args as $arg ) {
			if ( $arg instanceof WordPoints_Hook_Arg ) {

				$entity = $arg->get_entity();

				if ( $entity instanceof WordPoints_Entity ) {
					$this->entities[ $arg->get_slug() ] = $entity;
				}
			}
		}
	}

	public function set_validator( WordPoints_Hook_Reaction_Validator $validator ) {
		$this->validator = $validator;
	}

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

	public function ascend() {

		parent::ascend();

		$this->validator->pop_field();
	}
}

// EOF
