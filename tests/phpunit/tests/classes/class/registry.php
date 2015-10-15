<?php

/**
 * Test case for WordPoints_Class_Registry.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Class_Registry.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Class_Registry
 */
class WordPoints_Class_Registry_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test registering a class.
	 *
	 * @since 1.0.0
	 */
	public function test_register() {

		$registry = new WordPoints_Class_Registry;

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'test' )
		);
	}

	/**
	 * Test that register() will silently overwrite an existing registry.
	 *
	 * @since 1.0.0
	 */
	public function test_register_overwrite() {

		$registry = new WordPoints_Class_Registry;

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'test' )
		);

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object2' )
		);

		$this->assertTrue( $registry->is_registered( 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object2'
			, $registry->get( 'test' )
		);
	}

	/**
	 * Test that is_registered() returns false if a class isn't registered.
	 *
	 * @since 1.0.0
	 */
	public function test_is_registered_not_registered() {

		$registry = new WordPoints_Class_Registry;

		$this->assertFalse( $registry->is_registered( 'test' ) );
	}

	/**
	 * Test getting all registered classes.
	 *
	 * @since 1.0.0
	 */
	public function test_get_all() {

		$registry = new WordPoints_Class_Registry;

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue(
			$registry->register( 'test_2', 'WordPoints_PHPUnit_Mock_Object2' )
		);

		$objects = $registry->get();

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

		$registry = new WordPoints_Class_Registry;

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue(
			$registry->register( 'test_2', 'WordPoints_PHPUnit_Mock_Object2' )
		);

		$object = $registry->get( 'test' );

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

		$registry = new WordPoints_Class_Registry;

		$this->assertFalse( $registry->get( 'test' ) );
	}

	/**
	 * Test getting a registered class a second time returns a new object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_twice() {

		$registry = new WordPoints_Class_Registry;

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$object = $registry->get( 'test' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $object
		);

		$object_2 = $registry->get( 'test' );

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

		$registry = new WordPoints_Class_Registry;

		$this->assertTrue(
			$registry->register( 'test', 'WordPoints_PHPUnit_Mock_Object' )
		);

		$this->assertTrue( $registry->is_registered( 'test' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Object'
			, $registry->get( 'test' )
		);

		$this->assertNull( $registry->deregister( 'test' ) );

		$this->assertFalse( $registry->is_registered( 'test' ) );

		$this->assertFalse( $registry->get( 'test' ) );
	}

	/**
	 * Test deregistering an unregistered class.
	 *
	 * @since 1.0.0
	 */
	public function test_deregister_unregistered() {

		$registry = new WordPoints_Class_Registry;

		$this->assertNull( $registry->deregister( 'test' ) );

		$this->assertFalse( $registry->is_registered( 'test' ) );

		$this->assertFalse( $registry->get( 'test' ) );
	}
}

// EOF
