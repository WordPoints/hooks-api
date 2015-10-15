<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Hook_Action_Comment_New extends WordPoints_Hook_Action {

	/**
	 * @since 1.0.0
	 */
	protected $arg_index = array( 1 => 'comment' );

	/**
	 * @since 1.0.0
	 */
	public function should_fire() {
		return 1 === (int) $this->args[1]->comment_approved;
	}
}

// EOF
