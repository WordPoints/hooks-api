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
	 */
	public function create( $event_slug ) {

		$index_option = "wordpoints_{$this->reactor_slug}_hook_reaction_index";

		$index = wordpoints_get_array_option(
			$index_option
			, $this->network_wide ? 'site' : 'default'
		);

		$id = 1;

		// TODO this is fragile when the newest reaction gets deleted.
		if ( ! empty( $index ) ) {
			$id = 1 + max( wp_list_pluck( $index, 'id' ) );
		}

		$this->ID = $id;

		$option = "wordpoints_{$this->reactor_slug}_hook_reaction-{$this->ID}";

		$settings = array( 'event' => $event_slug, 'reactor' => $this->reactor_slug );

		if ( $this->network_wide ) {
			$result = add_site_option( $option, $settings );
		} else {
			$result = add_option( $option, $settings );
		}

		if ( ! $result ) {
			return false;
		}

		$index[] = array( 'event' => $event_slug, 'id' => $this->ID );

		if ( $this->network_wide ) {
			return update_site_option( $index_option, $index );
		} else {
			return update_option( $index_option, $index );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function exists() {
		return ! ( ! isset( $this->ID ) || false === $this->get_settings() );
	}

	/**
	 * @since 1.0.0
	 */
	public function delete() {

		$option = "wordpoints_{$this->reactor_slug}_hook_reaction-{$this->ID}";

		if ( $this->network_wide ) {
			return delete_site_option( $option );
		} else {
			return delete_option( $option );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_event_slug() { // TODO is this even needed.
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

		$option = "wordpoints_{$this->reactor_slug}_hook_reaction-{$this->ID}";

		if ( $this->network_wide ) {
			$settings = get_site_option( $option );
		} else {
			$settings = get_option( $option );
		}

		return $settings;
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

		$option = "wordpoints_{$this->reactor_slug}_hook_reaction-{$this->ID}";

		if ( $this->network_wide ) {
			return update_site_option( $option, $settings );
		} else {
			return update_option( $option, $settings );
		}
	}
}

// EOF
