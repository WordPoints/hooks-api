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
	 * @param int                               $id      The ID of a hook reaction.
	 * @param WordPoints_Hook_Reaction_StorageI $storage The storage object.
	 */
	public function __construct( $id, WordPoints_Hook_Reaction_StorageI $storage );

	/**
	 * Get a Globally Unique ID for this reaction.
	 *
	 * The GUID uniquely identifies this reaction, differentiating from any other
	 * reaction on this multi-network. It is composed of the reaction 'id', the
	 * 'reactor' slug, the reaction 'group', and the reaction 'context_id'.
	 *
	 * @since 1.0.0
	 *
	 * @return array The GUID for this reaction.
	 */
	public function get_guid();

	/**
	 * Check whether this reaction is network-wide.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether this reaction is network-wide.
	 */
	public function get_context_id();

	/**
	 * Get the slug of the event this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @return string The event slug.
	 */
	public function get_event_slug();

	/**
	 * Update the event this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug The event slug.
	 *
	 * @return bool Whether the event was updated successfully.
	 */
	public function update_event_slug( $event_slug );

	/**
	 * Get the slug of the reactor this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @return string The reactor slug.
	 */
	public function get_reactor_slug();

	/**
	 * Get the slug of the reaction storage group this reaction belongs to.
	 *
	 * Each reactor can store reactions in multiple different groups. For example,
	 * there are 'standard' and 'network' reactions. This method returns the slug of
	 * the group which this reaction is from.
	 *
	 * @since 1.0.0
	 *
	 * @return string The storage group slug.
	 */
	public function get_storage_group_slug();

	/**
	 * Get a piece of metadata for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The meta key.
	 *
	 * @return mixed|false The meta value, or false if not found.
	 */
	public function get_meta( $key );

	/**
	 * Add a piece of metadata for this reaction.
	 *
	 * If this meta key already exists, the value will not be changed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   The meta key.
	 * @param mixed  $value The value.
	 *
	 * @return bool Whether the metadata was added successfully.
	 */
	public function add_meta( $key, $value );

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
