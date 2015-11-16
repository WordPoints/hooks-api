<?php

/**
 * Class for option table hook reaction storage method.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Stores hook reaction settings in options.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Reaction_Storage_Options extends WordPoints_Hook_Reaction_Storage {

	/**
	 * @since 1.0.0
	 */
	protected $reaction_class = 'WordPoints_Hook_Reaction_Options';

	/**
	 * @since 1.0.0
	 */
	public function reaction_exists( $id ) {

		if ( wordpoints_hooks()->get_network_mode() ) {
			return (bool) get_site_option( $this->get_settings_option_name( $id ) );
		} else {
			return (bool) get_option( $this->get_settings_option_name( $id ) );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions() {

		$network_mode = $this->hooks->get_network_mode();

		return $this->create_reaction_objects(
			$this->get_reaction_index( $network_mode )
			, $network_mode
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions_to_event( $event_slug ) {

		$reactions = $this->_get_reactions_to_event( $event_slug );

		if ( is_wordpoints_network_active() ) {
			$reactions = array_merge(
				$reactions
				, $this->_get_reactions_to_event( $event_slug, true )
			);
		}

		return $reactions;
	}

	/**
	 * Get an index of the reaction for this reactor.
	 *
	 * The index is stored as an array of the following format:
	 *
	 * array(
	 *    array( 'event' => 'post_publish',  'id' => 1  ),
	 *    array( 'event' => 'user_register', 'id' => 23 ),
	 * );
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide Whether to retrieve the index for network-wide or
	 *                           standard reactions.
	 *
	 * @return array[] The index array.
	 */
	protected function get_reaction_index( $network_wide = false ) {

		return wordpoints_get_array_option(
			$this->get_reaction_index_option_name()
			, $network_wide ? 'site' : 'default'
		);
	}

	/**
	 * Update the index of the reactions for this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param array[] $index        The index {@see self::get_reaction_index()}.
	 * @param bool    $network_wide Whether to update the index for network-wide or
	 *                              standard reactions.
	 *
	 * @return bool Whether the index was updated successfully.
	 */
	protected function update_reaction_index( $index, $network_wide = false ) {

		$index_option = $this->get_reaction_index_option_name();

		if ( $network_wide ) {
			return update_site_option( $index_option, $index );
		} else {
			return update_option( $index_option, $index );
		}
	}

	/**
	 * Converts an index into reaction objects.
	 *
	 * @since 1.0.0
	 *
	 * @param array $index        A hook index {@see self::get_reaction_index()}.
	 * @param bool  $network_wide Whether the index is for network-wide reactions.
	 *
	 * @return WordPoints_Hook_Reaction_Options[] The objects for the reactions.
	 */
	protected function create_reaction_objects( $index, $network_wide = false ) {

		$reactions = array();

		foreach ( $index as $reaction ) {

			$reactions[] = new $this->reaction_class(
				$reaction['id']
				, $this->reactor_slug
				, $network_wide
			);
		}

		return $reactions;
	}

	/**
	 * Get the reactions to a specific event for this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $event_slug   The slug of the event to get the reactions for.
	 * @param bool   $network_wide Whether to get network-wide or standard reactions.
	 *
	 * @return WordPoints_Hook_Reaction_Options[] The reaction objects.
	 */
	protected function _get_reactions_to_event( $event_slug, $network_wide = false ) {

		$index = $this->get_reaction_index( $network_wide );
		$index = wp_list_filter( $index, array( 'event' => $event_slug ) );
		return $this->create_reaction_objects( $index, $network_wide );
	}

	/**
	 * @since 1.0.0
	 */
	public function delete_reaction( $id ) {

		if ( ! $this->reaction_exists( $id ) ) {
			return false;
		}

		$option = $this->get_settings_option_name( $id );

		$network_wide = $this->hooks->get_network_mode();

		if ( $network_wide ) {
			$result = delete_site_option( $option );
		} else {
			$result = delete_option( $option );
		}

		if ( ! $result ) {
			return false;
		}

		$index = $this->get_reaction_index( $network_wide );

		$index = wp_list_filter( $index, array( 'id' => $id ), 'NOT' );

		return $this->update_reaction_index( $index, $network_wide );
	}

	/**
	 * @since 1.0.0
	 */
	protected function _create_reaction( $event_slug ) {

		$network_wide = $this->hooks->get_network_mode();

		$index = $this->get_reaction_index( $network_wide );

		$id = 1;

		// TODO this is fragile when the newest reaction gets deleted.
		if ( ! empty( $index ) ) {
			$id = 1 + max( wp_list_pluck( $index, 'id' ) );
		}

		$option = $this->get_settings_option_name( $id );

		$settings = array( 'event' => $event_slug, 'reactor' => $this->reactor_slug );

		if ( $network_wide ) {
			$result = add_site_option( $option, $settings );
		} else {
			$result = add_option( $option, $settings );
		}

		if ( ! $result ) {
			return false;
		}

		$index[] = array( 'event' => $event_slug, 'id' => $id );

		if ( ! $this->update_reaction_index( $index, $network_wide ) ) {
			return false;
		}

		return $id;
	}

	/**
	 * Get the name of the option where the reaction's settings are stored.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the option where the settings are stored.
	 */
	protected function get_settings_option_name( $id ) {
		return "wordpoints_{$this->reactor_slug}_hook_reaction-{$id}";
	}

	/**
	 * Get the name of the option where the reaction index is stored.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the option where the reaction index is stored.
	 */
	protected function get_reaction_index_option_name() {
		return "wordpoints_{$this->reactor_slug}_hook_reaction_index";
	}
}

// EOF
