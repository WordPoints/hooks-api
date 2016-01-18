<?php

/**
 * Site entity context class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Represents the site context.
 *
 * On multisite installs there are many "sites" on a "network".
 *
 * @since 1.0.0
 */
class WordPoints_Entity_Context_Site extends WordPoints_Entity_Context {

	/**
	 * @since 1.0.0
	 */
	public function get_current_id() {

		if ( ! is_multisite() ) {
			return 1;
		}

		if ( wordpoints_is_network_context() ) {
			return false;
		}

		return get_current_blog_id();
	}
}

// EOF
