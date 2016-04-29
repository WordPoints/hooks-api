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
class WordPoints_Entity_Term extends WordPoints_Entity_Stored_DB_Table {

	/**
	 * @since 1.0.0
	 */
	protected $wpdb_table_name = 'terms';

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

		$taxonomy = get_taxonomy( substr( $this->slug, 5 /* term\ */ ) );

		if ( $taxonomy ) {
			return $taxonomy->labels->singular_name;
		} else {
			return $this->slug;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function get_entity( $id ) {
		return get_term( $id, 'taxonomy' );
	}
}

// EOF
