<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Post_Content extends WordPoints_Entity_Attr {

	protected $type = 'text';
	protected $field = 'post_content';

	public function get_title() {
		return __( 'Content' );
	}
}

// EOF
