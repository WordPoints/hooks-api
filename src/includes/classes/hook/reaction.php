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
	 * The slug of the reactor this reaction is for.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $reactor_slug;

	/**
	 * Whether this reaction is network-wide.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $network_wide;

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

		$this->ID           = wordpoints_int( $id );
		$this->storage      = $storage;
		$this->reactor_slug = $this->storage->get_reactor_slug();
		$this->network_wide = $this->storage->is_network_wide();
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
		return $this->reactor_slug;
	}

	/**
	 * @since 1.0.0
	 */
	public function is_network_wide() {
		return $this->network_wide;
	}
}

// EOF
