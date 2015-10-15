<?php

/**
 * Mock hook reaction storage class for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Mock hook reaction storage for the PHPUnit tests.
 *
 * @since 1.0.0
 */
class WordPoints_PHPUnit_Mock_Hook_Reaction_Storage extends WordPoints_Hook_Reaction_Storage {

	/**
	 * @since 1.0.0
	 */
	protected $reaction_class = 'WordPoints_PHPUnit_Mock_Hook_Reaction';

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
	 * @return array The index array.
	 */
	protected function get_reaction_index( $network_wide = false ) {

		return wordpoints_get_array_option(
			"wordpoints_{$this->reactor_slug}_mock_hook_reaction_index"
			, $network_wide ? 'site' : 'default'
		);
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
}

// EOF
