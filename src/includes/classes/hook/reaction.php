<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

abstract class WordPoints_Hook_Reaction implements WordPoints_Hook_ReactionI {

	/**
	 * @since 1.0.0
	 */
	protected $ID;

	protected $reactor_slug;

	protected $network_wide = false;

	//
	// Public Methods.
	//

	/**
	 * @since 1.0.0
	 */
	public function __construct( $id, $reactor_slug, $network_wide = false ) {

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

	public function get_reactor_slug() {
		return $this->reactor_slug;
	}
}

// EOF
