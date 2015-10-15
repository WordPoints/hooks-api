<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */


interface WordPoints_Hook_SettingsI {

	/**
	 * Validates the related settings for a hook reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array                              $settings  The settings for a hook reaction.
	 * @param WordPoints_Hook_Reaction_Validator $validator The validator.
	 *
	 * @param WordPoints_Hook_Event_Args         $event_args
	 *
	 * @return array The validated settings.
	 */
	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	);

	public function update_settings(
		WordPoints_Hook_ReactionI $reaction,
		array $settings
	);
}

// EOF
