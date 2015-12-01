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
class WordPoints_Entity_Post_Terms extends WordPoints_Entity_Relationship_Dynamic {

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

		$taxonomy = get_taxonomy( substr( $this->slug, 6 /* terms\ */ ) );

		if ( $taxonomy ) {
			return $taxonomy->labels->name;
		} else {
			return parent::get_title();
		}
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_related_entity_ids( WordPoints_Entity $entity ) {

		$id = $entity->get_the_id();

		$taxonomies = get_object_taxonomies( get_post( $id ) );

		return wp_get_object_terms( $id, $taxonomies, array( 'fields' => 'ids' ) );
	}
}

// EOF
