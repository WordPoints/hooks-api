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
abstract class WordPoints_Entity_Stored_Array
	extends WordPoints_Entity
	implements WordPoints_Entityish_StoredI {

	/**
	 * Get the array that the objects are stored in.
	 *
	 * @since 1.0.0
	 *
	 * @return array The array.
	 */
	abstract public function get_storage_array();

	/**
	 * @since 1.0.0
	 */
	public function get_storage_info() {
		return array(
			'type' => 'array',
			'info' => array( 'type' => 'method' ),
		);
	}
}

// EOF
