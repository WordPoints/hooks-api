<?php

/**
 * Test case for WordPoints_App.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_App.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_App
 */
class WordPoints_App_Test extends WordPoints_PHPUnit_TestCase {

	/**
	 * Test that it calls an action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_init_app-test', array( $mock, 'action' ) );

		$app = new WordPoints_App( 'test' );

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $app === $mock->calls[0][0] );
	}

	/**
	 * Test that it uses the parent slug if it is passed a parent.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct_parent() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_init_app-parent-test', array( $mock, 'action' ) );

		$parent = new WordPoints_App( 'parent' );
		$app = new WordPoints_App( 'test', $parent );

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $app === $mock->calls[0][0] );
	}

	/**
	 * Test that it uses the parent and grandparent slugs if it is passed a child as
	 * the parent.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct_grandparent() {

		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app-grandparent-parent-test'
			, array( $mock, 'action' )
		);

		$grandparent = new WordPoints_App( 'grandparent' );
		$parent = new WordPoints_App( 'parent', $grandparent );
		$app = new WordPoints_App( 'test', $parent );

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $app === $mock->calls[0][0] );
	}

	/**
	 * Test that it doesn't use the parent slug if it is the 'apps' app.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct_parent_apps() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_init_app-test', array( $mock, 'action' ) );

		$parent = new WordPoints_App( 'apps' );
		$app = new WordPoints_App( 'test', $parent );

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $app === $mock->calls[0][0] );
	}

	/**
	 * Test that it only uses the parent slug if the parent is an app.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct_parent_not_app() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_init_app-test', array( $mock, 'action' ) );

		$parent = (object) array( 'full_slug' => 'parent' );
		$app = new WordPoints_App( 'test', $parent );

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $app === $mock->calls[0][0] );
	}

	/**
	 * Test getting a sub-app.
	 *
	 * @since 1.0.0
	 */
	public function test_get_sub_app() {

		$app = new WordPoints_App( 'test' );

		$this->assertTrue(
			$app->sub_apps->register( 'sub', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( isset( $app->sub ) );

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Object', $app->sub );
	}

	/**
	 * Test getting a nonexistent a sub-app.
	 *
	 * @since 1.0.0
	 */
	public function test_get_nonexistent_sub_app() {

		$app = new WordPoints_App( 'test' );

		$this->assertNull( $app->sub );
		$this->assertFalse( isset( $app->sub ) );
	}

	/**
	 * Test that getting a registry sub-app calls an init action.
	 *
	 * @since 1.0.0
	 */
	public function test_get_registry_sub_app() {

		$app = new WordPoints_App( 'test' );
		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register( 'registry', 'WordPoints_Class_Registry' )
		);

		$registry = $app->registry;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $registry === $mock->calls[0][0] );
	}

	/**
	 * Test that getting a child registry sub-app calls an init action.
	 *
	 * @since 1.0.0
	 */
	public function test_get_child_registry_sub_app() {

		$app = new WordPoints_App( 'test' );
		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register(
				'registry'
				, 'WordPoints_Class_Registry_Children'
			)
		);

		$registry = $app->registry;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $registry === $mock->calls[0][0] );
	}

	/**
	 * Test that getting a registry sub-app calls an init action only the first time.
	 *
	 * @since 1.0.0
	 */
	public function test_get_registry_sub_app_twice() {

		$app = new WordPoints_App( 'test' );
		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register( 'registry', 'WordPoints_Class_Registry' )
		);

		$app->registry;

		$this->assertEquals( 1, $mock->call_count );

		$app->registry;

		$this->assertEquals( 1, $mock->call_count );
	}

	/**
	 * Test that getting a non-registry sub-app doesn't call an init action.
	 *
	 * @since 1.0.0
	 */
	public function test_get_non_registry_sub_app() {

		$app = new WordPoints_App( 'test' );
		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register( 'registry', 'WordPoints_App' )
		);

		$app->registry;

		$this->assertEquals( 0, $mock->call_count );
	}

	/**
	 * Test that setting a var is not allowed.
	 *
	 * @since 1.0.0
	 *
	 * @expectedIncorrectUsage WordPoints_App::__set
	 */
	public function test_setting_var_not_allowed() {

		$app = new WordPoints_App( 'test' );

		$app->sub = array();

		$this->assertNull( $app->sub );
	}

	/**
	 * Test that overwriting a sub-app is not allowed.
	 *
	 * @since 1.0.0
	 *
	 * @expectedIncorrectUsage WordPoints_App::__set
	 */
	public function test_setting_var_overwrite_not_allowed() {

		$app = new WordPoints_App( 'test' );

		$this->assertTrue(
			$app->sub_apps->register( 'sub', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$app->sub = array();

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Object', $app->sub );
	}

	/**
	 * Test that unsetting a sub-app is not allowed.
	 *
	 * @since 1.0.0
	 *
	 * @expectedIncorrectUsage WordPoints_App::__unset
	 */
	public function test_unsetting_var_not_allowed() {

		$app = new WordPoints_App( 'test' );

		$this->assertTrue(
			$app->sub_apps->register( 'sub', 'WordPoints_PHPUnit_Mock_Object' )
		);

		unset( $app->sub );

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Object', $app->sub );
	}

	/**
	 * Test when first getting a sub-app it is possible to access it in the action.
	 *
	 * @since 1.0.0
	 */
	public function test_get_registry_sub_app_access_sub_app() {

		$this->mock_apps();

		$apps = WordPoints_App::$main = new WordPoints_App( 'apps' );

		$apps->sub_apps->register( 'test', 'WordPoints_App' );

		$app = $apps->sub_apps->get( 'test' );

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $this, 'action_get_sub_app' )
		);

		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register( 'registry', 'WordPoints_Class_Registry' )
		);

		$registry = $app->registry;

		$this->assertInstanceOf( 'WordPoints_Class_Registry', $registry );

		$this->assertEquals( 1, $mock->call_count );

		$app->sub_apps->deregister( 'registry' );

		$this->assertNull( $app->registry );
	}

	/**
	 * Accesses a sub app.
	 *
	 * @since 1.0.0
	 */
	public function action_get_sub_app() {

		$app = wordpoints_apps()->test;

		$this->assertInstanceOf( 'WordPoints_Class_Registry', $app->registry );
	}

	/**
	 * Test when first getting a sub-app a warning is issued if it is modified during
	 * the action.
	 *
	 * @since 1.0.0
	 *
	 * @expectedIncorrectUsage WordPoints_App::__get
	 */
	public function test_get_registry_sub_app_modify_sub_app() {

		$this->mock_apps();

		$apps = WordPoints_App::$main = new WordPoints_App( 'apps' );

		$apps->sub_apps->register( 'test', 'WordPoints_App' );

		$app = $apps->sub_apps->get( 'test' );

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $this, 'action_modify_sub_app' )
		);

		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-registry'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register( 'registry', 'WordPoints_Class_Registry' )
		);

		$registry = $app->registry;

		$this->assertInstanceOf( 'WordPoints_Class_Registry', $registry );

		$this->assertEquals( 1, $mock->call_count );

		$app->sub_apps->deregister( 'registry' );

		$this->assertNull( $app->registry );
	}

	/**
	 * Modifies a sub app.
	 *
	 * @since 1.0.0
	 */
	public function action_modify_sub_app() {

		$app = wordpoints_apps()->test;

		$this->assertInstanceOf( 'WordPoints_Class_Registry', $app->registry );

		$app->registry = 'a';

		$this->assertEquals( 'a', $app->registry );
	}

	/**
	 * Test when some sub-app shares the name of a protected property.
	 *
	 * @since 1.0.0
	 *
	 * @expectedException PHPUnit_Framework_Error_Notice
	 * @expectedExceptionMessage Undefined property: WordPoints_App::$parent
	 *
	 * @expectedIncorrectUsage WordPoints_App::__set
	 */
	public function test_get_registry_sub_app_access_restricted_property() {

		$this->mock_apps();

		$apps = WordPoints_App::$main = new WordPoints_App( 'apps' );

		$apps->sub_apps->register( 'test', 'WordPoints_App' );

		$app = $apps->sub_apps->get( 'test' );

		add_action(
			'wordpoints_init_app_registry-test-parent'
			, array( $this, 'action_access_protected_sub_app' )
		);

		$mock = new WordPoints_Mock_Filter;

		add_action(
			'wordpoints_init_app_registry-test-parent'
			, array( $mock, 'action' )
		);

		$this->assertTrue(
			$app->sub_apps->register( 'parent', 'WordPoints_Class_Registry' )
		);

		$registry = $app->parent;

		$this->assertInstanceOf( 'WordPoints_Class_Registry', $registry );

		$this->assertEquals( 1, $mock->call_count );

		$app->sub_apps->deregister( 'parent' );

		$this->assertNull( $app->parent );
	}

	/**
	 * Accesses a sub app that is also a declared protected class property.
	 *
	 * @since 1.0.0
	 */
	public function action_access_protected_sub_app() {

		$app = wordpoints_apps()->test;
		// Attempt to modify it.
		$app->parent = 'a';

		$this->assertNull( $app->parent );
	}
}

// EOF
