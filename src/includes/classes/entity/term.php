<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since 1.
 */

class WordPoints_Entity_Term extends WordPoints_Entity_Object {

	protected $id_field = 'term_id';
	protected $human_id_field = 'name';

	public function get_title() {
		return __( 'Term', 'wordpoints' );
	}

	public function get_entity( $id ) {
		return get_term( $id, 'taxonomy' );
	}
}

// EOF
