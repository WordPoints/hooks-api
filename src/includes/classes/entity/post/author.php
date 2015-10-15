<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Post_Author extends WordPoints_Entity_Relationship {

	/**
	 * @since 1.0.0
	 */
	protected $primary_entity_slug = 'post';

	/**
	 * @since 1.0.0
	 */
	protected $related_entity_slug = 'user';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Author', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_related_entity_ids( $id ) {
		return get_post( $id )->post_author;
	}
}

// EOF
