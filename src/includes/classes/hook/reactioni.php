<?php

/**
 * Hook reaction interface.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Defines the API for objects representing a hook reaction.
 *
 * This allows for a reaction to be manipulated regardless of how it's settings are
 * stored.
 *
 * @property-read int $ID The ID of the reaction.
 */
interface WordPoints_Hook_ReactionI {

	/**
	 * Construct the class for a hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int|WordPoints_Hook_ReactionI $id           The ID of a hook reaction.
	 * @param string                        $reactor_slug The slug of the reactor this
	 *                                                    reaction is for.
	 * @param bool                          $network_wide Whether this is a network-
	 *                                                    wide reaction.
	 */
	public function __construct( $id, $reactor_slug, $network_wide = false );

	/**
	 * Checks whether this reaction exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the reaction exists.
	 */
	public function exists();

	/**
	 * Create this reaction if it doesn't exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug The slug of the event that this is a reaction to.
	 *
	 * @return bool Whether the reaction was created successfully.
	 */
	public function create( $event_slug );

	/**
	 * Delete this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the reaction was deleted successfully.
	 */
	public function delete();

	/**
	 * Get the slug of the event this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @return string The event slug.
	 */
	public function get_event_slug();

	/**
	 * Get the slug of the reactor this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_reactor_slug();

	/**
	 * Get a piece of metadata for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The meta key.
	 *
	 * @return mixed The meta value.
	 */
	public function get_meta( $key );

	/**
	 * Update a piece of metadata for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   The meta key.
	 * @param mixed  $value The new value.
	 *
	 * @return bool Whether the metadata was updated successfully.
	 */
	public function update_meta( $key, $value );

	/**
	 * Delete a piece of metadata for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The meta key.
	 *
	 * @return bool Whether the metadata was deleted successfully.
	 */
	public function delete_meta( $key );

	/**
	 * Get all of the metadata for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @return array|false All metadata for this reaction, or false on failure.
	 */
	public function get_all_meta();
}

// EOF
