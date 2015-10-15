<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

interface WordPoints_Hook_EventI {

	public function __construct( $slug );

	public function get_slug();

	public function get_title();

	public function get_description();
}




class WordPoints_Hook_Event_User_Visit extends WordPoints_Hook_Event {

	public function get_title() {
		return __( 'Visit', 'wordpoints' );
	}

	public function get_description() {
		return __( 'When a logged-in user or guest visits the site.', 'wordpoints' );
	}
}



// EOF
