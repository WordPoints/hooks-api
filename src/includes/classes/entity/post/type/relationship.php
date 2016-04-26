<?php

/**
 * Post Type entity relationship class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the relationship between a Post and its Post Type.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Post_Type_Relationship
	extends WordPoints_Entity_Relationship_Stored_Field {

	/**
	 * @since 1.0.0
	 */
	protected $primary_entity_slug = 'post';

	/**
	 * @since 1.0.0
	 */
	protected $related_entity_slug = 'post_type';

	/**
	 * @since 1.0.0
	 */
	protected $related_ids_field = 'post_type';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Post Type', 'wordpoints' );
	}
}

// EOF
