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
	public function __construct( $id, $reactor_slug, $network_wide, $storage = null ) {

		if ( is_a( $id, __CLASS__ ) ) {
			$id = $id->ID;
		}

		$id = wordpoints_int( $id );

		if ( $id ) {
			$this->ID = $id;
		}

		if ( $reactor_slug ) {
			$this->reactor_slug = $reactor_slug;
		}

		$this->network_wide = (bool) $network_wide;

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
		return $this->reactor_slug;
	}
}

// EOF
