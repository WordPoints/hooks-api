<?php

/**
 * Entity hierarchy class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a hierarchy of entities and their descendants.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Hierarchy implements WordPoints_Entity_HierarchyI {

	/**
	 * The ordered list of entities in the current hierarchy.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_EntityishI[]
	 */
	protected $hierarchy = array();

	/**
	 * The current entity/entity child in the hierarchy.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_EntityishI
	 */
	protected $current;

	/**
	 * A list of top-level entities in this hierarchy.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Entity[]
	 */
	protected $entities = array();

	/**
	 * Construct a hierarchy, optionally with a top-level entity.
	 *
	 * @since 1.0.0
	 *        
	 * @param WordPoints_Entity|null $entity An entity.
	 */
	public function __construct( WordPoints_Entity $entity = null ) {
		if ( isset( $entity ) ) {
			$this->add_entity( $entity );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_entities() {
		return $this->entities;
	}

	/**
	 * @since 1.0.0
	 */
	public function add_entity( WordPoints_Entity $entity ) {

		$this->entities[ $entity->get_slug() ] = $entity;
	}

	/**
	 * @since 1.0.0
	 */
	public function remove_entity( $slug ) {
		unset( $this->entities[ $slug ], $this->current );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_from_hierarchy( array $hierarchy ) {

		$backup = $this->hierarchy;
		$current = $this->current;

		$this->hierarchy = array();
		$this->current = null;

		$slug = reset( $hierarchy );

		while ( $slug ) {
			$this->descend( $slug );
			$slug = next( $hierarchy );
		}

		$entityish = $this->current;

		$this->hierarchy = $backup;
		$this->current = $current;

		return $entityish;
	}

	/**
	 * @since 1.0.0
	 */
	public function descend( $child_slug ) {

		if ( ! $this->current ) {

			if ( ! isset( $this->entities[ $child_slug ] ) ) {
				return false;
			}

			$child = $this->entities[ $child_slug ];

		} else {

			if ( ! ( $this->current instanceof WordPoints_Entity_ParentI ) ) {
				return false;
			}

			$child = $this->current->get_child( $child_slug );

			if ( ! $child ) {
				return false;
			}
		}

		$this->hierarchy[] = $this->current;

		$this->current = $child;

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function ascend() {
		$this->current = array_pop( $this->hierarchy );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_current() {
		return $this->current;
	}
}

// EOF
