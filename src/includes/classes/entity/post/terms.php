<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Post_Terms extends WordPoints_Entity_Relationship_OneToMany {

	protected $primary_entity_slug = 'post';
	protected $related_entity_slug = 'term{}';

	public function get_title() {
		return __( 'Terms', 'wordpoints' );
	}

	public function get_related_entity_ids( $id ) {

		$taxonomies = get_object_taxonomies( get_post( $id ) );

		return wp_get_object_terms( $id, $taxonomies, array( 'fields' => 'ids' ) );
	}
}

// EOF
