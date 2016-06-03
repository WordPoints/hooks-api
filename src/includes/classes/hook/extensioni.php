<?php

/**
 * Hook extension interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Defines the API for a hook extension.
 *
 * Hook extensions extend the basic hooks API, and can modify whether a particular
 * hook firing should hit the target. Each extension makes this decision based on
 * custom settings it offers for each reaction.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_ExtensionI extends WordPoints_Hook_SettingsI {

	/**
	 * Get the slug of this extension.
	 *
	 * @since 1.0.0
	 *
	 * @return string The extension's slug.
	 */
	public function get_slug();

	/**
	 * Check whether this hook firing should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 *
	 * @return bool Whether the target should be hit by this hook firing.
	 */
	public function should_hit( WordPoints_Hook_Fire $fire );

	/**
	 * Get the data the scripts need for the UI.
	 *
	 * @since 1.0.0
	 *
	 * @return array Any data that needs to be present for the scripts in the UI.
	 */
	public function get_ui_script_data();
}

// EOF
