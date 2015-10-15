<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Post_Type extends WordPoints_Entity_Object {

	protected $id_field = 'name';
	protected $getter = 'get_post_type_object';
	protected $human_id_field = 'label';

	public function get_title() {
		return __( 'Content Type', 'wordpoints' );
	}

	public static function get_post_types() {
		return get_post_types( array( 'public' => true ), 'labels' );
	}
}

// EOF
