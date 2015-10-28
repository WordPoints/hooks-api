<?php

/**
 * Term entity class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents a Taxonomy Term.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Term extends WordPoints_Entity {

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'term_id';

	/**
	 * @since 1.0.0
	 */
	protected $human_id_field = 'name';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Term', 'wordpoints' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_entity( $id ) {
		return get_term( $id, 'taxonomy' );
	}
}

// EOF
