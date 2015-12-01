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
class WordPoints_Hook_Event_Post_Publish extends WordPoints_Hook_Event_Dynamic {

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
			__( 'Publish %s', 'wordpoints' )
			, $this->get_entity_title()
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {

		return sprintf(
			// translators: singular name of the post type
			__( 'When a %s is published.', 'wordpoints' )
			, $this->get_entity_title()
		);
	}
}

// EOF
