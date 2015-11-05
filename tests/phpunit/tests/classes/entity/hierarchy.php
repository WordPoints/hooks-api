<?php

/**
 * Test case for WordPoints_Entity_Hierarchy.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Hierarchy.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Hierarchy
 */
class WordPoints_Entity_Hierarchy_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the entities in the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entities() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$hierarchy = new WordPoints_Entity_Hierarchy( $entity );

		$this->assertEquals(
			array( 'test' => $entity )
			, $hierarchy->get_entities()
		);
	}

	/**
	 * Test getting the entities in the hierarchy when there are none.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entities_none() {

		$hierarchy = new WordPoints_Entity_Hierarchy();

		$this->assertEquals( array(), $hierarchy->get_entities() );
	}

	/**
	 * Test adding an entity to the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_add_entity() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );

		$this->assertEquals(
			array( 'test' => $entity )
			, $hierarchy->get_entities()
		);

		$entity_2 = new WordPoints_PHPUnit_Mock_Entity( 'test_2' );
		$hierarchy->add_entity( $entity_2 );

		$this->assertEquals(
			array( 'test' => $entity, 'test_2' => $entity_2 )
			, $hierarchy->get_entities()
		);
	}

	/**
	 * Test removing an entity from the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_entity() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity_2 = new WordPoints_PHPUnit_Mock_Entity( 'test_2' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );
		$hierarchy->add_entity( $entity_2 );

		$this->assertEquals(
			array( 'test' => $entity, 'test_2' => $entity_2 )
			, $hierarchy->get_entities()
		);

		$hierarchy->remove_entity( 'test' );

		$this->assertEquals(
			array( 'test_2' => $entity_2 )
			, $hierarchy->get_entities()
		);
	}

	/**
	 * Test removing an entity that isn't present.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_entity_not_there() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );

		$this->assertEquals(
			array( 'test' => $entity )
			, $hierarchy->get_entities()
		);

		$hierarchy->remove_entity( 'other' );

		$this->assertEquals(
			array( 'test' => $entity )
			, $hierarchy->get_entities()
		);
	}

	/**
	 * Test removing an entity from the hierarchy resets the current entity.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_entity_resets_current() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity_2 = new WordPoints_PHPUnit_Mock_Entity( 'test_2' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );
		$hierarchy->add_entity( $entity_2 );

		$this->assertNull( $hierarchy->get_current() );

		$hierarchy->descend( 'test' );
		$hierarchy->descend( 'child' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $hierarchy->get_current()
		);

		$hierarchy->remove_entity( 'test' );

		$this->assertNull( $hierarchy->get_current() );

		$hierarchy->ascend();

		// This tests that the hierarchy was reset as well.
		$this->assertNull( $hierarchy->get_current() );
	}

	/**
	 * Test removing an entity not in the current hierarchy doesn't cause a reset.
	 *
	 * @since 1.0.0
	 */
	public function test_remove_entity_no_reset_not_current() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity_2 = new WordPoints_PHPUnit_Mock_Entity( 'test_2' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );
		$hierarchy->add_entity( $entity_2 );

		$this->assertNull( $hierarchy->get_current() );

		$hierarchy->descend( 'test' );
		$hierarchy->descend( 'child' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $hierarchy->get_current()
		);

		$hierarchy->remove_entity( 'test_2' );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $hierarchy->get_current()
		);

		$hierarchy->ascend();

		$this->assertEquals( $entity, $hierarchy->get_current() );
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

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );

		$this->assertNull( $hierarchy->get_current() );

		$this->assertTrue( $hierarchy->descend( 'test' ) );

		$this->assertEquals( $entity, $hierarchy->get_current() );

		$this->assertTrue( $hierarchy->descend( 'child' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $hierarchy->get_current()
		);

		$hierarchy->ascend();

		$this->assertEquals( $entity, $hierarchy->get_current() );

		$hierarchy->ascend();

		$this->assertNull( $hierarchy->get_current() );
	}

	/**
	 * Test descending when the entity isn't part of the hierarchy.
	 *
	 * @since 1.0.0
	 */
	public function test_descend_not_entity() {

		$hierarchy = new WordPoints_Entity_Hierarchy();

		$this->assertFalse( $hierarchy->descend( 'test' ) );

		$this->assertNull( $hierarchy->get_current() );
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

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );

		$this->assertNull( $hierarchy->get_current() );

		$this->assertTrue( $hierarchy->descend( 'test' ) );

		$this->assertEquals( $entity, $hierarchy->get_current() );

		$this->assertTrue( $hierarchy->descend( 'child' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $hierarchy->get_current()
		);

		$this->assertFalse( $hierarchy->descend( 'grandchild' ) );

		$this->assertInstanceOf(
			'WordPoints_PHPUnit_Mock_Entity_Child'
			, $hierarchy->get_current()
		);
	}

	/**
	 * Test descending when the child doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function test_descend_child_nonexistent() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );

		$this->assertTrue( $hierarchy->descend( 'test' ) );

		$this->assertEquals( $entity, $hierarchy->get_current() );

		$this->assertFalse( $hierarchy->descend( 'child' ) );

		$this->assertEquals( $entity, $hierarchy->get_current() );
	}

	/**
	 * Test ascending when the hierarchy is empty.
	 *
	 * @since 1.0.0
	 */
	public function test_ascend_empty_hierarchy() {

		$hierarchy = new WordPoints_Entity_Hierarchy();

		$hierarchy->ascend();

		$this->assertNull( $hierarchy->get_current() );
	}

	/**
	 * Test getting an entity from an array of slugs.
	 *
	 * @since 1.0.0
	 */
	public function test_get_from_hierarchy() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );

		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entities->children->register(
			'test'
			, 'child_2'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );
		$hierarchy->descend( 'test' );
		$hierarchy->descend( 'child' );

		$this->assertEquals( 'child', $hierarchy->get_current()->get_slug() );

		$from_hierarchy = $hierarchy->get_from_hierarchy(
			array( 'test', 'child_2' )
		);

		$this->assertEquals( 'child_2', $from_hierarchy->get_slug() );

		$this->assertEquals( 'child', $hierarchy->get_current()->get_slug() );

		$hierarchy->ascend();

		$this->assertEquals( $entity, $hierarchy->get_current() );
	}

	/**
	 * Test getting an entity from an invalid array of slugs.
	 *
	 * @since 1.0.0
	 */
	public function test_get_from_hierarchy_invalid() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );

		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );
		$hierarchy->descend( 'test' );
		$hierarchy->descend( 'child' );

		$this->assertEquals( 'child', $hierarchy->get_current()->get_slug() );

		$from_hierarchy = $hierarchy->get_from_hierarchy(
			array( 'test', 'child_2' )
		);

		$this->assertNull( $from_hierarchy );

		$this->assertEquals( 'child', $hierarchy->get_current()->get_slug() );

		$hierarchy->ascend();

		$this->assertEquals( $entity, $hierarchy->get_current() );
	}

	/**
	 * Test getting an entity from an empty array of slugs.
	 *
	 * @since 1.0.0
	 */
	public function test_get_from_hierarchy_empty() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );

		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$hierarchy = new WordPoints_Entity_Hierarchy();
		$hierarchy->add_entity( $entity );
		$hierarchy->descend( 'test' );
		$hierarchy->descend( 'child' );

		$this->assertEquals( 'child', $hierarchy->get_current()->get_slug() );

		$from_hierarchy = $hierarchy->get_from_hierarchy( array() );

		$this->assertNull( $from_hierarchy );

		$this->assertEquals( 'child', $hierarchy->get_current()->get_slug() );

		$hierarchy->ascend();

		$this->assertEquals( $entity, $hierarchy->get_current() );
	}
}

// EOF
