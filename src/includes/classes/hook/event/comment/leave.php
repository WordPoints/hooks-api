<?php

/**
 * Comment leave hook event class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a hook event that occurs when a comment is left.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Event_Comment_Leave extends WordPoints_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Comment', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {
		return __( 'When a user leaves a reply to a Post or other type of content.', 'wordpoints' );
	}
}

// EOF
