<?php

/**
 * Test case for WordPoints_Hook_Action.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Action.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Action
 */
class WordPoints_Hook_Action_Test extends PHPUnit_Framework_TestCase {

	/**
	 * Test getting the action slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug() {

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test', array( 5 ) );

		$this->assertEquals( 'test', $action->get_slug() );
	}

	/**
	 * Test checking if an action should fire.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_met() {

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test'
			, array( 5, 'a' )
			, array( 'requirements' => array( 1 => 'a' ) )
		);

		$this->assertTrue( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when the requirements aren't met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_not_met() {

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test'
			, array( 5, 'a' )
			, array( 'requirements' => array( 1 => 'b' ) )
		);

		$this->assertFalse( $action->should_fire() );
	}

	/**
	 * Test that actions should fire by default.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_true_by_default() {

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test', array( 5, 'a' ) );

		$this->assertTrue( $action->should_fire() );
	}

	/**
	 * Test checking if an action should fire when there are multiple requirements,
	 * and some aren't met.
	 *
	 * @since 1.0.0
	 */
	public function test_should_fire_requirements_not_met_multiple() {

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test'
			, array( 5, 'a', true )
			, array( 'requirements' => array( 1 => 'a', 2 => false ) )
		);

		$this->assertFalse( $action->should_fire() );
	}

	/**
	 * Test getting the arg hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_get_arg_value() {

		wordpoints_apps()->entities->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test'
			, array( 5 )
			, array( 'arg_index' => array( 'test_entity' => 0 ) )
		);

		$this->assertEquals( 5, $action->get_arg_value( 'test_entity' ) );
	}

	/**
	 * Test getting the entity ID when it is passed as an arg other than the first.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_id_different_index() {

		wordpoints_apps()->entities->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test'
			, array( 'test', 5 )
			, array( 'arg_index' => array( 'test_entity' => 1 ) )
		);

		$action->set( 'entity_index', 1 );

		$this->assertEquals( 5, $action->get_arg_value( 'test_entity' ) );
	}
}

// EOF
