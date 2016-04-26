<?php

/**
 * Field entity relationship interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by relationships that are stored in a field of the primary entity.
 * 
 * @since 1.0.0
 */
interface WordPoints_Entity_Relationship_Stored_FieldI {

	/**
	 * Get the name of the primary entity's field where this relationship is stored.
	 * 
	 * The field's value should be the ID of the related entity, or an array of IDs
	 * if it is a one-to-many relationship.
	 *
	 * @since 1.0.0
	 *        
	 * @return string The name of the field.
	 */
	public function get_field();
}

// EOF
