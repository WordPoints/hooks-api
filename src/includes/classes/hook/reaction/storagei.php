<?php

/**
 * Hook reaction storage interface.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Interface for hook reaction storage methods.
 *
 * This allows hook reactions to be create/updated/deleted through a common interface
 * regardless of where the reaction data is stored.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Reaction_StorageI {

	/**
	 * Constructs the class with the reactor slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string                  $slug    The slug of this storage group.
	 * @param WordPoints_Hook_Reactor $reactor The reactor the reactions belong to.
	 */
	public function __construct( $slug, WordPoints_Hook_Reactor $reactor );

	/**
	 * Get the slug of this reaction storage group.
	 *
	 * This isn't the slug of the storage method itself, but the identifier for the
	 * group of reactions a particular object happens to be storing.
	 *
	 * @since 1.0.0
	 *
	 * @return string The slug of this storage group.
	 */
	public function get_slug();

	/**
	 * Get the slug of the reactor this object stores reactions for.
	 *
	 * @since 1.0.0
	 *
	 * @return string The reactor slug.
	 */
	public function get_reactor_slug();

	/**
	 * Get the ID of the current context in which reactions are being stored.
	 *
	 * @since 1.0.0
	 *
	 * @see wordpoints_entities_get_current_context_id()
	 *
	 * @return array|false The ID of the context in which this method is currently
	 *                     storing reactions, or false if out of context.
	 */
	public function get_context_id();

	/**
	 * Check whether a reaction exists.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The reaction ID.
	 *
	 * @return bool Whether the reaction exists.
	 */
	public function reaction_exists( $id );

	/**
	 * Get an reaction object.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The ID of an reaction.
	 *
	 * @return WordPoints_Hook_ReactionI|false The reaction, or false if nonexistent.
	 */
	public function get_reaction( $id );

	/**
	 * Create an reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The settings for the reaction.
	 *
	 * @return WordPoints_Hook_ReactionI|false|WordPoints_Hook_Reaction_Validator
	 *         The reaction object if created successfully. False or a validator
	 *         instance if not.
	 */
	public function create_reaction( array $settings );

	/**
	 * Update an reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id       The ID of the reaction to update.
	 * @param array $settings The settings for the reaction.
	 *
	 * @return WordPoints_Hook_ReactionI|false|WordPoints_Hook_Reaction_Validator
	 *         The reaction object if updated successfully. False or a validator
	 *         instance if not.
	 */
	public function update_reaction( $id, array $settings );

	/**
	 * Delete an reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The ID of the reaction.
	 *
	 * @return bool Whether the reaction was deleted successfully.
	 */
	public function delete_reaction( $id );

	/**
	 * Get all hook reactions for the reactor.
	 *
	 * Only standard or network-wide reactions should be returned, depending on
	 * whether network mode is on or off.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Hook_ReactionI[]
	 */
	public function get_reactions();

	/**
	 * Get all hook reactions to a given event for the reactor.
	 *
	 * Both standard and network-wide reactions should be returned.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Hook_ReactionI[]
	 */
	public function get_reactions_to_event( $event_slug );
}

// EOF
