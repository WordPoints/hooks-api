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
class WordPoints_Entity_Post_Type_Name
	extends WordPoints_Entity_Attr
	implements WordPoints_Entity_Attr_Enumerable {

	/**
	 * @since 1.0.0
	 */
	protected $type = 'text';

	/**
	 * @since 1.0.0
	 */
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

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return _x( 'Name', 'post type', 'wordpoints' );
	}
}

// EOF
