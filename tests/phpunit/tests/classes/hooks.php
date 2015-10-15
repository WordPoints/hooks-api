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
class WordPoints_Hooks_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test that it calls the wordpoints_hooks_init action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hooks_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hooks;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $hooks === $mock->calls[0][0] );
	}

	/**
	 * Test that it registers the sub-apps when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_registers_sub_apps_on_construct() {

		$hooks = new WordPoints_Hooks;

		$this->assertInstanceOf( 'WordPoints_Hook_Actions', $hooks->actions );
		$this->assertInstanceOf( 'WordPoints_Hook_Events', $hooks->events );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->reactors );
		$this->assertInstanceOf( 'WordPoints_Class_Registry', $hooks->conditions );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->extensions );

		$this->assertInstanceOf( 'WordPoints_Hook_Router', $hooks->router );
	}

	/**
	 * Test setting the network mode.
	 *
	 * @since 1.0.0
	 */
	public function test_set_network_mode() {

		$hooks = new WordPoints_Hooks;

		$hooks->set_network_mode( false );

		$this->assertFalse( $hooks->get_network_mode() );

		$hooks->set_network_mode( true );

		$this->assertTrue( $hooks->get_network_mode() );
	}

	/**
	 * Test setting the network mode to a non-boolean value.
	 *
	 * @since 1.0.0
	 */
	public function test_set_network_mode_non_boolean() {

		$hooks = new WordPoints_Hooks;

		$hooks->set_network_mode( '0' );

		$this->assertFalse( $hooks->get_network_mode() );

		$hooks->set_network_mode( 'hey!' );

		$this->assertTrue( $hooks->get_network_mode() );
	}

	/**
	 * Test that network mode is off by default.
	 *
	 * @since 1.0.0
	 */
	public function test_network_mode_off_by_default() {

		$hooks = new WordPoints_Hooks;

		$this->assertFalse( $hooks->get_network_mode() );
	}

	/**
	 * Test that it calls an init action when a registry sub-app is first accessed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_sub_app_init() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hook_conditions_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hooks;

		$conditions = $hooks->conditions;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $conditions === $mock->calls[0][0] );
	}

	/**
	 * Test that it calls an init action when a persistent registry sub-app is first
	 * accessed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_sub_app_init_persistent() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hook_reactors_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hooks;

		$reactors = $hooks->reactors;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $reactors === $mock->calls[0][0] );
	}

	/**
	 * Test that it doesn't call the init action when a registry sub-app is accessed
	 * again.
	 *
	 * @since 1.0.0
	 */
	public function test_sub_app_access_second() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hook_reactors_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hooks;

		$hooks->reactors;

		$this->assertEquals( 1, $mock->call_count );

		$hooks->reactors;

		$this->assertEquals( 1, $mock->call_count );
	}

	/**
	 * Test that it doesn't call the init action when a non-registry sub-app is
	 * accessed again.
	 *
	 * @since 1.0.0
	 */
	public function test_sub_app_access_non_registry() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hook_router_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hooks;

		$hooks->router;

		$this->assertEquals( 0, $mock->call_count );
	}
}

// EOF
