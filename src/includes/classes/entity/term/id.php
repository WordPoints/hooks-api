<?php

/**
 * Term ID entity attribute class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the ID attribute of a Term.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Term_Id extends WordPoints_Entity_Attr_Field {

	/**
	 * @since 1.0.0
	 */
	protected $storage_type = 'db';

	/**
	 * @since 1.0.0
	 */
	protected $field = 'term_id';

	/**
	 * @since 1.0.0
	 */
	protected $data_type = 'int';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return _x( 'ID', 'taxonomy term', 'wordpoints' );
	}
}
// EOF
