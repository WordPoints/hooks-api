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
	 * @param string $reactor_slug The slug of the reactor the reactions belong to.
	 */
	public function __construct( $reactor_slug );

	/**
	 * Get an reaction object.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The ID of an reaction.
	 *
	 * @return WordPoints_Hook_ReactionI The reaction object.
	 */
	public function get_reaction( $id );

	/**
	 * Create an reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The settings for the reaction.
	 *
	 * @return WordPoints_Hook_ReactionI|bool|WordPoints_Hook_Reaction_Validator True if the reaction was created
	 *                                        successfully. False or a validator
	 *                                        instance if not.
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
	 * @return bool|WordPoints_Hook_Reaction_Validator True if the reaction was updated
	 *                                        successfully. False or a validator
	 *                                        instance if not.
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
