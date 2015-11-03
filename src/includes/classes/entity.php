<?php

/**
 * Class for representing an entity.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents an entity.
 *
 * An entity can be just about anything, like a Post, a User, a Comment, a Site, etc.
 * This class defines a single common interface for interacting with entities. This
 * is useful when some code needs to be able to work with several different kinds of
 * entities and can't know beforehand what they are.
 *
 * Each different type of entity is defined by a child of this class.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entity
	extends WordPoints_Entityish
	implements WordPoints_Entity_ParentI {

	//
	// Protected
	//

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

	/**
	 * The entity itself.
	 *
	 * This will probably always be an array or object.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $the_entity;

	/**
	 * Get an entity by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $id The unique ID of the entity.
	 *
	 * @return mixed The entity.
	 */
	protected function get_entity( $id ) {
		return call_user_func( $this->getter, $id );
	}

	/**
	 * Checks if a value is an entity of this type.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $entity A value that might be an entity.
	 *
	 * @return bool Whether the passed value is an entity.
	 */
	protected function is_entity( $entity ) {

		if ( ! is_object( $entity ) && ! is_array( $entity ) ) {
			return false;
		}

		return (bool) $this->get_entity_id( $entity );
	}

	/**
	 * Gets the value of one of an entity's attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $entity An entity of this type.
	 * @param string $attr   The attribute whose value to get.
	 *
	 * @return mixed The value of the attribute of the entity.
	 */
	protected function get_attr_value( $entity, $attr ) {

		if ( is_array( $entity ) ) {
			return $entity[ $attr ];
		} else {
			return $entity->{$attr};
		}
	}

	/**
	 * Get the ID from an entity.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $entity The entity (usually object or array).
	 *
	 * @return mixed The ID of the entity.
	 */
	protected function get_entity_id( $entity ) {
		return $this->get_attr_value( $entity, $this->get_id_field() );
	}

	//
	// Public
	//

	/**
	 * Get the attribute that holds the entity's unique ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string The attribute that holds the entity's unique ID.
	 */
	public function get_id_field() {
		return $this->id_field;
	}

	/**
	 * Get the human ID for an entity.
	 *
	 * The human ID is a human readable identifier for the entity, and may be
	 * different than the regular ID. It is also possible that the human ID will not
	 * always be unique.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $id The ID of an entity.
	 *
	 * @return mixed The human identifier for the entity.
	 */
	public function get_human_id( $id ) {

		return $this->get_attr_value(
			$this->get_entity( $id )
			, $this->human_id_field
		);
	}

	/**
	 * Check if an entity exists, by ID.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $id The entity ID.
	 *
	 * @return bool Whether or not an entity with that ID exists.
	 */
	public function exists( $id ) {
		return (bool) $this->get_entity( $id );
	}

	/**
	 * Get a child of this entity.
	 *
	 * Entities can have children, which currently fall into two types: attributes
	 * and relationships.
	 *
	 * @since 1.0.0
	 *
	 * @param string $child_slug The slug of the child.
	 *
	 * @return WordPoints_Entityish|false The child's object, or false if not found.
	 */
	public function get_child( $child_slug ) {

		$children = wordpoints_entities()->children;

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

	/**
	 * Set the value of this entity.
	 *
	 * This class can represent a type of entity generically (e.g., Post), or a
	 * specific entity of that type (the Post with ID 3). This function allows you to
	 * make this object instance represent a specific entity.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value An entity or entity ID.
	 */
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

	/**
	 * Get the value of one of this entity's attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $attr The attribute to get the value of.
	 *
	 * @return mixed The value of the attribute.
	 */
	public function get_the_attr_value( $attr ) {
		return $this->get_attr_value( $this->the_entity, $attr );
	}

	/**
	 * Get the ID of the entity.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed The ID of the entity.
	 */
	public function get_the_id() {
		return $this->get_the_value();
	}
}

// EOF
