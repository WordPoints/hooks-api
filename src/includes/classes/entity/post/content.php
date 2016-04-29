<?php

/**
 * Post content entity attribute class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a Post's content attribute.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Post_Content extends WordPoints_Entity_Attr_Field {

	/**
	 * @since 1.0.0
	 */
	protected $storage_type = 'db';
	
	/**
	 * @since 1.0.0
	 */
	protected $data_type = 'text';

	/**
	 * @since 1.0.0
	 */
	protected $field = 'post_content';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Content', 'wordpoints' );
	}
}

// EOF
