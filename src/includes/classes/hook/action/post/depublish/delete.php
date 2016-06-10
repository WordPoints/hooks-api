<?php

/**
 * Post delete depublish action class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents the Post depublish delete action.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Action_Post_Depublish_Delete extends WordPoints_Hook_Action_Post_Type {

	/**
	 * @since 1.0.0
	 */
	public function should_fire() {


		$post = $this->get_post_entity();
		if ( ! $post ) {
			return false;
		}

		if ( 'publish' !== get_post_status( $post->get_the_id() ) ) {
			return false;
		}

		return parent::should_fire();
	}
}

// EOF
