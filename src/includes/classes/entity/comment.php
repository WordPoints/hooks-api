<?php

/**
 * Comment entity class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents a Comment as an entity.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Comment
	extends WordPoints_Entity
	implements WordPoints_Entity_Check_CapsI {

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'comment_ID';

	/**
	 * @since 1.0.0
	 */
	protected $getter = 'get_comment';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Comment', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_human_id( $id ) {
		return get_comment_excerpt( $id );
	}

	/**
	 * @since 1.0.0
	 */
	public function check_user_caps( $user_id, $id ) {

		$comment = get_comment( $id );

		if ( $comment ) {
			return user_can( $user_id, 'read_post', $comment->comment_post_ID );
		}

		return false;
	}
}

// EOF
