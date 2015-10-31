<?php

/**
 * Entityish interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Defines the API for entity-like objects.
 *
 * Some parts of the API are common to both entities and their children (attributes
 * and relationships). This interface outlines that basic API that is common to all
 * entity-like objects.
 *
 * @since 1.0.0
 */
interface WordPoints_EntityishI {

	/**
	 * Get the slug of this entity/entity-child.
	 *
	 * @since 1.0.0
	 *
	 * @return string The entity/entity-child's slug.
	 */
	public function get_slug();

	/**
	 * Get the human-readable title of this entity/entity-child.
	 *
	 * @since 1.0.0
	 *
	 * @return string The title of this entity/entity-child.
	 */
	public function get_title();

	/**
	 * Check whether a user can view an entity/entity child.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $user_id The user ID.
	 * @param mixed $id      The entity/entity-child ID.
	 *
	 * @return bool Whether the user can view this entity/entity-child.
	 */
	public function user_can_view( $user_id, $id );

	/**
	 * Get the value of this entity/entity-child.
	 *
	 * The objects that use this interface can be used to represent an entity/entity-
	 * child generically, or a particular entity/entity-child specifically. For
	 * example, it could represent a Post entity generically, but it can also
	 * represent a particular Post specifically. This function is used to get the
	 * specific value.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed The value of this entity/entity-child.
	 */
	public function get_the_value();

	/**
	 * Set the value of this entity/entity-child.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value The value of this entity/entity-child.
	 */
	public function set_the_value( $value );
}

// EOF