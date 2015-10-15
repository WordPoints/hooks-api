<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_User_Role extends WordPoints_Entity_Object {

	protected $id_field = 'name';
	protected $getter = 'get_role';

	public function get_title() {
		return __( 'Role' );
	}

	public function get_human_id( $id ) {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$names = $wp_roles->get_names();

		return $names[ $id ];
	}

	public function get_user_roles() {
		return wp_roles()->role_objects;
	}
}

// EOF
