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
	 * A list of global variables to back up between tests.
	 *
	 * PHPUnit has built-in support for backing up globals between tests, but it has
	 * a few issues that make it difficult to use. First, it has only a blacklist, no
	 * whitelist. That means that when you enable the backup globals feature for a
	 * test, all of the globals will be backed up. This can be time-consuming, and
	 * also leads to breakage because of the way that the globals are backed up.
	 * PHPUnit backs up the globals by serializing them, which is necessary for some
	 * uses, but causes `$wpdb` to stop working after the globals are restored,
	 * causing all tests after that to fail. Our implementation here is much simpler,
	 * and is based on a whitelist so that we can just back up the globals that
	 * actually need to be backed up.
	 *
	 * @since 1.0.0
	 *
	 * @var string[]
	 */
	protected $backup_globals;

	/**
	 * Backed up values of global variables that are modified in the tests.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $backed_up_globals = array();

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

		if ( ! empty( $this->backup_globals ) ) {
			foreach ( $this->backup_globals as $global ) {
				$this->backed_up_globals[ $global ] = isset( $GLOBALS[ $global ] )
					? $GLOBALS[ $global ]
					: null;
			}
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

		if ( ! empty( $this->backed_up_globals ) ) {
			foreach ( $this->backed_up_globals as $key => $value ) {
				$GLOBALS[ $key ] = $value;
			}
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
