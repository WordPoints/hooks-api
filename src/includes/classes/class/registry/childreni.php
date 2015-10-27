<?php

/**
 * Class registry for children interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Defines a class registry for classes grouped together under common parents.
 *
 * Parent and child relationships are arbitrary, and not related to the actual
 * structure of the class hierarchy in the code.
 *
 * @since 1.0.0
 *
 * @see WordPoints_Class_RegistryI
 */
interface WordPoints_Class_Registry_ChildrenI {

	/**
	 * Get an object by its slug, or retrieve a list of all objects.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_slug The group slug.
	 * @param string $slug        The slug of the type of object to retrieve.
	 *
	 * @return object|object[]|false The object or all of the objects in the group.
	 *                               False on failure.
	 */
	public function get( $parent_slug = null, $slug = null );

	/**
	 * Register a type of object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_slug A unique identifier for this group of objects.
	 * @param string $slug        A unique identifier for this type of object.
	 * @param string $class       The class name.
	 * @param array  $args        Other arguments.
	 *
	 * @return bool Whether the class was registered successfully.
	 */
	public function register( $parent_slug, $slug, $class, array $args = array() );

	/**
	 * Deregister a type of object.
	 *
	 * If the $slug is omitted, all children of the parent will be deregistered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_slug The group slug.
	 * @param string $slug        The slug of the class to deregister.
	 */
	public function deregister( $parent_slug, $slug = null );

	/**
	 * Check if a type of object is registered by its slug.
	 *
	 * If the $slug is omitted, it will check if any children of the parent are de-
	 * registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_slug The group slug.
	 * @param string $slug        The slug of the class to check for.
	 *
	 * @return bool Whether the class is registered.
	 */
	public function is_registered( $parent_slug, $slug = null );
}

// EOF
