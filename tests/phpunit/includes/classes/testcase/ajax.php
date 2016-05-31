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

	/**
	 * Generate a request from specifications.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $specs The request specifications.
	 */
	public function generate_request( $specs ) {

		foreach ( $specs as $spec ) {

			$parts = explode( '_', $spec );

			$type = $parts[0];

			unset( $parts[0] );

			switch ( $type ) {

				case 'am':
					$this->fulfill_am_requirement( $parts );
				break;

				case 'can':
					$this->fulfill_can_requirement( $parts );
				break;

				case 'posts':
					$this->fulfill_posts_requirement( $parts );
				break;

				default:
					$this->fulfill_other_requirement( $type, $parts );
			}
		}
	}

	/**
	 * Create the specs for valid requests based on the specs for a valid request.
	 *
	 * Because some things can be optional.
	 * 
	 * @since 1.0.0
	 *
	 * @param string[] $specs The specs for a valid request.
	 *
	 * @return array[] The valid requests, ready to be returned by a data provider.
	 */
	public function generate_valid_request_specs( $specs ) {

		$valid_requests = array( 'basic' => array( $specs ) );

		foreach ( $specs as $index => $spec ) {
			
			if ( 'posts_optional_' === substr( $spec, 0, 15 ) ) {
				$request = $specs;
				unset( $request[ $index ] );
				$valid_requests['no' . substr( $spec, 14 ) ] = array( $request );
			}
		}

		return $valid_requests;
	}

	/**
	 * Create the specs for invalid requests based on the specs for a valid request.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $specs The specs for a valid request.
	 *
	 * @return array[] The invalid requests, ready to be returned by a data provider.
	 */
	public function generate_invalid_request_specs( $specs ) {

		$invalid_requests = array();

		foreach ( $specs as $index => $spec ) {

			$parts = explode( '_', $spec );

			$request = $specs;

			unset( $request[ $index ] );

			$type = $parts[0];

			unset( $parts[0] );

			$rest = implode( '_', $parts );

			switch ( $type ) {

				case 'am':
					$invalid_requests[ 'not_' . $rest ] = array( $request );
				break;
				
				case 'can':
					$invalid_requests[ 'cant_' . $rest ] = array( $request );
				break;

				case 'posts':
					$next_part = '';
					
					switch ( $parts[1] ) {
						
						case 'valid':
							$invalid_requests[ 'missing_' . $rest ] = array( $request );
							$next_part = $parts[1];
						break;

						case 'optional':
							$next_part = $parts[2];
							$rest = substr( $rest, 0, 9 /* optional_ */ );
						break;
					}

					if ( 'valid' === $next_part ) {
						// The 'in' makes 'valid' become 'invalid'.
						$request[ $index ] = 'posts_in' . $rest;

						$invalid_requests[ 'invalid' . ltrim( $rest, 'valid' ) ] = array( $request );
					}
				break;
			}
		}

		return $invalid_requests;
	}

	/**
	 * Fulfill the requirements for an "am" request specification.
	 *
	 * An "am" request specification dictates that the current user must have a
	 * certain role.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $requirement_parts The requirement parts.
	 */
	public function fulfill_am_requirement( $requirement_parts ) {

		$this->_setRole( implode( '_', $requirement_parts ) );
	}

	/**
	 * Fulfill the requirements for an "can" request specification.
	 *
	 * A "can" request specification dictates that the current user must have a
	 * certain capability.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $requirement_parts The requirement parts.
	 */
	public function fulfill_can_requirement( $requirement_parts ) {
		
		$post = $_POST;
		
		/** @var WP_User $user */
		$user = $this->factory->user->create_and_get();
		$user->add_cap( implode( '_', $requirement_parts ) );
		
		wp_set_current_user( $user->ID );
		
		$_POST = array_merge($_POST, $post);
	}

	/**
	 * Fulfill the requirements for a "posts" request specification.
	 *
	 * A "posts" request spec dictates that a certain value should be posted. The
	 * value can be requested to be valid or invalid, and will be supplied
	 * accordingly.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $requirement_parts The requirement parts.
	 */
	public function fulfill_posts_requirement( $requirement_parts ) {

		$type = $requirement_parts[1];

		$parts = $requirement_parts;
		
		unset( $parts[1] );
		
		$rest = implode( '_', $parts );

		switch ( $type ) {

			case 'invalid':
				$_POST[ $rest ] = 'invalid';
			break;

			case 'valid':
			case 'optional':
				$_POST[ $rest ] = $this->get_valid_posts_value( $rest );
			break;

			default:
				$parts = implode( '_', $requirement_parts );
				
				$_POST[ $parts ] = $this->get_valid_posts_value( $parts );
		}
	}

	/**
	 * Get a valid value for a POST query arg.
	 *
	 * @since 1.0.0
	 *
	 * @param string $query_arg The name of the POST query arg to get the valid data
	 *                          for.
	 *
	 * @return mixed The valid data for this query arg.
	 */
	public function get_valid_posts_value( $query_arg ) {
		return null;
	}

	/**
	 * Fulfill the requirements for a non-standard request specification.
	 *
	 * If you want to use a request spec of a type other than those provided, you
	 * can override this method to provide the logic to fulfill such requirements.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $type              The type of requirement.
	 * @param string[] $requirement_parts The requirement parts.
	 */
	public function fulfill_other_requirement( $type, $requirement_parts ) {}
}

// EOF
