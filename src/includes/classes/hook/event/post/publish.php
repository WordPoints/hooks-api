<?php

/**
 * Post publish hook event class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a hook event that occurs when a post is published.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Event_Post_Publish extends WordPoints_Hook_Event {

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Post Publish', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {
		return __( 'When a Post, Page, or other type of content is published.', 'wordpoints' );
	}
}

// EOF
