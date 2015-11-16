<?php

/**
 * Mock hook reaction class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook reaction for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Reaction extends WordPoints_Hook_Reaction {

	/**
	 * @since 1.0.0
	 */
	public function get_event_slug() {
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

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $this->reactor_slug );

		if ( ! $reactor ) {
			return false;
		}

		return $reactor->reactions->get_reaction_settings(
			$this->ID
			, $this->network_wide
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

		/** @var WordPoints_Hook_Reactor $reactor */
		$reactor = wordpoints_hooks()->reactors->get( $this->reactor_slug );

		if ( ! $reactor ) {
			return false;
		}

		return $reactor->reactions->update_reaction_settings(
			$this->ID
			, $this->network_wide
			, $settings
		);
	}
}

// EOF
