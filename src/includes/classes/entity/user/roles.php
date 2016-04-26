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
class WordPoints_Entity_User_Roles
	extends WordPoints_Entity_Relationship
	implements WordPoints_Entity_Relationship_Stored_DB_Table_ConditionsI {

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
	protected $related_ids_field = 'roles';

	/**
	 * @since 1.0.0
	 */
	protected function get_related_entity_ids( WordPoints_Entity $entity ) {
		return $entity->get_the_attr_value( $this->related_ids_field );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Roles', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_table_name() {
		return $GLOBALS['wpdb']->usermeta;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_primary_id_field() {
		return 'user_id';
	}

	/**
	 * @since 1.0.0
	 */
	public function get_related_id_field() {
		return array( 'field' => 'meta_value', 'type' => 'serialized_array' );
	}
	
	/**
	 * @since 1.0.0
	 */	
	public function get_conditions() {
		return array(
			array(
				'field' => 'meta_key',
				'value' => $GLOBALS['wpdb']->get_blog_prefix() . 'capabilities',
			),
		);
	}
}

// EOF
