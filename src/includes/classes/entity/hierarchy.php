<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/*

# Problem: how to store condition settings.

This problem arises because args have a hierarchy, and conditions can be added
anywhere in that hierarchy. There are three places where this data needs to be
accessed:

- In the UI.
- When the settings are validated.
- When the event fires.

The UI is not our main concern, so let's consider the other things first. Validation
of the settings has two main parts:

1. Determining that the hierarchy is valid.
2. Checking that the settings for each condition are valid.

This is flexible and could probably work for almost any design.

The main issue then, is what would be most useful when it comes time for the event to
fire. At that time, all of the conditions have to be checked against the attributes
of the arg value provided by the action. The conditions can be on any of the
attributes, which may also have sub-attributes in the hierarchy of unlimited depth.

In the interest of performance, it would be best to execute as few database queries
as possible. Which means that all conditions on the directly accessible attributes
of the arg should be checked first. After that each of the other attributes should
have it's sub-attributes checked in like manner.

Of course, conditions are optional, and there may be some on one level and none on
another.

> 1 arg, many attributes and sub-attributes.

The best way to do this may be to separate the conditions on an arg's attributes from
any conditions on its sub-attributes.

Another thing to consider is the possibility that we will introduce conditions which
compare the values of two attributes/sub-attributes. It's probably not worth it to
optimize that.

And another thing that we have to get is the target.

We could try to optimize for time, but it would increase memory. Optimizing either
is really over-kill at this point. If we see an issue later on, we can always change
things.

*/

interface WordPoints_Entity_HierarchyI {

	/**
	 *
	 *
	 * @since 1.
	 * @return WordPoints_Entity[]
	 */
	public function get_entities();
	public function add_entity( WordPoints_Entity $entity );
	public function remove_entity( $slug );

	public function descend( $child_slug );
	public function ascend();

	/**
	 *
	 *
	 * @since 1.
	 * @return WordPoints_EntityishI
	 */
	public function get_current();

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @param array $hierarchy
	 *
	 * @return WordPoints_Entity
	 */
	public function get_from_hierarchy( array $hierarchy );
}

class WordPoints_Entity_Hierarchy implements WordPoints_Entity_HierarchyI {

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_EntityishI[]
	 */
	protected $hierarchy = array();

	/**
	 *
	 *
	 * @since 1.
	 *
	 * @var WordPoints_EntityishI
	 */
	protected $current;

	protected $entities = array();

	public function __construct( WordPoints_Entity $entity = null ) {
		if ( isset( $entity ) ) {
			$this->add_entity( $entity );
		}
	}

	public function get_entities() {
		return $this->entities;
	}
	public function add_entity( WordPoints_Entity $entity ) {

		$this->entities[ $entity->get_slug() ] = $entity;
	}

	public function remove_entity( $slug ) {
		unset( $this->entities[ $slug ], $this->current );
	}

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

	public function ascend() {
		$this->current = array_pop( $this->hierarchy );
	}

	public function get_current() {
		return $this->current;
	}
}

// EOF
