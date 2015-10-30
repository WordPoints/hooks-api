<?php

/**
 * Hook settings interface.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Implemented by classes which handle custom hook reaction settings.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_SettingsI {

	/**
	 * Validates the related settings for a hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array                              $settings   The settings for a hook reaction.
	 * @param WordPoints_Hook_Reaction_Validator $validator  The validator.
	 * @param WordPoints_Hook_Event_Args         $event_args The event args.
	 *
	 * @return array The validated settings.
	 */
	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	);

	/**
	 * Update the related settings for a hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_ReactionI $reaction The reaction object.
	 * @param array                     $settings The settings for a hook reaction.
	 */
	public function update_settings(
		WordPoints_Hook_ReactionI $reaction,
		array $settings
	);
}

// EOF
