<?php

/**
 * Base test case class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case.
 *
 * @since 1.0.0
 */
abstract class WordPoints_PHPUnit_TestCase extends WordPoints_UnitTestCase {

	/**
	 * A backup of the main app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_App
	 */
	protected $backup_app;

	/**
	 * @since 1.0.0
	 */
	public function setUp() {

		parent::setUp();

		if ( ! isset( $this->factory->wordpoints ) ) {
			$this->factory->wordpoints = WordPoints_PHPUnit_Factory::$factory;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function tearDown() {

		parent::tearDown();

		if ( isset( $this->backup_app ) ) {
			WordPoints_App::$main = $this->backup_app;
			$this->backup_app = null;
		}
	}

	/**
	 * Set up the global apps object as a mock.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_App The mock app.
	 */
	public function mock_apps() {

		$this->backup_app = WordPoints_App::$main;

		return WordPoints_App::$main = new WordPoints_PHPUnit_Mock_App_Silent(
			'apps'
		);
	}
}

// EOF
