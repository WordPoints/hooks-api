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
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->reactors );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Children', $hooks->reaction_stores );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Persistent', $hooks->extensions );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Children', $hooks->conditions );
	}

	/**
	 * Test setting the current mode.
	 *
	 * @since 1.0.0
	 */
	public function test_set_current_mode() {

		$hooks = new WordPoints_Hooks( 'hooks' );

		$hooks->set_current_mode( 'standard' );

		$this->assertEquals( 'standard', $hooks->get_current_mode() );

		$hooks->set_current_mode( 'network' );

		$this->assertEquals( 'network', $hooks->get_current_mode() );
	}

	/**
	 * Test that current mode is 'standard' by default.
	 *
	 * @since 1.0.0
	 */
	public function test_standard_mode_by_default() {

		$hooks = new WordPoints_Hooks( 'hooks' );

		$this->assertEquals( 'standard', $hooks->get_current_mode() );
	}

	/**
	 * Test that current mode is 'network' by default in the network admin.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_network_mode_on_if_network_admin() {

		$this->set_network_admin();

		$hooks = new WordPoints_Hooks( 'hooks' );

		$this->assertEquals( 'network', $hooks->get_current_mode() );
	}

	/**
	 * Test getting a reaction store.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_store() {

		$slug = $this->factory->wordpoints->hook_reaction_store->create();

		$reaction_store = wordpoints_hooks()->get_reaction_store( $slug );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Hook_Reaction_Store'
			, $reaction_store
		);

		$this->assertEquals( $slug, $reaction_store->get_slug() );
	}

	/**
	 * Test getting an unregistered reaction store.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_store_unregistered() {

		$this->assertFalse(
			wordpoints_hooks()->get_reaction_store( 'unregistered' )
		);
	}

	/**
	 * Test getting a reaction store when out of context.
	 *
	 * @since 1.0.0
	 */
	public function test_get_reaction_store_out_of_context() {

		$slug = $this->factory->wordpoints->hook_reaction_store->create(
			array(
				'class' => 'WordPoints_PHPUnit_Mock_Hook_Reaction_Store_Contexted',
			)
		);

		wordpoints_entities()->contexts->register(
			'test_context'
			, 'WordPoints_PHPUnit_Mock_Entity_Context_OutOfState'
		);

		$this->assertFalse( wordpoints_hooks()->get_reaction_store( $slug ) );
	}
}

// EOF
