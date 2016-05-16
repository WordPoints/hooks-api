<?php

/**
 * Hit listener hook extension interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by extensions that want to listen for hits.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Extension_Hit_ListenerI {

	/**
	 * Method to be called after a reaction has hit the target.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The hook fire object.
	 */
	public function after_hit( WordPoints_Hook_Fire $fire );
}

// EOF
