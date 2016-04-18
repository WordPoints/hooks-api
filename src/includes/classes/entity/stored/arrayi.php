<?php

/**
 * Array stored entity interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by entities that are stored in arrays.
 *
 * @since 1.0.0
 */
interface WordPoints_Entity_Stored_ArrayI {

	/**
	 * Get the array that the objects are stored in.
	 *
	 * @since 1.0.0
	 *
	 * @return array The array.
	 */
	public function get_storage_array();
}

// EOF
