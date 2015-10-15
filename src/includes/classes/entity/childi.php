<?php

/**
 * Interface for an entity child.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Defines the API for an entity child.
 *
 * @since 1.0.0
 */
interface WordPoints_Entity_ChildI {

	/**
	 * Set the value of the child from an entity.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Entity $entity The entity.
	 */
	public function set_the_value_from_entity( WordPoints_Entity $entity );
}

// EOF
