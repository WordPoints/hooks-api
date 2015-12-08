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

		$parsed = wordpoints_parse_dynamic_slug( $this->slug );

		switch ( $parsed['dynamic'] ) {

			case 'post':
				return __( 'Publish Post', 'wordpoints' );

			case 'page':
				return __( 'Publish Page', 'wordpoints' );

			default:
				return sprintf(
					// translators: singular name of the post type
					__( 'Publish %s', 'wordpoints' )
					, $this->get_entity_title()
				);
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_description() {

		$parsed = wordpoints_parse_dynamic_slug( $this->slug );

		switch ( $parsed['dynamic'] ) {

			case 'post':
				return __( 'When a Post is published.', 'wordpoints' );

			case 'page':
				return __( 'When a Page is published.', 'wordpoints' );

			default:
				return sprintf(
					// translators: singular name of the post type
					__( 'When a %s is published.', 'wordpoints' )
					, $this->get_entity_title()
				);
		}
	}
}

// EOF
