<?php

/**
 * User Register hook event class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * An event that fires when a user is registered.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Event_User_Register extends WordPoints_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Register', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {
		return __( 'Registering.', 'wordpoints' );
	}
}

// EOF
