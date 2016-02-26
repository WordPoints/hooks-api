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

		if ( ! is_array( $settings[ $this->slug ] ) ) {

			$validator->add_error(
				__( 'Invalid settings format.', 'wordpoints' )
				, $this->slug
			);

			return $settings;
		}

		$this->validator = $validator;
		$this->event_args = $event_args;

		$this->validator->push_field( $this->slug );

		foreach ( $settings[ $this->slug ] as $firer => $firer_settings ) {

			if ( ! wordpoints_hooks()->firers->is_registered( $firer ) ) {
				$this->validator->add_error( __( 'Unknown hook firer.', 'wordpoints' ), $firer );
				continue;
			}

			$this->validator->push_field( $firer );

			$settings[ $this->slug ][ $firer ] = $this->validate_firer_settings(
				$firer_settings
			);

			$this->validator->pop_field();
		}

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
	 * Validate the settings for this extension for a particular firer.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $settings The settings for a particular firer.
	 *
	 * @return mixed The validated settings.
	 */
	protected function validate_firer_settings( $settings ) {}

	/**
	 * Get the extension settings from the fire object.
	 *
	 * By default the settings are stored per firer, so we offer this helper method
	 * to get the settings that should be used based on the firer from the fire
	 * object.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 *
	 * @return mixed The settings for the extension, or false if none.
	 */
	protected function get_settings_from_fire( WordPoints_Hook_Fire $fire ) {

		$firer_slug = $fire->firer->get_slug();
		$settings   = $fire->reaction->get_meta( $this->slug );

		if ( ! is_array( $settings ) ) {
			return $settings;
		}

		if ( isset( $settings[ $firer_slug ] ) ) {
			return $settings[ $firer_slug ];
		} else {
			return false;
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
