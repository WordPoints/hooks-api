<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Hook_Event_Post_Publish extends WordPoints_Hook_Event {

	public function get_title() {
		return __( 'Post Publish', 'wordpoints' );
	}

	public function get_description() {
		return __( 'When a Post, Page, or other type of content is published.', 'wordpoints' );
	}
}

// EOF
