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

		// Todo special handling for Ajax, ect.
		if ( is_network_admin() ) {
			return false;
		}

		return get_current_blog_id();
	}
}

// EOF
