<?php

/**
 * Reversible hook extension interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Implemented by extensions that want to hook into the Reverse firer.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Extension_ReverseI {

	/**
	 * Should this hit be reversed?
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The reverse fire object.
	 *
	 * @return bool Whether the hit should be reversed or not.
	 */
	public function should_reverse( WordPoints_Hook_Fire $fire );

	/**
	 * Called after a reverse action is called.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The reverse fire object.
	 */
	public function after_reverse( WordPoints_Hook_Fire $fire );
}

// EOF
