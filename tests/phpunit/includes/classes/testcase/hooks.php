<?php

/**
 * Parent test case class for the hooks API tests.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Parent test case for testing the hooks API.
 *
 * @since 1.0.0
 */
abstract class WordPoints_PHPUnit_TestCase_Hooks extends WordPoints_UnitTestCase {

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
			WordPoints_Apps::$main_app = $this->backup_app;
			$this->backup_app = null;
		}
	}

	/**
	 * Set up the global apps object as a mock.
	 *
	 * @since 1.0.0
	 *
	 * @return WordPoints_Apps The mock app.
	 */
	public function mock_apps() {

		$this->backup_app = WordPoints_Apps::$main_app;

		return WordPoints_Apps::$main_app = new WordPoints_Apps();
	}

	public function assertIsReaction( $reaction ) {

		if ( $reaction instanceof WordPoints_Hook_Reaction_Validator ) {

			$message = '';

			foreach ( $reaction->get_errors() as $error ) {
				$message .= PHP_EOL . 'Field: ' . implode( '.',  $error['field'] );
				$message .= PHP_EOL . 'Error: ' . $error['message'];

			}

			$this->fail( $message );
		}

		$this->assertInstanceOf( 'WordPoints_Hook_ReactionI', $reaction );
	}
}

// EOF
