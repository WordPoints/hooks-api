<?php

/**
 * Test case for the hooks functions.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests the hooks functions.
 *
 * @since 1.0.0
 */
class WordPoints_Hooks_Functions_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test initializing the API registers the actions.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_init_hooks
	 */
	public function test_init() {

		$action = new WordPoints_Mock_Filter();

		add_action(
			'wordpoints_init_app_registry-hooks-actions'
			, array( $action, 'action' )
		);

		$this->mock_apps();

		WordPoints_App::$main = null;

		$this->assertEquals( 0, $action->call_count );

		wordpoints_init_hooks();

		$this->assertEquals( 1, $action->call_count );
	}

	/**
	 * Test getting the app.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hooks
	 */
	public function test_get_app() {

		$this->mock_apps();

		$this->assertInstanceOf( 'WordPoints_Hooks', wordpoints_hooks() );
	}

	/**
	 * Test getting the app when the apps haven't been initialized.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hooks
	 */
	public function test_get_app_not_initialized() {

		$this->mock_apps();

		WordPoints_App::$main = null;

		$this->assertInstanceOf( 'WordPoints_Hooks', wordpoints_hooks() );
	}

	/**
	 * Test the reactor registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hook_reactors_init
	 */
	public function test_reactors() {

		$this->mock_apps();

		$reactors = new WordPoints_Class_Registry_Persistent();

		wordpoints_hook_reactors_init( $reactors );

		$this->assertTrue( $reactors->is_registered( 'points' ) );
	}

	/**
	 * Test the extension registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hook_extension_init
	 */
	public function test_extensions() {

		$this->mock_apps();

		$extensions = new WordPoints_Class_Registry_Persistent();

		wordpoints_hook_extension_init( $extensions );

		$this->assertTrue( $extensions->is_registered( 'conditions' ) );
		$this->assertTrue( $extensions->is_registered( 'periods' ) );
	}

	/**
	 * Test the conditions registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hook_conditions_init
	 */
	public function test_conditions() {

		$this->mock_apps();

		$conditions = new WordPoints_Class_Registry_Children();

		wordpoints_hook_conditions_init( $conditions );

		$this->assertTrue( $conditions->is_registered( 'text', 'contains' ) );
		$this->assertTrue( $conditions->is_registered( 'text', 'equals' ) );
		$this->assertTrue( $conditions->is_registered( 'entity', 'equals' ) );
		$this->assertTrue( $conditions->is_registered( 'entity_array', 'contains' ) );
	}

	/**
	 * Test the action registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hook_actions_init
	 */
	public function test_actions() {

		$this->mock_apps();

		$actions = new WordPoints_Hook_Actions();

		wordpoints_hook_actions_init( $actions );

		$this->assertTrue( $actions->is_registered( 'comment_approve' ) );
		$this->assertTrue( $actions->is_registered( 'comment_new' ) );
		$this->assertTrue( $actions->is_registered( 'comment_deapprove' ) );
		$this->assertTrue( $actions->is_registered( 'post_publish' ) );
		$this->assertTrue( $actions->is_registered( 'post_delete' ) );
		$this->assertTrue( $actions->is_registered( 'user_register' ) );
		$this->assertTrue( $actions->is_registered( 'user_delete' ) );
		$this->assertTrue( $actions->is_registered( 'user_visit' ) );
	}

	/**
	 * Test the events registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hook_events_init
	 */
	public function test_events() {

		$this->mock_apps();

		$events = new WordPoints_Hook_Events( 'events' );

		wordpoints_hook_events_init( $events );

		$this->assertTrue( $events->is_registered( 'comment_leave' ) );
		$this->assertTrue( $events->is_registered( 'post_publish' ) );
		$this->assertTrue( $events->is_registered( 'user_register' ) );
		$this->assertTrue( $events->is_registered( 'user_visit' ) );

		$this->assertEquals(
			is_multisite()
			, $events->args->is_registered( 'user_visit', 'current:site' )
		);
	}

	/**
	 * Test the firer registration function.
	 *
	 * @since 1.0.0
	 *
	 * @covers ::wordpoints_hook_firers_init
	 */
	public function test_firers() {

		$this->mock_apps();

		$firers = new WordPoints_Class_Registry_Persistent();

		wordpoints_hook_firers_init( $firers );

		$this->assertTrue( $firers->is_registered( 'fire' ) );
		$this->assertTrue( $firers->is_registered( 'spam' ) );
		$this->assertTrue( $firers->is_registered( 'reverse' ) );
	}
}

// EOF
