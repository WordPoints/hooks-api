<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_User extends WordPoints_Entity_Object {

	protected $id_field = 'ID';
	protected $getter = 'get_userdata';
	protected $human_id_field = 'display_name';

	public function get_title() {
		return __( 'User', 'wordpoints' );
	}
}

// EOF
