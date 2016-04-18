<?php

/**
 * Field entity attribute interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by attributes that are stored in an entity field.
 * 
 * @since 1.0.0
 */
interface WordPoints_Entity_Attr_FieldI {

	/**
	 * Get the name of the entity field where this attribute is stored.
	 *
	 * @since 1.0.0
	 *        
	 * @return string The name of the field.
	 */
	public function get_field();
}

// EOF
