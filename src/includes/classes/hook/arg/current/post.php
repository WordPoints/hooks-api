<?php

/**
 * Current Post hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents the current Post as a hook arg.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Arg_Current_Post extends WordPoints_Hook_Arg {

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Current Post', 'wordpoints' ); // TODO better title?
	}

	/**
	 * @since 1.0.0
	 */
	public function get_value() {

		if ( ! is_main_query() ) {
			return false;
		}

		$object = get_queried_object();

		if ( $object instanceof WP_Post ) {
			return $object;
		} else {
			return false;
		}
	}
}

// EOF
