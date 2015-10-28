<?php

/**
 * Entity post class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a Post.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Post
	extends WordPoints_Entity
	implements WordPoints_Entity_Check_CapsI {

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'ID';

	/**
	 * @since 1.0.0
	 */
	protected $getter = 'get_post';

	/**
	 * @since 1.0.0
	 */
	protected $human_id_field = 'post_title';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Content', 'wordpoints' ); // TODO standardize on Post or Content
	}

	/**
	 * @since 1.0.0
	 */
	public function check_user_caps( $user_id, $id ) {
		return user_can( $user_id, 'read_post', $id );
	}
}

// EOF
