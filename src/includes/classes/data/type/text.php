<?php

/**
 * Text data type class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * A handler for text data.
 *
 * Text is just a string, with no particular restrictions. However, it is usually a
 * string of space-delimited words.
 *
 * @since 1.0.0
 */
class WordPoints_Data_Type_Text extends WordPoints_Data_Type {

	/**
	 * @since 1.0.0
	 */
	public function validate_value( $value ) {

		if ( ! is_string( $value ) ) {
			return new WP_Error(
				'not_string'
				, __( '%s must be a text value.', 'wordpoints' )
			);
		}

		return $value;
	}
}

// EOF
