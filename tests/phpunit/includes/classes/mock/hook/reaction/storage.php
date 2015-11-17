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
		return isset( $this->reactions[ $id ] );
	}

	/**
	 * Get the raw settings for a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id The reaction ID.
	 *
	 * @return array|false The reaction settings, or false.
	 */
	public function get_reaction_settings( $id ) {

		if ( ! isset( $this->reactions[ $id ] ) ) {
			return false;
		}

		return $this->reactions[ $id ];
	}

	/**
	 * Update the settings for a reaction.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $id       The reaction ID.
	 * @param array $settings The new settings for the reaction.
	 *
	 * @return bool Whether the settings were updated successfully.
	 */
	public function update_reaction_settings( $id, $settings ) {

		if ( ! isset( $this->reactions[ $id ] ) ) {
			return false;
		}

		$this->reactions[ $id ] = $settings;

		return true;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions() {
		return $this->create_reaction_objects( $this->reactions );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions_to_event( $event_slug ) {

		return $this->create_reaction_objects(
			wp_list_filter( $this->reactions, array( 'event' => $event_slug ) )
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function delete_reaction( $id ) {

		if ( ! isset( $this->reactions[ $id ] ) ) {
			return false;
		}

		unset( $this->reactions[ $id ] );

		return true;
	}

	/**
	 * Converts an array of reactions into reaction objects.
	 *
	 * @since 1.0.0
	 *
	 * @param array[] $reactions An array of reactions.
	 *
	 * @return WordPoints_PHPUnit_Mock_Hook_Reaction[] The objects for the reactions.
	 */
	protected function create_reaction_objects( $reactions ) {

		$objects = array();

		foreach ( $reactions as $id => $reaction ) {

			$object = $this->get_reaction( $id );

			if ( ! $object ) {
				continue;
			}

			$objects[] = $object;
		}

		return $objects;
	}

	/**
	 * @since 1.0.0
	 */
	protected function _create_reaction( $event_slug ) {

		$id = $this->increment_id();

		$this->reactions[ $id ] = array( 'event' => $event_slug );

		return $id;
	}
}

// EOF
