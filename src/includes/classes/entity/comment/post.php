<?php

/**
 * Comment post entity relationship class.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Represents the relationship between a Comment and its Post.
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Comment_Post extends WordPoints_Entity_Relationship_Dynamic {

	/**
	 * @since 1.0.0
	 */
	protected $primary_entity_slug = 'comment';

	/**
	 * @since 1.0.0
	 */
	protected $related_entity_slug = 'post';

	/**
	 * @since 1.0.0
	 */
	protected $related_ids_field = 'comment_post_ID';
}

// EOF
