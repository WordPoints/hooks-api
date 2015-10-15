<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Entity_Relationship
	extends WordPoints_Entityish
	implements WordPoints_Entity_ParentI, WordPoints_Entity_ChildI {

	protected $primary_entity_slug;

	/**
	 * The slug of the related entity type.
	 *
	 * You must either define this or override get_related_entity_slug().
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $related_entity_slug;

	/**
	 * The function to get the list of related IDs.
	 *
	 * You must either define this or override get_related_entity_ids().
	 *
	 * @since 1.0.0
	 *
	 * @var callable
	 */
	protected $related_getter;

	public function get_primary_entity_slug() {
		return $this->primary_entity_slug;
	}

	public function get_related_entity_slug() {
		return $this->related_entity_slug;
	}

	/**
	 * Parse an entity slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug to parse.
	 *
	 * @return array The parsed slug in the 'slug' key and whether it is an array in
	 *               the 'is_array' key.
	 */
	protected function parse_slug( $slug ) {

		$is_array = false;

		if ( '{}' === substr( $slug, -2 ) ) {
			$is_array = true;
			$slug = substr( $slug, 0, -2 );
		}

		return array( 'slug' => $slug, 'is_array' => $is_array );
	}

	public function get_child( $child_slug ) {

		if ( $child_slug !== $this->get_related_entity_slug() ) {
			return false;
		}

		$parsed_slug = $this->parse_slug( $child_slug );

		if ( $parsed_slug['is_array'] ) {

			$child = new WordPoints_Entity_Array( $parsed_slug['slug'] );

		} else {

			if ( ! isset( $this->entities ) ) {
				$this->entities = wordpoints_apps()->entities;
			}

			$child = $this->entities->get( $parsed_slug['slug'] );
		}

		if ( isset( $this->the_value ) ) {
			$child->set_the_value( $this->the_value );
		}

		return $child;
	}

	public function set_the_value_from_entity( WordPoints_Entity $entity ) {
		$this->set_the_value(
			$this->get_related_entity_ids( $entity->get_the_id() )
		);
	}

	protected function get_related_entity_ids( $id ) {
		return call_user_func( $this->related_getter, $id );
	}
}

abstract class WordPoints_Entity_Relationship_OneToOne extends WordPoints_Entity_Relationship {}
abstract class WordPoints_Entity_Relationship_OneToMany extends WordPoints_Entity_Relationship {}

// EOF
