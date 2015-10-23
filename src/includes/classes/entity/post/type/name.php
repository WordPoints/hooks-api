<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Post_Type_Name
	extends WordPoints_Entity_Attr
	implements WordPoints_Entity_Attr_Enumerable {

	protected $type = 'text';
	protected $field = 'name';

	/**
	 * @since 1.0.0
	 */
	public function get_enumerated_values() {

		$values = array();

		foreach ( WordPoints_Entity_Post_Type::get_post_types() as $post_type ) {

			$values[ $post_type->name ] = array(
				'value' => $post_type->name,
				'label' => $post_type->labels->singular_name,
			);
		}

		return $values;
	}

	public function get_title() {
		return _x( 'Name', 'post type', 'wordpoints' );
	}
}

// EOF
