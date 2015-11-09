<?php

/**
 * Comment author entity relationship class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the relationship between a Comment and its author.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Comment_Author extends WordPoints_Entity_Relationship {

	/**
	 * @since 1.0.0
	 */
	protected $primary_entity_slug = 'comment';

	/**
	 * @since 1.0.0
	 */
	protected $related_entity_slug = 'user';

	/**
	 * @since 1.0.0
	 */
	protected $related_ids_field = 'comment_author';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Author', 'wordpoints' );
	}
}

// EOF
