<?php

/**
 * Test case for WordPoints_Hook_Arg_Current_Site.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Arg_Current_Site.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Arg_Current_Site
 */
class WordPoints_Hook_Arg_Current_Site_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the arg value.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_get_value() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_Entity_Site'
		);

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test_action', array() );
		$arg = new WordPoints_Hook_Arg_Current_Site( 'test_entity', $action );

		$this->assertEquals( get_current_blog_id(), $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_Entity_Site', $entity );

		$this->assertEquals( get_current_blog_id(), $entity->get_the_id() );
		$this->assertNotEmpty( $arg->get_title() );
	}

	/**
	 * Test getting the arg value in network context returns false.
	 *
	 * @since 1.0.0
	 *
	 * @requires WordPress multisite
	 */
	public function test_get_value_network_context() {

		$this->mock_apps();
		$this->set_network_admin();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_Entity_Site'
		);

		$action = new WordPoints_PHPUnit_Mock_Hook_Action( 'test_action', array() );
		$arg = new WordPoints_Hook_Arg_Current_Site( 'test_entity', $action );

		$this->assertFalse( $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_Entity_Site', $entity );

		$this->assertNull( $entity->get_the_id() );
	}

	/**
	 * Test checking if the arg is stateful.
	 *
	 * @since 1.0.0
	 */
	public function test_is_stateful() {

		$arg = new WordPoints_Hook_Arg_Current_Site( 'test_entity' );

		$this->assertTrue( $arg->is_stateful() );
	}
}

// EOF
