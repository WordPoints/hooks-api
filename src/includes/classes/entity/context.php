<?php

/**
 * Entity context base class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Bootstrap for representing an entity context.
 *
 * The entity API makes it possible to encapsulate different sorts of things (like
 * posts and users) so that their data can be accessed through a common set of
 * interfaces. Each entity has an ID—a single piece of data (like an integer or
 * string) that can be used to identify that entity. The ID is unique to that entity,
 * but it is not *globally unique*. That is, it is unique to that entity only within
 * a particular scope or "context". For example, on multisite a post's ID is unique
 * only in the context of the site on which that post was published. That same ID
 * would refer to a different post on each other site on the network. The job of the
 * context API is to encapsulate the different sorts of contexts in which entities
 * can exist. This allows us to, for example, create globally unique IDs (GUIDs) for
 * entities, so that they can be identified specifically even outside of their usual
 * context.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Entity_Context {

	/**
	 * The slug of this context.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * @since 1.0.0
	 *
	 * @param string $slug The slug of this context.
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Get the slug of this context.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Get the current ID of this context.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string|false The ID or slug of the context, or false if not
	 *                          currently in this context.
	 */
	abstract public function get_current_id();
}

// EOF
