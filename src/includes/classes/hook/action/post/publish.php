<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Hook_Action_Post_Publish extends WordPoints_Hook_Action {

	/**
	 * @since 1.0.0
	 */
	public function should_fire() {

		$post = $this->get_arg_value( 'post' );

		if ( ! isset( $post->post_type ) ) {
			return false;
		}

		$post_type = get_post_type( $post->post_type );

		// Don't fire for non-public post types (revisions, etc.).
		if ( empty( $post_type->public ) ) {
			return false;
		}

		return parent::should_fire();
	}
}

// EOF
