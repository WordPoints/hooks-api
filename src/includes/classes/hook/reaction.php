<?php

/**
 * Hook reaction class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Bootstrap for representing a hook reaction.
 *
 * @since 1.0.0
 */
abstract class WordPoints_Hook_Reaction implements WordPoints_Hook_ReactionI {

	/**
	 * @since 1.0.0
	 */
	protected $ID;

	/**
	 * The reaction storage object.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_Hook_Reaction_StorageI
	 */
	protected $storage;

	//
	// Public Methods.
	//

	/**
	 * @since 1.0.0
	 */
	public function __construct( $id, WordPoints_Hook_Reaction_StorageI $storage ) {

		$this->ID      = wordpoints_int( $id );
		$this->storage = $storage;
	}

	/**
	 * @since 1.0.0
	 */
	public function __get( $var ) {

		if ( 'ID' === $var ) {
			return $this->ID;
		}

		return null;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactor_slug() {
		return $this->storage->get_reactor_slug();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_storage_group_slug() {
		return $this->storage->get_slug();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_context_id() {
		return $this->storage->get_context_id();
	}
}

// EOF
