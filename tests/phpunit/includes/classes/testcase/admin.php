<?php

/**
 * Base admin test case class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case for admin-side code tests.
 *
 * @since 1.0.0
 */
abstract class WordPoints_PHPUnit_TestCase_Admin extends WordPoints_PHPUnit_TestCase {

	/**
	 * Whether the admin-side code has been included yet.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected static $included_files = false;

	/**
	 * @since 1.0.0
	 */
	public static function setUpBeforeClass() {

		parent::setUpBeforeClass();

		if ( ! self::$included_files ) {

			/**
			 * WordPoints administration-side code.
			 *
			 * @since 1.0.0
			 */
			require_once( WORDPOINTS_DIR . '/admin/admin.php' );

			/**
			 * Administration-side code.
			 *
			 * @since 1.0.0
			 */
			require_once( dirname( __FILE__ ) . '/../../../../../src/admin/admin.php' );

			self::$included_files = true;
		}
	}
}

// EOF
