<?php

/**
 * Post Type entity class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a Post Type.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Post_Type extends WordPoints_Entity {

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'name';

	/**
	 * @since 1.0.0
	 */
	protected $getter = 'get_post_type_object';

	/**
	 * @since 1.0.0
	 */
	protected $human_id_field = 'label';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Content Type', 'wordpoints' );
	}

	/**
	 * Get a list of all available Post Types.
	 *
	 * @since 1.0.0
	 */
	public static function get_post_types() {
		return get_post_types( array( 'public' => true ), 'labels' );
	}
}

// EOF
