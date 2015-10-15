<?php

/**
 * Unit tests for the class autoloader.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Tests the WordPoints_Class_Autoloader class.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Autoloader
 */
class WordPoints_Class_Autoloader_Test extends PHPUnit_Framework_TestCase {

	//
	// Helpers.
	//

	/**
	 * Register the directory for the classes.
	 *
	 * @since 1.0.0
	 */
	protected function register_class_dir() {

		WordPoints_Class_Autoloader::register_dir(
			WORDPOINTS_MODULE_TESTS_DIR . '/data/class/autoloader'
			, 'WordPoints_Class_Autoloader_Test_'
		);
	}

	//
	// Tests.
	//

	/**
	 * Test loading a class that hasn't been registered.
	 *
	 * @since 1.0.0
	 */
	public function test_load_unregistered_class() {

		$class = 'Unregistered';
		WordPoints_Class_Autoloader::load_class( $class );
		$this->assertFalse( class_exists( $class ) );
	}

	/**
	 * Test loading a class that matches a prefix but isn't in the registered dir.
	 *
	 * @since 1.0.0
	 */
	public function test_load_nonexistent_class() {

		$class = 'WordPoints_Autoloader_Test_Nonexistent';

		$this->register_class_dir();

		WordPoints_Class_Autoloader::load_class( $class );
		$this->assertFalse( class_exists( $class ) );
	}

	/**
	 * Test loading a class.
	 *
	 * @since 1.0.0
	 */
	public function test_load_class() {

		$class = 'WordPoints_Class_Autoloader_Test_Load';

		$this->register_class_dir();

		WordPoints_Class_Autoloader::load_class( $class );
		$this->assertTrue( class_exists( $class ) );
	}
}

// EOF
