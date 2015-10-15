<?php

/**
 * Test case for WordPoints_Entities.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entities.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entities
 */
class WordPoints_Entities_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test that it calls an action when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_does_action_on_construct() {

		$mock = new WordPoints_Mock_Filter;

		add_action( 'wordpoints_entities_init', array( $mock, 'action' ) );

		$hooks = new WordPoints_Entities;

		$this->assertEquals( 1, $mock->call_count );

		$this->assertTrue( $hooks === $mock->calls[0][0] );
	}

	/**
	 * Test that it registers the sub-apps when it is constructed.
	 *
	 * @since 1.0.0
	 */
	public function test_registers_sub_apps_on_construct() {

		$entities = new WordPoints_Entities;

		$this->assertInstanceOf( 'WordPoints_Class_Registry', $entities );
		$this->assertInstanceOf( 'WordPoints_Class_Registry_Children', $entities->children );
	}
}

// EOF
