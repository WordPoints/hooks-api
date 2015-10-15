<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Hook_Event_Comment_Leave extends WordPoints_Hook_Event {

	public function get_title() {
		return __( 'Comment', 'wordpoints' );
	}

	public function get_description() {
		return __( 'When a user leaves a reply to a Post or other type of content.', 'wordpoints' );
	}
}

// EOF
