<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_User_Roles extends WordPoints_Entity_Relationship_OneToMany {

	protected $primary_entity_slug = 'user';
	protected $related_entity_slug = 'user_role{}';

	public function get_title() {
		return __( 'Roles', 'wordpoints' );
	}

	public function get_related_entity_ids( $id ) {
		return get_userdata( $id )->roles;
	}
}

// EOF
