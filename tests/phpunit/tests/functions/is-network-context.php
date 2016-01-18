<?php

/**
 * Test case for wordpoints_is_network_context().
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests wordpoints_is_network_context().
 *
 * @since 1.0.0
 *
 * @covers ::wordpoints_is_network_context
 */
class WordPoints_Is_Network_Context_Function_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test that it is false by default.
	 *
	 * @since 1.0.0
	 */
	public function test_not() {

		$this->assertFalse( wordpoints_is_network_context() );
	}

	/**
	 * Test that true in the network admin.
	 *
	 * @since 1.0.0
	 */
	public function test_network_admin() {

		$this->set_network_admin();
		$this->assertTrue( wordpoints_is_network_context() );
	}

	/**
	 * Test that true for Ajax requests from the network admin.
	 *
	 * @since 1.0.0
	 *
	 * @backupGlobals enabled
	 */
	public function test_network_admin_ajax() {

		$_SERVER['HTTP_REFERER'] = network_admin_url() . '/admin-ajax.php';

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$this->assertTrue( wordpoints_is_network_context() );
	}

	/**
	 * Test that true in the network admin.
	 *
	 * @since 1.0.0
	 */
	public function test_has_filter() {

		$filter = new WordPoints_Mock_Filter( true );
		add_filter( 'wordpoints_is_network_context', array( $filter, 'filter' ) );

		$this->assertTrue( wordpoints_is_network_context() );

		$this->assertEquals( 1, $filter->call_count );
		$this->assertEquals( array( false ), $filter->calls[0] );
	}
}

// EOF
