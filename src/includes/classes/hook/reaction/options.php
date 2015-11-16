<?php

/**
 * Class for hook reactions whose settings are stored as options.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a hook reaction whose settings are stored using the options API.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Reaction_Options extends WordPoints_Hook_Reaction {

	/**
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_Storage_Options
	 */
	protected $storage;

	/**
	 * @since 1.0.0
	 */
	public function get_event_slug() {
	    // TODO is this even needed.
		return $this->get_meta( 'event' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_meta( $key ) {

		$settings = $this->get_settings();

		if ( ! is_array( $settings ) || ! isset( $settings[ $key ] ) ) {
			return null;
		}

		return $settings[ $key ];
	}

	/**
	 * @since 1.0.0
	 */
	public function add_meta( $key, $value ) {

		$settings = $this->get_settings();

		if ( ! is_array( $settings ) || isset( $settings[ $key ] ) ) {
			return false;
		}

		$settings[ $key ] = $value;

		return $this->update_settings( $settings );
	}

	/**
	 * @since 1.0.0
	 */
	public function update_meta( $key, $value ) {

		$settings = $this->get_settings();

		if ( ! is_array( $settings ) ) {
			return false;
		}

		$settings[ $key ] = $value;

		return $this->update_settings( $settings );
	}

	/**
	 * @since 1.0.0
	 */
	public function delete_meta( $key ) {

		$settings = $this->get_settings();

		if ( ! is_array( $settings ) || ! isset( $settings[ $key ] ) ) {
			return false;
		}

		unset( $settings[ $key ] );

		return $this->update_settings( $settings );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_all_meta() {
		return $this->get_settings();
	}

	/**
	 * Gets the settings for this reaction from the database.
	 *
	 * @since 1.0.0
	 *
	 * @return array|false The settings, or false if none.
	 */
	protected function get_settings() {

		return $this->storage->get_option(
			$this->storage->get_settings_option_name( $this->ID )
		);
	}

	/**
	 * Updates the settings for this reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings The settings for this reaction.
	 *
	 * @return bool Whether the settings were updated successfully.
	 */
	protected function update_settings( $settings ) {

		return $this->storage->update_option(
			$this->storage->get_settings_option_name( $this->ID )
			, $settings
		);
	}
}

// EOF
