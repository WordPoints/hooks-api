<?php

/**
 * Test case for WordPoints_Hook_Router.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Router.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Router
 */
class WordPoints_Hook_Router_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test registering an action with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action() {

		$router = new WordPoints_Hook_Router;

		$router->add_action( 'test', array( 'action' => __METHOD__ ) );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test registering an action with a specific priority.
	 *
	 * @since 1.0.0
	 */
	public function test_add_action_priority() {

		$router = new WordPoints_Hook_Router;

		$router->add_action(
			'test'
			, array( 'action' => __METHOD__, 'priority' => 15 )
		);

		$this->assertEquals(
			15
			, has_action( __METHOD__, array( $router, __METHOD__ . ',15' ) )
		);
	}

	/**
	 * Test deregistering an action.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_action() {

		$router = new WordPoints_Hook_Router;

		$router->add_action( 'test', array( 'action' => __METHOD__ ) );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);

		$router->remove_action( 'test' );

		$this->assertFalse(
			has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test deregistering an action with a specific priority.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_action_priority() {

		$router = new WordPoints_Hook_Router;

		$router->add_action(
			'test'
			, array( 'action' => __METHOD__, 'priority' => 15 )
		);

		$this->assertEquals(
			15
			, has_action( __METHOD__, array( $router, __METHOD__ . ',15' ) )
		);

		$router->remove_action( 'test' );

		$this->assertFalse(
			has_action( __METHOD__, array( $router, __METHOD__ . ',15' ) )
		);
	}

	/**
	 * Test deregistering an action when others are still registered doesn't unhook
	 * the router.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_action_still_others() {

		$router = new WordPoints_Hook_Router;

		$router->add_action( 'test', array( 'action' => __METHOD__ ) );
		$router->add_action( 'test_2', array( 'action' => __METHOD__ ) );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);

		$router->remove_action( 'test' );

		$this->assertEquals(
			10
			, has_action( __METHOD__, array( $router, __METHOD__ . ',10' ) )
		);
	}

	/**
	 * Test routing an action with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_route_action() {

		$hooks = $this->mock_apps()->hooks;

		$slug = $this->factory->wordpoints->hook_action->create(
			array( 'action' => __CLASS__ )
		);

		$this->assertEquals( 'test_action', $slug );

		$result = $this->factory->wordpoints->hook_reaction->create();

		$this->assertIsReaction( $result );

		do_action( __CLASS__, 1, 2, 3 );

		/** @var WordPoints_PHPUnit_Mock_Hook_Reactor $reactor */
		$reactor = $hooks->reactors->get( 'test_reactor' );

		$this->assertCount( 1, $reactor->hits );
	}

	/**
	 * Test routing a nonexistent action with the router.
	 *
	 * @since 1.0.0
	 */
	public function test_route_nonexistent_action() {

		$router = new WordPoints_Hook_Router;

		$this->assertNull( $router->{'action,10'}() );
	}

	/**
	 * Test that the first argument is returned, in case a filter is being routed.
	 *
	 * @since 1.0.0
	 */
	public function test_route_filter() {

		$router = new WordPoints_Hook_Router;

		$this->assertEquals( 'arg', $router->{'filter,10'}( 'arg' ) );

	}
}

// EOF
