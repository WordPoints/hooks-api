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
	implements WordPoints_Entity_Restricted_VisibilityI {

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

		$post_type = get_post_type_object( substr( $this->slug, 5 /* post\ */ ) );

		if ( $post_type ) {
			return $post_type->labels->singular_name;
		} else {
			return $this->slug;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function user_can_view( $user_id, $id ) {
		return user_can( $user_id, 'read_post', $id );
	}
}

// EOF
