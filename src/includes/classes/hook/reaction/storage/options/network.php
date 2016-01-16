<?php

/**
 * Class for network option table hook reaction storage method.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Stores hook reaction settings in network options.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Reaction_Storage_Options_Network extends WordPoints_Hook_Reaction_Storage_Options {

	/**
	 * @since 1.0.0
	 */
	protected $context = array( 'network' );

	/**
	 * @since 1.0.0
	 */
	protected $reaction_class = 'WordPoints_Hook_Reaction_Options';

	/**
	 * @since 1.0.0
	 */
	public function get_option( $name ) {
		return get_site_option( $name );
	}

	/**
	 * @since 1.0.0
	 */
	protected function add_option( $name, $value ) {
		return add_site_option( $name, $value );
	}

	/**
	 * @since 1.0.0
	 */
	public function update_option( $name, $value ) {
		return update_site_option( $name, $value );
	}

	/**
	 * @since 1.0.0
	 */
	protected function delete_option( $name ) {
		return delete_site_option( $name );
	}
}

// EOF
