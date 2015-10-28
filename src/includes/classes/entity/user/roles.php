<?php

/**
 * User Roles entity relationship class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the relationship of between a User and their Roles.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_User_Roles extends WordPoints_Entity_Relationship {

	/**
	 * @since 1.0.0
	 */
	protected $primary_entity_slug = 'user';

	/**
	 * @since 1.0.0
	 */
	protected $related_entity_slug = 'user_role{}';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Roles', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_related_entity_ids( $id ) {
		return get_userdata( $id )->roles;
	}
}

// EOF
