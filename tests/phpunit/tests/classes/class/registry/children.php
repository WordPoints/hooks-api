<?php

/**
 * Test case for WordPoints_Class_Registry_Children.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Class_Registry_Children.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Class_Registry_Children
 */
class WordPoints_Class_Registry_Children_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test registering a class.
	 *
	 * @since 1.0.0
	 */
	public function test_register() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'parent', 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'parent', 'test' )
		);
	}

	/**
	 * Test that register() will silently overwrite an existing registry.
	 *
	 * @since 1.0.0
	 */
	public function test_register_overwrite() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'parent', 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'parent', 'test' )
		);

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object2' )
		);

		$this->assertTrue( $registry->is_registered( 'parent', 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object2'
			, $registry->get( 'parent', 'test' )
		);
	}

	/**
	 * Test that is_registered() returns false if a class isn't registered.
	 *
	 * @since 1.0.0
	 */
	public function test_is_registered_not_registered() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertFalse( $registry->is_registered( 'parent', 'test' ) );
	}

	/**
	 * Test getting all registered classes.
	 *
	 * @since 1.0.0
	 */
	public function test_get_all() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue(
			$registry->register( 'parent', 'test_2', 'WordPoints_PHPUnit_Mock_Object2' )
		);

		$objects = $registry->get( 'parent' );

		$this->assertCount( 2, $objects );

		$this->assertArrayHasKey( 'test', $objects );
		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $objects['test']
		);

		$this->assertEquals( 'test', $objects['test']->calls[0]['arguments'][0] );

		$this->assertArrayHasKey( 'test_2', $objects );
		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object2'
			, $objects['test_2']
		);

		$this->assertEquals( 'test_2', $objects['test_2']->calls[0]['arguments'][0] );
	}

	/**
	 * Test getting a registered class.
	 *
	 * @since 1.0.0
	 */
	public function test_get() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue(
			$registry->register( 'parent', 'test_2', 'WordPoints_PHPUnit_Mock_Object2' )
		);

		$object = $registry->get( 'parent', 'test' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $object
		);

		$this->assertEquals( 'test', $object->calls[0]['arguments'][0] );
	}

	/**
	 * Test getting an unregistered class.
	 *
	 * @since 1.0.0
	 */
	public function test_get_unregistered() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertFalse( $registry->get( 'parent', 'test' ) );
	}

	/**
	 * Test getting a registered class a second time returns a new object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_twice() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$object = $registry->get( 'parent', 'test' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $object
		);

		$object_2 = $registry->get( 'parent', 'test' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $object
		);

		$this->assertFalse( $object === $object_2, 'Two objects are not equal.' );
	}

	/**
	 * Test deregistering a class.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'parent', 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'parent', 'test' )
		);

		$this->assertNull( $registry->deregister( 'parent', 'test' ) );

		$this->assertFalse( $registry->is_registered( 'parent', 'test' ) );

		$this->assertFalse( $registry->get( 'parent', 'test' ) );
	}

	/**
	 * Test deregistering an unregistered class.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister_unregistered() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertNull( $registry->deregister( 'parent', 'test' ) );

		$this->assertFalse( $registry->is_registered( 'parent', 'test' ) );

		$this->assertFalse( $registry->get( 'parent', 'test' ) );
	}

	/**
	 * Test deregistering all classes for a given parent.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister_all() {

		$registry = new WordPoints_Class_Registry_Children;

		$this->assertTrue(
			$registry->register( 'parent', 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'parent', 'test' ) );

		$this->assertTrue(
			$registry->register( 'parent', 'test_2', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'parent', 'test_2' ) );

		$this->assertTrue( $registry->is_registered( 'parent' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'parent', 'test' )
		);

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'parent', 'test_2' )
		);

		$this->assertNull( $registry->deregister( 'parent' ) );

		$this->assertFalse( $registry->is_registered( 'parent', 'test' ) );
		$this->assertFalse( $registry->is_registered( 'parent', 'test_2' ) );
		$this->assertFalse( $registry->is_registered( 'parent' ) );

		$this->assertFalse( $registry->get( 'parent', 'test' ) );
		$this->assertFalse( $registry->get( 'parent', 'test_2' ) );
		$this->assertFalse( $registry->get( 'parent' ) );
	}
}

// EOF
