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
	 * The reactions.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	protected $reactions = array();

	/**
	 * The network-wide reactions.
	 *
	 * @since 1.0.0
	 *
	 * @var array[]
	 */
	protected $network_reactions = array();

	/**
	 * Incremented to determine the ID of each mock reaction object.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected static $ids = 0;

	/**
	 * Get the ID for the next mock reaction
	 *
	 * @since 1.0.0
	 *
	 * @return int The next ID.
	 */
	protected function increment_id() {
		return ++self::$ids;
	}

	/**
	 * @since 1.0.0
	 */
	public function reaction_exists( $id ) {

		$reactions = $this->_get_reactions( wordpoints_hooks()->get_network_mode() );

		return isset( $reactions[ $id ] );
	}

	/**
	 * Get the raw settings for a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int  $id           The reaction ID.
	 * @param bool $network_wide Whether the reaction is network-wide.
	 *
	 * @return array|false The reaction settings, or false.
	 */
	public function get_reaction_settings( $id, $network_wide ) {

		$reactions = $this->_get_reactions( $network_wide );

		if ( ! isset( $reactions[ $id ] ) ) {
			return false;
		}

		return $reactions[ $id ];
	}

	/**
	 * Update the settings for a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id           The reaction ID.
	 * @param bool  $network_wide Whether the reaction is network-wide.
	 * @param array $settings     The new settings for the reaction.
	 *
	 * @return bool Whether the settings were updated successfully.
	 */
	public function update_reaction_settings( $id, $network_wide, $settings ) {

		$reactions = $this->_get_reactions( $network_wide );

		if ( ! isset( $reactions[ $id ] ) ) {
			return false;
		}

		$reactions[ $id ] = $settings;

		return $this->_update_reactions( $reactions, $network_wide );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions() {

		$network_wide = $this->hooks->get_network_mode();

		return $this->create_reaction_objects(
			$this->_get_reactions( $network_wide )
			, $network_wide
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions_to_event( $event_slug ) {

		$reactions = $this->_get_reactions_to_event( $event_slug, false );

		if ( is_wordpoints_network_active() ) {
			$reactions = array_merge(
				$reactions
				, $this->_get_reactions_to_event( $event_slug, true )
			);
		}

		return $reactions;
	}

	/**
	 * @since 1.0.0
	 */
	public function delete_reaction( $id ) {

		$network_wide = $this->hooks->get_network_mode();

		$reactions = $this->_get_reactions( $network_wide );

		if ( ! isset( $reactions[ $id ] ) ) {
			return false;
		}

		unset( $reactions[ $id ] );

		return $this->_update_reactions( $reactions, $network_wide );
	}

	/**
	 * Get the reactions for this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide Whether to retrieve the network-wide or standard
	 *                           reactions.
	 *
	 * @return array[] The reactions.
	 */
	protected function _get_reactions( $network_wide ) {

		return ( $network_wide ) ? $this->network_reactions : $this->reactions;
	}

	/**
	 * Converts an array of reactions into reaction objects.
	 *
	 * @since 1.0.0
	 *
	 * @param array[] $reactions    An array of reactions.
	 * @param bool    $network_wide Whether these are network-wide reactions.
	 *
	 * @return WordPoints_PHPUnit_Mock_Hook_Reaction[] The objects for the reactions.
	 */
	protected function create_reaction_objects( $reactions, $network_wide ) {

		$objects = array();

		foreach ( $reactions as $id => $reaction ) {

			$objects[] = new $this->reaction_class(
				$id
				, $this->reactor_slug
				, $network_wide
			);
		}

		return $objects;
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
	protected function _get_reactions_to_event( $event_slug, $network_wide ) {

		$reactions = $this->_get_reactions( $network_wide );
		$reactions = wp_list_filter( $reactions, array( 'event' => $event_slug ) );
		return $this->create_reaction_objects( $reactions, $network_wide );
	}

	/**
	 * @since 1.0.0
	 */
	protected function _create_reaction( $event_slug ) {

		$network_wide = $this->hooks->get_network_mode();

		$reactions = $this->_get_reactions( $network_wide );

		$id = $this->increment_id();

		$reactions[ $id ] = array(
			'event' => $event_slug,
			'reactor' => $this->reactor_slug,
		);

		if ( ! $this->_update_reactions( $reactions, $network_wide ) ) {
			return false;
		}

		return $id;
	}

	/**
	 * Update the list of reactions
	 *
	 * @since 1.0.0
	 *
	 * @param array[] $reactions    The reactions.
	 * @param bool    $network_wide Whether these reactions are network-wide.
	 *
	 * @return bool Whether the list of reactions was updated successfully.
	 */
	protected function _update_reactions( $reactions, $network_wide ) {

		if ( $network_wide ) {
			$this->network_reactions = $reactions;
		} else {
			$this->reactions = $reactions;
		}

		return true;
	}
}

// EOF
