<?php

/**
 * Test case for wordpoints_*_maybe_network_option() functions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the maybe network option functions.
 *
 * @since 1.0.0
 */
class WordPoints_Maybe_Network_Option_Function_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test getting a maybe network option returns a regular option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_option
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_get_not_network_active() {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertEquals( 'option', wordpoints_get_maybe_network_option( 'test' ) );
	}

	/**
	 * Test getting a maybe network option returns a network option if network active.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_option
	 *
	 * @requires WordPoints network-active
	 */
	public function test_get_network_active() {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertEquals(
			'network_option'
			, wordpoints_get_maybe_network_option( 'test' )
		);
	}

	/**
	 * Test getting a maybe network option returns a regular option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_option
	 *
	 * @dataProvider data_provider_get_option
	 *
	 * @param bool  $network Whether to get a network or regular option.
	 * @param mixed $result  The expected result.
	 */
	public function test_get( $network, $result ) {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertEquals(
			$result
			, wordpoints_get_maybe_network_option( 'test', $network )
		);
	}

	/**
	 * Provides data for getting options.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Data for getting options.
	 */
	public function data_provider_get_option() {
		return array(
			'regular' => array( false, 'option' ),
			'network' => array( true, 'network_option' ),
		);
	}

	/**
	 * Test getting a maybe network option returns false if no option found.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_option
	 *
	 * @dataProvider data_provider_get_option
	 *
	 * @param bool $network Whether to get a network or regular option.
	 */
	public function test_get_no_option( $network ) {

		$this->assertEquals(
			false
			, wordpoints_get_maybe_network_option( 'test', $network )
		);
	}

	/**
	 * Test getting a maybe network option returns $default if no option found.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_option
	 *
	 * @dataProvider data_provider_get_option
	 *
	 * @param bool $network Whether to get a network or regular option.
	 */
	public function test_get_no_option_default_passed( $network ) {

		$this->assertEquals(
			'bob'
			, wordpoints_get_maybe_network_option( 'test', $network, 'bob' )
		);
	}

	/**
	 * Test getting a maybe network option returns a regular option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_array_option
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_get_array_not_network_active() {

		update_option( 'test', array( 'option' ) );
		update_site_option( 'test', array( 'network_option' ) );

		$this->assertEquals(
			array( 'option' )
			, wordpoints_get_maybe_network_array_option( 'test' )
		);
	}

	/**
	 * Test getting a maybe network option returns a network option if network active.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_array_option
	 *
	 * @requires WordPoints network-active
	 */
	public function test_get_array_network_active() {

		update_option( 'test', array( 'option' ) );
		update_site_option( 'test', array( 'network_option' ) );

		$this->assertEquals(
			array( 'network_option' )
			, wordpoints_get_maybe_network_array_option( 'test' )
		);
	}

	/**
	 * Test getting a maybe network option returns a regular option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_array_option
	 *
	 * @dataProvider data_provider_get_option
	 *
	 * @param bool  $network Whether to get a network or regular option.
	 * @param mixed $result  The expected result.
	 */
	public function test_get_array( $network, $result ) {

		update_option( 'test', array( 'option' ) );
		update_site_option( 'test', array( 'network_option' ) );

		$this->assertEquals(
			(array) $result
			, wordpoints_get_maybe_network_array_option( 'test', $network )
		);
	}

	/**
	 * Test getting a maybe network option returns an empty array if no option found.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_get_maybe_network_array_option
	 *
	 * @dataProvider data_provider_get_option
	 *
	 * @param bool $network Whether to get a network or regular option.
	 */
	public function test_get_array_no_option( $network ) {

		$this->assertEquals(
			array()
			, wordpoints_get_maybe_network_array_option( 'test', $network )
		);
	}

	/**
	 * Test adding a maybe network option adds a regular option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_add_not_network_active() {

		$this->assertTrue( wordpoints_add_maybe_network_option( 'test', 'testing' ) );

		$this->assertEquals( 'testing', get_option( 'test' ) );
		$this->assertFalse( get_site_option( 'test' ) );
	}

	/**
	 * Test adding a maybe network option adds a network option if network active.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 *
	 * @requires WordPoints network-active
	 */
	public function test_add_network_active() {

		$this->assertTrue( wordpoints_add_maybe_network_option( 'test', 'testing' ) );

		$this->assertFalse( get_option( 'test' ) );
		$this->assertEquals( 'testing', get_site_option( 'test' ) );
	}

	/**
	 * Test adding a maybe network option adds a regular option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 */
	public function test_add() {

		$this->assertTrue(
			wordpoints_add_maybe_network_option( 'test', 'testing', false )
		);

		$this->assertEquals( 'testing', get_option( 'test' ) );
		$this->assertFalse( get_site_option( 'test' ) );
	}

	/**
	 * Test adding a maybe network option adds a network option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 */
	public function test_add_network() {

		$this->assertTrue(
			wordpoints_add_maybe_network_option( 'test', 'testing', true )
		);

		$this->assertFalse( get_option( 'test' ) );
		$this->assertEquals( 'testing', get_site_option( 'test' ) );
	}

	/**
	 * Test adding a maybe network option doesn't overwrite the option if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 */
	public function test_add_option_exists() {

		update_option( 'test', 'option' );

		$this->assertFalse(
			wordpoints_add_maybe_network_option( 'test', 'testing', false )
		);

		$this->assertEquals( 'option', get_option( 'test' ) );
	}

	/**
	 * Test adding a maybe network option doesn't overwrite the option if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 */
	public function test_add_network_option_exists() {

		update_site_option( 'test', 'network_option' );

		$this->assertFalse(
			wordpoints_add_maybe_network_option( 'test', 'testing', true )
		);

		$this->assertEquals( 'network_option', get_site_option( 'test' ) );
	}

	/**
	 * Test adding a maybe network option adds an autoloaded option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 */
	public function test_add_autoloaded() {

		$this->assertTrue(
			wordpoints_add_maybe_network_option( 'test', 'testing', false )
		);

		$this->assertTrue( $this->is_option_autoloaded( 'test' ) );
	}


	/**
	 * Test adding a maybe network option adds an autoloaded option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_add_maybe_network_option
	 */
	public function test_add_not_autoloaded() {

		$this->assertTrue(
			wordpoints_add_maybe_network_option( 'test', 'testing', false, 'no' )
		);

		$this->assertFalse( $this->is_option_autoloaded( 'test' ) );
	}

	/**
	 * Checks whether an option is autoloaded or not.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option The option name.
	 *
	 * @return bool Whether the option is autoloaded or not.
	 */
	protected function is_option_autoloaded( $option ) {

		global $wpdb;

		return 'yes' === $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `autoload` FROM {$wpdb->options} WHERE `option_name` = %s"
				, $option
			)
		);
	}

	/**
	 * Test updating a maybe network option updates a regular option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_update_maybe_network_option
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_update_not_network_active() {

		$this->assertTrue(
			wordpoints_update_maybe_network_option( 'test', 'testing' )
		);

		$this->assertEquals( 'testing', get_option( 'test' ) );
		$this->assertFalse( get_site_option( 'test' ) );
	}

	/**
	 * Test updating a maybe network option updates a network option if network active.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_update_maybe_network_option
	 *
	 * @requires WordPoints network-active
	 */
	public function test_update_network_active() {

		$this->assertTrue(
			wordpoints_update_maybe_network_option( 'test', 'testing' )
		);

		$this->assertFalse( get_option( 'test' ) );
		$this->assertEquals( 'testing', get_site_option( 'test' ) );
	}

	/**
	 * Test updating a maybe network option updates a regular option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_update_maybe_network_option
	 */
	public function test_update() {

		$this->assertTrue(
			wordpoints_update_maybe_network_option( 'test', 'testing', false )
		);

		$this->assertEquals( 'testing', get_option( 'test' ) );
		$this->assertFalse( get_site_option( 'test' ) );
	}

	/**
	 * Test updating a maybe network option updates a network option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_update_maybe_network_option
	 */
	public function test_update_network() {

		$this->assertTrue(
			wordpoints_update_maybe_network_option( 'test', 'testing', true )
		);

		$this->assertFalse( get_option( 'test' ) );
		$this->assertEquals( 'testing', get_site_option( 'test' ) );
	}

	/**
	 * Test updating a maybe network option overwrites the option if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_update_maybe_network_option
	 */
	public function test_update_option_exists() {

		update_option( 'test', 'option' );

		$this->assertTrue(
			wordpoints_update_maybe_network_option( 'test', 'testing', false )
		);

		$this->assertEquals( 'testing', get_option( 'test' ) );
	}

	/**
	 * Test updating a maybe network option overwrites the option if it exists.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_update_maybe_network_option
	 */
	public function test_update_network_option_exists() {

		update_site_option( 'test', 'network_option' );

		$this->assertTrue(
			wordpoints_update_maybe_network_option( 'test', 'testing', true )
		);

		$this->assertEquals( 'testing', get_site_option( 'test' ) );
	}


	/**
	 * Test deleting a maybe network option deletes a regular option by default.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_delete_maybe_network_option
	 *
	 * @requires WordPoints !network-active
	 */
	public function test_delete_not_network_active() {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertTrue( wordpoints_delete_maybe_network_option( 'test' ) );

		$this->assertFalse( get_option( 'test' ) );
		$this->assertEquals( 'network_option', get_site_option( 'test' ) );

	}

	/**
	 * Test deleting a maybe network option deletes a network option if network active.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_delete_maybe_network_option
	 *
	 * @requires WordPoints network-active
	 */
	public function test_delete_network_active() {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertTrue( wordpoints_delete_maybe_network_option( 'test' ) );

		$this->assertEquals( 'option', get_option( 'test' ) );
		$this->assertFalse( get_site_option( 'test' ) );
	}

	/**
	 * Test deleting a maybe network option deletes a regular option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_delete_maybe_network_option
	 */
	public function test_delete() {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertTrue(
			wordpoints_delete_maybe_network_option( 'test', false )
		);

		$this->assertFalse( get_option( 'test' ) );
		$this->assertEquals( 'network_option', get_site_option( 'test' ) );
	}

	/**
	 * Test deleting a maybe network option deletes a network option when requested.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_delete_maybe_network_option
	 */
	public function test_delete_network() {

		update_option( 'test', 'option' );
		update_site_option( 'test', 'network_option' );

		$this->assertTrue(
			wordpoints_delete_maybe_network_option( 'test', true )
		);

		$this->assertEquals( 'option', get_option( 'test' ) );
		$this->assertFalse( get_site_option( 'test' ) );
	}

	/**
	 * Test deleting a maybe network option when the option doesn't exist.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_delete_maybe_network_option
	 */
	public function test_delete_option_nonexistent() {

		$this->assertFalse(
			wordpoints_delete_maybe_network_option( 'test', false )
		);
	}

	/**
	 * Test deleting a maybe network option when the option doesn't exist.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_delete_maybe_network_option
	 */
	public function test_delete_network_option_nonexistent() {

		$this->assertFalse(
			wordpoints_delete_maybe_network_option( 'test', true )
		);
	}
}

// EOF
