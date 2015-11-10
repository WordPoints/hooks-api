<?php

/**
 * Test case for WordPoints_Hook_Arg.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Arg.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Arg
 */
class WordPoints_Hook_Arg_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the arg slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug() {

		$arg = new WordPoints_Hook_Arg( 'test' );

		$this->assertEquals( 'test', $arg->get_slug() );
		$this->assertEquals( 'test', $arg->get_entity_slug() );
	}

	/**
	 * Test getting the arg slug when the slug is an alias.
	 *
	 * @since 1.0.0
	 */
	public function test_get_slug_alias() {

		$arg = new WordPoints_Hook_Arg( 'alias:test' );

		$this->assertEquals( 'alias:test', $arg->get_slug() );
		$this->assertEquals( 'test', $arg->get_entity_slug() );
	}

	/**
	 * Test getting the arg value.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$entity_id = 13;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test_action'
			, array( $entity_id )
		);

		$arg = new WordPoints_Hook_Arg( 'test_entity', $action );

		$this->assertEquals( $entity_id, $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity', $entity );

		$this->assertEquals( $entity_id, $entity->get_the_id() );
		$this->assertEquals( $entity->get_title(), $arg->get_title() );
	}

	/**
	 * Test getting the arg value when the slug is an alias.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value_alias() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$entity_id = 13;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test_action'
			, array( $entity_id )
			, array( 'arg_index' => array( 'current:test_entity' => 0 ) )
		);

		$arg = new WordPoints_Hook_Arg( 'current:test_entity', $action );
		$this->assertEquals( $entity_id, $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity', $entity );

		$this->assertEquals( $entity_id, $entity->get_the_id() );
		$this->assertEquals( $entity->get_title(), $arg->get_title() );
	}

	/**
	 * Test getting the arg value when the entity doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value_invalid_entity() {

		$entity_id = 13;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test_action'
			, array( $entity_id )
		);

		$arg = new WordPoints_Hook_Arg( 'test_entity', $action );
		$this->assertEquals( $entity_id, $arg->get_value() );

		$this->assertFalse( $arg->get_entity() );
		$this->assertEquals( $arg->get_slug(), $arg->get_title() );
	}

	/**
	 * Test getting the arg value when no action is passed.
	 *
	 * @since 1.0.0
	 */
	public function test_get_value_no_action() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$arg = new WordPoints_Hook_Arg( 'test_entity' );

		$this->assertNull( $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity', $entity );

		$this->assertNull( $entity->get_the_id() );
		$this->assertEquals( $entity->get_title(), $arg->get_title() );
	}
}

// EOF
