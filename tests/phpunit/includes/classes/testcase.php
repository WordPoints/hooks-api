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
 *
 * @property WordPoints_PHPUnit_Factory_Stub $factory The factory.
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

		global $wpdb, $EZSQL_ERROR;

		if ( $EZSQL_ERROR instanceof WordPoints_PHPUnit_Error_Handler_Database ) {
			$wpdb->suppress_errors = true;
			$wpdb->show_errors = false;
		}

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

		unset( $GLOBALS['current_screen'] );
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

	/**
	 * Mock being in the network admin.
	 *
	 * @since 1.0.0
	 */
	public function set_network_admin() {
		$GLOBALS['current_screen'] = WP_Screen::get( 'test-network' );
	}
}

// EOF
