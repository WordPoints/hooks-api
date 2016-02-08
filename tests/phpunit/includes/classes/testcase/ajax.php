<?php

/**
 * Base Ajax test case class.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case for Ajax tests.
 *
 * @since 1.0.0
 *
 * @property WordPoints_PHPUnit_Factory_Stub $factory The factory.
 */
abstract class WordPoints_PHPUnit_TestCase_Ajax extends WordPoints_Ajax_UnitTestCase {

	/**
	 * A backup of the main app.
	 *
	 * @since 1.0.0
	 *
	 * @var WordPoints_App
	 */
	protected $backup_app;

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
			 * Administration-side code.
			 *
			 * @since 1.0.0
			 */
			require_once( dirname( __FILE__ ) . '/../../../../../src/admin/admin.php' );

			self::$included_files = true;
		}
	}

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

		WordPoints_PHPUnit_Mock_Entity_Context::$current_id = 1;
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
