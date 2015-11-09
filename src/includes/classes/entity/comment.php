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
	implements WordPoints_Entity_Restricted_VisibilityI {

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
	protected function get_entity_human_id( $entity ) {
		return get_comment_excerpt( $entity );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Comment', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function user_can_view( $user_id, $id ) {

		$comment = get_comment( $id );

		if ( $comment ) {
			return user_can( $user_id, 'read_post', $comment->comment_post_ID );
		}

		return false;
	}
}

// EOF
