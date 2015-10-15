<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Hook_Arg_Current_User extends WordPoints_Hook_Arg {

	public function get_value() {
		return wp_get_current_user();
	}

	public function get_title() {
		return __( 'Visitor', 'wordpoints' );
	}
}

// EOF
