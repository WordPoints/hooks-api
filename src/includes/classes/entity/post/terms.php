<?php

/**
 * Post terms entity relationship class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the relationship between a Post and its Terms.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Post_Terms extends WordPoints_Entity_Relationship {

	/**
	 * @since 1.0.0
	 */
	protected $primary_entity_slug = 'post';

	/**
	 * @since 1.0.0
	 */
	protected $related_entity_slug = 'term{}';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Terms', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_related_entity_ids( $id ) {

		$taxonomies = get_object_taxonomies( get_post( $id ) );

		return wp_get_object_terms( $id, $taxonomies, array( 'fields' => 'ids' ) );
	}
}

// EOF
