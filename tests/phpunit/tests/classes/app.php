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
			$app->sub_apps()->register( 'sub', 'WordPoints_PHPUnit_Mock_Object' )
		);
		
		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $app->get_sub_app( 'sub' )
		);
	}

	/**
	 * Test getting a nonexistent a sub-app.
	 *
	 * @since 1.0.0
	 */
	public function test_get_nonexistent_sub_app() {

		$app = new WordPoints_App( 'test' );

		$this->assertNull( $app->get_sub_app( 'sub' ) );
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
			$app->sub_apps()->register( 'registry', 'WordPoints_Class_Registry' )
		);

		$registry = $app->get_sub_app( 'registry' );

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
			$app->sub_apps()->register(
				'registry'
				, 'WordPoints_Class_Registry_Children'
			)
		);

		$registry = $app->get_sub_app( 'registry' );

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
			$app->sub_apps()->register( 'registry', 'WordPoints_Class_Registry' )
		);

		$app->get_sub_app( 'registry' );

		$this->assertEquals( 1, $mock->call_count );

		$app->get_sub_app( 'registry' );

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
			$app->sub_apps()->register( 'registry', 'WordPoints_App' )
		);

		$app->get_sub_app( 'registry' );

		$this->assertEquals( 0, $mock->call_count );
	}
}

// EOF
