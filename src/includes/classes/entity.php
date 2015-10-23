<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Entity
	extends WordPoints_Entityish
	implements WordPoints_Entity_ParentI {

	/**
	 * The field the entity is identified by.
	 *
	 * You must either define this or override get_id_field() in your subclass.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $id_field;

	/**
	 * The field the entity can be identified by humans by.
	 *
	 * You must either define this or override get_human_id().
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $human_id_field;

	/**
	 * A function to call with an entity ID to retrieve that entity.
	 *
	 * You must either define this or override get_entity().
	 *
	 * @since 1.0.0
	 *
	 * @var callable
	 */
	protected $getter;

	protected $the_entity;

	protected function get_entity( $id ) {
		return call_user_func( $this->getter, $id );
	}

	public function get_id_field() {
		return $this->id_field;
	}

	abstract protected function is_entity( $entity );
	abstract protected function get_attr_value( $entity, $attr );

	protected function get_entity_id( $entity ) {
		return $this->get_attr_value( $entity, $this->get_id_field() );
	}

	public function get_human_id( $id ) {
		return $this->get_attr_value( $this->get_entity( $id ), $this->human_id_field );
	}

	public function get_child( $child_slug ) {

		$children = wordpoints_apps()->entities->children;

		$child = $children->get( $this->slug, $child_slug );

		if ( $child ) {
			if (
				isset( $this->the_value )
				&& $child instanceof WordPoints_Entity_ChildI
			) {
				$child->set_the_value_from_entity( $this );
			}

			return $child;
		}

		return false;
	}

	public function exists( $id ) {
		return (bool) $this->get_entity( $id );
	}

	public function set_the_value( $value ) {

		if ( $this->is_entity( $value ) ) {
			$entity = $value;
			$value = $this->get_entity_id( $value );
		} else {
			$entity = $this->get_entity( $value );
		}

		$this->the_value = $value;
		$this->the_entity = $entity;
	}

	public function get_the_attr_value( $attr ) {
		return $this->get_attr_value( $this->the_entity, $attr );
	}

	public function get_the_id() {
		return $this->get_the_value();
	}
}

// EOF
