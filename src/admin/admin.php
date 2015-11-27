<?php

/**
 * Includes administration-side code..
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * The admin-side functions.
 *
 * @since 1.0.0
 */
require_once( dirname( __FILE__ ) . '/includes/functions.php' );

/**
 * The admin-side actions and filters.
 *
 * @since 1.0.0
 */
require_once( dirname( __FILE__ ) . '/includes/actions.php' );

WordPoints_Class_Autoloader::register_dir(
	dirname( __FILE__ ) . '/includes/classes'
	, 'WordPoints_Admin_'
);

// EOF
