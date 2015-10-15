<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Entity_Object extends WordPoints_Entity {

	protected function get_attr_value( $entity, $attr ) {
		return $entity->{$attr};
	}

	protected function is_entity( $entity ) {

		if ( ! is_object( $entity ) ) {
			return false;
		}

		return (bool) $this->get_entity_id( $entity );
	}
}

// EOF
