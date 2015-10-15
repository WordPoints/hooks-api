<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Post
	extends WordPoints_Entity_Object
	implements WordPoints_Entity_Check_CapsI {

	protected $id_field = 'ID';
	protected $getter = 'get_post';
	protected $human_id_field = 'post_title';

	public function get_title() {
		return __( 'Content', 'wordpoints' ); // TODO standardize on Post or Content
	}

	public function check_user_caps( $user_id, $id ) {
		return user_can( $user_id, 'read_post', $id );
	}
}

// EOF
