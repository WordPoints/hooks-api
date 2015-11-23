<?php

/**
 * Integer data type class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A handler for integer data.
 *
 * @since 1.0.0
 */
class WordPoints_Data_Type_Integer extends WordPoints_Data_Type {

	/**
	 * @since 1.0.0
	 */
	public function validate_value( $value ) {

		wordpoints_int( $value );

		if ( false === $value ) {
			return new WP_Error(
				'not_integer'
				, __( '%s must be an integer.', 'wordpoints' )
			);
		}

		return $value;
	}
}

// EOF
