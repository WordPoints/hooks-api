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
		return (bool) $this->get_option( $this->get_settings_option_name( $id ) );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions() {
		return $this->create_reaction_objects( $this->get_reaction_index() );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactions_to_event( $event_slug ) {

		$index = $this->get_reaction_index();
		$index = wp_list_filter( $index, array( 'event' => $event_slug ) );
		return $this->create_reaction_objects( $index );
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
	 * @return array[] The index array.
	 */
	protected function get_reaction_index() {

		$index = $this->get_option( $this->get_reaction_index_option_name() );

		if ( ! is_array( $index ) ) {
			$index = array();
		}

		return $index;
	}

	/**
	 * Update the index of the reactions for this reactor.
	 *
	 * @since 1.0.0
	 *
	 * @param array[] $index The index {@see self::get_reaction_index()}.
	 *
	 * @return bool Whether the index was updated successfully.
	 */
	protected function update_reaction_index( $index ) {

		return $this->update_option(
			$this->get_reaction_index_option_name()
			, $index
		);
	}

	/**
	 * Converts an index into reaction objects.
	 *
	 * @since 1.0.0
	 *
	 * @param array $index A hook index {@see self::get_reaction_index()}.
	 *
	 * @return WordPoints_Hook_Reaction_Options[] The objects for the reactions.
	 */
	protected function create_reaction_objects( $index ) {

		$reactions = array();

		foreach ( $index as $reaction ) {

			$object = $this->get_reaction( $reaction['id'] );

			if ( ! $object ) {
				continue;
			}

			$reactions[] = $object;
		}

		return $reactions;
	}

	/**
	 * @since 1.0.0
	 */
	public function delete_reaction( $id ) {

		if ( ! $this->reaction_exists( $id ) ) {
			return false;
		}

		$result = $this->delete_option( $this->get_settings_option_name( $id ) );

		if ( ! $result ) {
			return false;
		}

		$index = $this->get_reaction_index();

		$index = wp_list_filter( $index, array( 'id' => $id ), 'NOT' );

		return $this->update_reaction_index( $index );
	}

	/**
	 * @since 1.0.0
	 */
	protected function _create_reaction( $event_slug ) {

		$index = $this->get_reaction_index();

		$id = 1;

		// TODO this is fragile when the newest reaction gets deleted.
		if ( ! empty( $index ) ) {
			$id = 1 + max( wp_list_pluck( $index, 'id' ) );
		}

		$option = $this->get_settings_option_name( $id );

		$settings = array( 'event' => $event_slug, 'reactor' => $this->reactor_slug );


		$result = $this->add_option( $option, $settings );

		if ( ! $result ) {
			return false;
		}

		$index[] = array( 'event' => $event_slug, 'id' => $id );

		if ( ! $this->update_reaction_index( $index ) ) {
			return false;
		}

		return $id;
	}

	/**
	 * Get an option.
	 *
	 * This is public so that the reaction object can access it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The option name.
	 *
	 * @return mixed The option value, or false.
	 */
	public function get_option( $name ) {
		return get_option( $name );
	}

	/**
	 * Add an option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  The name of the option.
	 * @param mixed  $value The option value.
	 *
	 * @return bool Whether the option was added successfully.
	 */
	protected function add_option( $name, $value ) {
		return add_option( $name, $value );
	}

	/**
	 * Update an option.
	 *
	 * This is public so that the reaction object can access it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name  The option name.
	 * @param mixed  $value The option value.
	 *
	 * @return bool Whether the option was updated successfully.
	 */
	public function update_option( $name, $value ) {
		return update_option( $name, $value );
	}

	/**
	 * Delete an option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The option name.
	 *
	 * @return bool Whether the option was deleted successfully.
	 */
	protected function delete_option( $name ) {
		return delete_option( $name );
	}

	/**
	 * Get the name of the option where the reaction's settings are stored.
	 *
	 * This is public so that the reaction object can access it.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the option where the settings are stored.
	 */
	public function get_settings_option_name( $id ) {
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
