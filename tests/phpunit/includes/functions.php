<?php

/**
 * Functions for the PHPUnit tests.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Autoloader for the helper classes used by the PHPUnit tests.
 *
 * We could use the WordPoints_Class_Autoloader class instead, but the plugin isn't
 * always loaded during the tests.
 *
 * @since 1.0.0
 *
 * @param string $class_name The name of the class to load.
 */
function wordpoints_hooks_api_phpunit_autoloader( $class_name ) {

	if ( 'WordPoints_PHPUnit_' !== substr( $class_name, 0, 19 ) ) {
		return;
	}

	$file_name = str_replace( '_', '/', strtolower( substr( $class_name, 19 ) ) );
	$file_name = dirname( __FILE__ ) . '/classes/' . $file_name . '.php';

	if ( ! file_exists( $file_name ) ) {
		return;
	}

	require( $file_name );
}

// EOF
