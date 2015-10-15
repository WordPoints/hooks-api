<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

class WordPoints_Entity_Comment
	extends WordPoints_Entity_Object
	implements WordPoints_Entity_Check_CapsI {

	protected $id_field = 'comment_ID';
	protected $getter = 'get_comment';

	public function get_title() {
		return __( 'Comment', 'wordpoints' );
	}

	public function get_human_id( $id ) {
		return get_comment_excerpt( $id );
	}

	public function check_user_caps( $user_id, $id ) {

		$comment = get_comment( $id );

		if ( $comment ) {
			return user_can( $user_id, 'read_post', $comment->comment_post_ID );
		}

		return false;
	}
}

// EOF
