<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

// TODO should implement check caps
class WordPoints_Entity_Site extends WordPoints_Entity_Object {

	protected $id_field = 'blog_id';
	protected $human_id_field = 'blogname';
	protected $getter = 'get_blog_details';

	public function get_title() {
		return __( 'Site', 'wordpoints' );
	}
}

// EOF
