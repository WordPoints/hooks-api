<?php

/**
 * User entity class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a User.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_User extends WordPoints_Entity_Stored_DB_Table {

	/**
	 * @since 1.0.0
	 */
	protected $wpdb_table_name = 'users';

	/**
	 * @since 1.0.0
	 */
	protected $context = '';

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'ID';

	/**
	 * @since 1.0.0
	 */
	protected $getter = 'get_userdata';

	/**
	 * @since 1.0.0
	 */
	protected $human_id_field = 'display_name';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'User', 'wordpoints' );
	}
}

// EOF
