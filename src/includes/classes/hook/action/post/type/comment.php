<?php

/**
 * Comment post type action class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Bootstrap for comment-related actions that occur across multiple post types.
 *
 * @since 1.0.0
 */
class WordPoints_Hook_Action_Post_Type_Comment
	extends WordPoints_Hook_Action_Post_Type {

	/**
	 * @since 1.0.0
	 */
	protected $post_hierarchy = array( 'comment\\post', 'post\\post', 'post\\post' );
}

// EOF
