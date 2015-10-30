<?php

/**
 * User Role entity class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a User Role.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_User_Role extends WordPoints_Entity {

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'name';

	/**
	 * @since 1.0.0
	 */
	protected $getter = 'get_role';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Role', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_human_id( $id ) {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$names = $wp_roles->get_names();

		return $names[ $id ];
	}
}

// EOF
