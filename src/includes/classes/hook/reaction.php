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
	 * @var WordPoints_Hook_Reaction_StoreI
	 */
	protected $store;

	//
	// Public Methods.
	//

	/**
	 * @since 1.0.0
	 */
	public function __construct( $id, WordPoints_Hook_Reaction_StoreI $store ) {

		$this->ID    = wordpoints_int( $id );
		$this->store = $store;
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
	public function get_guid() {

		return array(
			'id' => $this->ID,
			'store' => $this->get_store_slug(),
			'context_id' => $this->get_context_id(),
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_reactor_slug() {
		return $this->get_meta( 'reactor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_store_slug() {
		return $this->store->get_slug();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_context_id() {
		return $this->store->get_context_id();
	}
}

// EOF
