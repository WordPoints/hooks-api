<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

interface WordPoints_Entity_Attr_Enumerable {

	/**
	 * Get a list of the predefined values this attribute can have.
	 *
	 * The list should contain an array of data for each value, with the value itself
	 * accessible via the 'value' key. The slug for the value, if different from the
	 * value itself, should be supplied as the 'slug'. A human-readable 'label' may
	 * optionally be supplied.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] An array of data for each value, indexed by value slug.
	 */
	public function get_enumerated_values();
}

// EOF
