<?php

/**
 * Post Type name entity attribute class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the name attribute of a Post Type.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Post_Type_Name extends WordPoints_Entity_Attr_Field {

	/**
	 * @since 1.0.0
	 */
	protected $storage_type = 'array';

	/**
	 * @since 1.0.0
	 */
	protected $data_type = 'text';

	/**
	 * @since 1.0.0
	 */
	protected $field = 'name';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return _x( 'Name', 'post type', 'wordpoints' );
	}
}

// EOF
