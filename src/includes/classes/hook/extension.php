<?php

/**
 * Hook extension class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a hook extension.
 *
 * Hook extensions extend the basic hooks API, and can modify whether a particular
 * hook firing should hit the target. Each extension makes this decision based on
 * custom settings it offers for each reaction.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Hook_Extension implements WordPoints_Hook_SettingsI {

	/**
	 * The unique slug for identifying this extension.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The validator for the current reaction.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_Validator
	 */
	protected $validator;

	/**
	 * The args for the current event.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Event_Args
	 */
	protected $event_args;

	/**
	 * Get the slug of this extension.
	 *
	 * @since 1.0.0
	 *
	 * @return string The extension's slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * @since 1.0.0
	 */
	public function validate_settings(
		array $settings,
		WordPoints_Hook_Reaction_Validator $validator,
		WordPoints_Hook_Event_Args $event_args
	) {

		if ( ! isset( $settings[ $this->slug ] ) ) {
			return $settings;
		}

		$this->validator = $validator;
		$this->event_args = $event_args;

		$this->validator->push_field( $this->slug );
		$settings[ $this->slug ] = $this->{"validate_{$this->slug}"}( $settings[ $this->slug ] );
		$this->validator->pop_field();

		return $settings;
	}

	/**
	 * @since 1.0.0
	 */
	public function update_settings( WordPoints_Hook_ReactionI $reaction, array $settings ) {

		if ( isset( $settings[ $this->slug ] ) ) {
			$reaction->update_meta( $this->slug, $settings[ $this->slug ] );
		} else {
			$reaction->delete_meta( $this->slug );
		}
	}

	/**
	 * Check whether this hook firing should hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 *
	 * @return bool Whether the target should be hit by this hook firing.
	 */
	abstract public function should_hit( WordPoints_Hook_Fire $fire );

	/**
	 * After a reaction has hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 */
	public function after_hit( WordPoints_Hook_Fire $fire ) {}

	/**
	 * Called after a reverse action is called.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The reverse fire object.
	 */
	public function after_reverse( WordPoints_Hook_Fire $fire ) {}

	/**
	 * Get the data the scripts need for the UI.
	 *
	 * @since 1.0.0
	 *
	 * @return array Any data that needs to be present for the scripts in the UI.
	 */
	public function get_ui_script_data() {
		return array();
	}
}

// EOF
