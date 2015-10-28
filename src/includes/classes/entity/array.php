<?php

/**
 * Entity array class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents an array of entities.
 *
 * All of the entities an an array must be of the same type. For example, you can
 * have an array of Posts, or an array of Users, but you cannot have an array of
 * Posts and Users both together.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Array {

	/**
	 * The slug of the type of the entities in this array.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $entity_slug;

	/**
	 * The objects for the entities in this array.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Entity[]
	 */
	protected $the_entities = array();

	/**
	 * The raw values of the entities in this array.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $the_value;

	/**
	 * Construct the array with the slug of the type of the entities it will contain.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entity_slug The slug of the entity type.
	 */
	public function __construct( $entity_slug ) {
		$this->entity_slug = $entity_slug;
	}

	/**
	 * Get the slug of the type of entities in this array.
	 *
	 * @since 1.0.0
	 *
	 * @return string The entity slug.
	 */
	public function get_entity_slug() {
		return $this->entity_slug;
	}

	/**
	 * Populate this array with some entities.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values The entities or their IDs to populate the array with.
	 */
	public function set_the_value( $values ) {

		$object = wordpoints_apps()->entities->get( $this->entity_slug );

		$this->the_entities = array();

		$this->the_value = $values;

		foreach ( $values as $value ) {

			/** @var WordPoints_Entity $entity */
			$entity = clone $object;
			$entity->set_the_value( $value );
			$this->the_entities[] = $entity;
		}
	}

	/**
	 * Get the entities in this array.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Entity[] The objects for the entities in this array.
	 */
	public function get_the_entities() {
		return $this->the_entities;
	}
}

// EOF
