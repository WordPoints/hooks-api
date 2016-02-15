<?php

/**
 * Reversing hook reactor interface.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Interface implemented by reactors that want to listen for the reverse firer.
 *
 * @since 1.0.0
 */
interface WordPoints_Hook_Reactor_ReverseI {

	/**
	 * Reverses all hits matching this event and args.
	 *
	 * @since 1.0.0
	 *
	 * @param WordPoints_Hook_Fire $fire The reverse fire object.
	 */
	public function reverse_hit( WordPoints_Hook_Fire $fire );
}

// EOF
