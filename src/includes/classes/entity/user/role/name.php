<?php

/**
 * User Role name entity attribute class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the name attribute of a User Role.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_User_Role_Name
	extends WordPoints_Entity_Attr
	implements WordPoints_Entity_Attr_Enumerable {

	/**
	 * @since 1.0.0
	 */
	protected $field = 'name';

	/**
	 * @since 1.0.0
	 */
	protected $type = 'slug';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Role Name', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_enumerated_values() {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$values = array();

		foreach ( $wp_roles->get_names() as $slug => $name ) {

			$values[ $slug ] = array(
				'value' => $slug,
				'label' => $name,
			);
		}

		return $values;
	}
}

// EOF
