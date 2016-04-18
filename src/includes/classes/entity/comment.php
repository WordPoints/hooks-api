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
	implements WordPoints_Entity_Stored_DBI,
		WordPoints_Entity_Restricted_VisibilityI {

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'comment_ID';

	/**
	 * @since 1.0.0
	 */
	protected function get_entity( $id ) {

		// We must do this because the $id parameter is expected by reference.
		$comment = get_comment( $id );

		if ( ! $comment ) {
			return false;
		}

		return $comment;
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_entity_human_id( $entity ) {
		// An extra space is added to the end in WP <4.4 if the comment is short.
		// See https://core.trac.wordpress.org/ticket/27526#comment:5
		return rtrim( get_comment_excerpt( $entity->comment_ID ) );
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
	
	/**
	 * @since 1.0.0
	 */
	public function get_table_name() {
		return $GLOBALS['wpdb']->comments;
	}
}

// EOF
