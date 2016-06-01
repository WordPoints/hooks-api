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
class WordPoints_Entity_User_Role_Name extends WordPoints_Entity_Attr_Field {

	/**
	 * @since 1.0.0
	 */
	protected $storage_type = 'array';

	/**
	 * @since 1.0.0
	 */
	protected $field = 'name';

	/**
	 * @since 1.0.0
	 */
	protected $data_type = 'slug';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return _x( 'Name', 'user role', 'wordpoints' );
	}
}

// EOF
