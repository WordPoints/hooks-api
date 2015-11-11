<?php

/**
 * Test case for WordPoints_Hook_Event_Args.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Event_Args.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Event_Args
 */
class WordPoints_Hook_Event_Args_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the entities in the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entities() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );

		$arg = new WordPoints_PHPUnit_Mock_Hook_Arg( 'test' );
		$arg_2 = new WordPoints_PHPUnit_Mock_Hook_Arg( 'another:test' );

		$args = new WordPoints_Hook_Event_Args( array( $arg, $arg_2 ) );

		$this->assertEquals(
			array(
				'test' => $arg->get_entity(),
				'another:test' => $arg_2->get_entity(),
			)
			, $args->get_entities()
		);
	}

	/**
	 * Test getting the entities in the hierarchy when there are none.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entities_none() {

		$args = new WordPoints_Hook_Event_Args( array() );

		$this->assertEquals( array(), $args->get_entities() );
	}

	/**
	 * Test descending.
	 *
	 * @since 1.0.0
	 */
	public function test_descend() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$args = new WordPoints_Hook_Event_Args( array() );
		$args->add_entity( $entity );

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$args->set_validator( $validator );

		$this->assertNull( $args->get_current() );
		$this->assertEquals( array(), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertTrue( $args->descend( 'test' ) );

		$this->assertEquals( $entity, $args->get_current() );
		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertTrue( $args->descend( 'child' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $args->get_current()
		);

		$this->assertEquals(
			array( 'test', 'child' )
			, $validator->get_field_stack()
		);

		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertTrue( $args->ascend() );

		$this->assertEquals( $entity, $args->get_current() );
		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertTrue( $args->ascend() );

		$this->assertNull( $args->get_current() );
		$this->assertEquals( array(), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );
	}

	/**
	 * Test descending when the entity isn't part of the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_descend_not_entity() {

		$args = new WordPoints_Hook_Event_Args( array() );

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$args->set_validator( $validator );

		$this->assertFalse( $args->descend( 'test' ) );

		$this->assertNull( $args->get_current() );
		$this->assertEquals( array(), $validator->get_field_stack() );
		$this->assertCount( 1, $validator->get_errors() );
	}

	/**
	 * Test descending when the current entity is not a parent.
	 *
	 * @since 1.0.0
	 */
	public function test_descend_not_parent() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$args = new WordPoints_Hook_Event_Args( array() );
		$args->add_entity( $entity );

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$args->set_validator( $validator );

		$this->assertNull( $args->get_current() );
		$this->assertEquals( array(), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertTrue( $args->descend( 'test' ) );

		$this->assertEquals( $entity, $args->get_current() );
		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertTrue( $args->descend( 'child' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $args->get_current()
		);

		$this->assertEquals( array( 'test', 'child' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertFalse( $args->descend( 'grandchild' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $args->get_current()
		);

		$this->assertEquals( array( 'test', 'child' ), $validator->get_field_stack() );
		$this->assertCount( 1, $validator->get_errors() );
	}

	/**
	 * Test descending when the child doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function test_descend_child_nonexistent() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$args = new WordPoints_Hook_Event_Args( array() );
		$args->add_entity( $entity );

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$args->set_validator( $validator );

		$this->assertTrue( $args->descend( 'test' ) );
		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertEquals( $entity, $args->get_current() );

		$this->assertFalse( $args->descend( 'child' ) );

		$this->assertEquals( $entity, $args->get_current() );
		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertCount( 1, $validator->get_errors() );
	}

	/**
	 * Test ascending when the hierarchy is empty.
	 *
	 * @since 1.0.0
	 *
	 * @expectedIncorrectUsage WordPoints_Entity_Hierarchy::ascend
	 */
	public function test_ascend_empty_hierarchy() {

		$args = new WordPoints_Hook_Event_Args( array() );

		$validator = new WordPoints_Hook_Reaction_Validator( array() );
		$args->set_validator( $validator );

		$validator->push_field( 'test' );

		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );

		$this->assertFalse( $args->ascend() );

		$this->assertEquals( array( 'test' ), $validator->get_field_stack() );
		$this->assertEquals( array(), $validator->get_errors() );
	}
}

// EOF
