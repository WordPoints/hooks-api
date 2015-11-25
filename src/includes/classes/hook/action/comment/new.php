<?php

/**
 * New comment action class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents the new Comment action.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Action_Comment_New extends WordPoints_Hook_Action {

	/**
	 * @since 1.0.0
	 */
	public function should_fire() {

		if ( ! isset( $this->args[1]->comment_approved ) ) {
			return false;
		}

		if ( 1 !== (int) $this->args[1]->comment_approved ) {
			return false;
		}

		return parent::should_fire();
	}
}

// EOF
