<?php

/**
 * Test case for WordPoints_Hook_Actions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Actions.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Actions
 */
class WordPoints_Hook_Actions_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test that it calls an action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_hook_actions_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Hook_Actions;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $hooks === $mock->calls[0][0] );
	}

	/**
	 * Test registering an action registers it with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_register() {

		$actions = new WordPoints_Hook_Actions;

		$actions->register(
			'test'
			, 'WordPoints_PHPUnit_Mock_Hook_Action'
			, array( 'action' => __METHOD__ )
		);

		$this->assertEquals(
			10
			, has_action(
				__METHOD__
				, array( wordpoints_apps()->hooks->router, __METHOD__ . ',10' )
			)
		);
	}

	/**
	 * Test deregistering an action deregisters it with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister() {

		$actions = new WordPoints_Hook_Actions;

		$actions->register(
			'test'
			, 'WordPoints_PHPUnit_Mock_Hook_Action'
			, array( 'action' => __METHOD__ )
		);

		$router = wordpoints_apps()->hooks->router;

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);

		$actions->deregister( 'test' );

		$this->assertFalse(
			has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test getting an action instantiates with the passed args.
	 *
	 * @since 1.0.0
	 */
	public function test_get() {

		$actions = new WordPoints_Hook_Actions;

		$actions->register(
			'test'
			, 'WordPoints_PHPUnit_Mock_Hook_Action'
			, array( 'action' => __METHOD__ )
		);

		wordpoints_apps()->entities->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		/** @var WordPoints_Hook_ActionI $action */
		$action = $actions->get(
			'test'
			, array( 5 )
			, array( 'arg_index' => array( 'test_entity' => 0 ) )
		);

		$this->assertEquals( 5, $action->get_arg_value( 'test_entity' ) );
	}
}

// EOF
