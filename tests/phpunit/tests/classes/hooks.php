<?php

/**
 * Test case for WordPoints_Hooks.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hooks.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hooks
 */
class WordPoints_Hooks_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test that it calls the wordpoints_hooks_init action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_init_app-hooks', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hooks( 'hooks' );

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $hooks === $mock->calls[0][0] );
	}

	/**
	 * Test that it registers the sub-apps when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_registers_sub_apps_on_construct() {

		$hooks = new WordPoints_Hooks( 'hooks' );

		$this->assertInstanceOf( 'WordPoints_Hook_Router', $hooks->router );
		$this->assertInstanceOf( 'WordPoints_Hook_Actions', $hooks->actions );
		$this->assertInstanceOf( 'WordPoints_Hook_Events', $hooks->events );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->firers );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->reactors );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->extensions );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Children', $hooks->conditions );
	}

	/**
	 * Test setting the network mode.
	 *
	 * @since 1.0.0
	 */
	public function test_set_network_mode() {

		$hooks = new WordPoints_Hooks( 'hooks' );

		$hooks->_set_network_mode( false );

		$this->assertFalse( $hooks->get_network_mode() );

		$hooks->_set_network_mode( true );

		$this->assertTrue( $hooks->get_network_mode() );
	}

	/**
	 * Test setting the network mode to a non-boolean value.
	 *
	 * @since 1.0.0
	 */
	public function test_set_network_mode_non_boolean() {

		$hooks = new WordPoints_Hooks( 'hooks' );

		$hooks->_set_network_mode( '0' );

		$this->assertFalse( $hooks->get_network_mode() );

		$hooks->_set_network_mode( 'hey!' );

		$this->assertTrue( $hooks->get_network_mode() );
	}

	/**
	 * Test that network mode is off by default.
	 *
	 * @since 1.0.0
	 */
	public function test_network_mode_off_by_default() {

		$hooks = new WordPoints_Hooks( 'hooks' );

		$this->assertFalse( $hooks->get_network_mode() );
	}

	/**
	 * Test that network mode is on by default in the network admin.
	 *
	 * @since 1.0.0
	 */
	public function test_network_mode_on_if_network_admin() {

		$this->set_network_admin();

		$hooks = new WordPoints_Hooks( 'hooks' );

		$this->assertTrue( $hooks->get_network_mode() );
	}
}

// EOF
