<?php

/**
 * Current Site hook arg class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents the current Site as a hook arg.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Arg_Current_Site extends WordPoints_Hook_Arg {

	/**
	 * @since 1.0.0
	 */
	protected $is_stateful = true;

	/**
	 * @since 1.0.0
	 */
	public function get_value() {

		if ( wordpoints_is_network_context() ) {
			return false;
		}

		return get_current_blog_id();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Site', 'wordpoints' );
	}
}

// EOF
