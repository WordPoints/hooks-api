<?php

/**
 * Site entity class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents a Site (formerly "Blog") on a multisite Network.
 *
 * TODO should implement check caps
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Site extends WordPoints_Entity {

	/**
	 * @since 1.0.0
	 */
	protected $context = array( 'network' );

	/**
	 * @since 1.0.0
	 */
	protected $id_field = 'blog_id';

	/**
	 * @since 1.0.0
	 */
	protected $human_id_field = 'blogname';

	/**
	 * @since 1.0.0
	 */
	protected $getter = 'get_blog_details';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Site', 'wordpoints' );
	}
}

// EOF
