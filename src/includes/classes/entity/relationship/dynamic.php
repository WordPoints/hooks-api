<?php

/**
 * Dynamic entity relationship class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the relationship between dynamic entities.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entity_Relationship_Dynamic extends WordPoints_Entity_Relationship {

	/**
	 * @since 1.0.0
	 */
	public function __construct( $slug ) {

		parent::__construct( $slug );

		$parts = explode( '-', $this->slug, 2 );

		if ( isset( $parts[1] ) ) {

			$parsed = $this->parse_slug( $this->related_entity_slug );

			$this->primary_entity_slug = "{$this->primary_entity_slug}-{$parts[1]}";
			$this->related_entity_slug = "{$parsed['slug']}-{$parts[1]}";

			if ( $parsed['is_array'] ) {
				$this->related_entity_slug .= '{}';
			}
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {

		$parsed = $this->parse_slug( $this->related_entity_slug );

		$entity = wordpoints_entities()->get( $parsed['slug'] );

		if ( $entity instanceof WordPoints_Entity ) {
			return $entity->get_title();
		} else {
			return $this->related_entity_slug;
		}
	}
}

// EOF
