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
class WordPoints_Hook_Event_Comment_Leave extends WordPoints_Hook_Event_Dynamic {

	/**
	 * @since 1.0.0
	 */
	protected $generic_entity_slug = 'post';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {

		return sprintf(
			// translators: singular name of the post type
			__( 'Comment on a %s', 'wordpoints' )
			, $this->get_entity_title()
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {

		return sprintf(
			// translators: singular name of the post type
			__( 'When a user leaves a reply to a %s.', 'wordpoints' )
			, $this->get_entity_title()
		);
	}
}

// EOF
