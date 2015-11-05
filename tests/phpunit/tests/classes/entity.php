<?php

/**
 * Test case for WordPoints_Entity.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity
 */
class WordPoints_Entity_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the entity.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity() {

		$return_value = 'entity';

		$mock = new WordPoints_Mock_Filter( $return_value );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );

		$args = array( 1 );

		$this->assertEquals( $return_value, $entity->call( 'get_entity', $args ) );

		$this->assertEquals( $args, $mock->calls[0] );
	}

	/**
	 * Test is_entity() with an object.
	 *
	 * @since 1.0.0
	 */
	public function test_is_entity_object() {

		$object = (object) array( 'id' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertTrue( $entity->call( 'is_entity', array( $object ) ) );
	}

	/**
	 * Test is_entity() with an object that isn't an entity.
	 *
	 * @since 1.0.0
	 */
	public function test_is_entity_object_not() {

		$object = (object) array( 'not' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertFalse( $entity->call( 'is_entity', array( $object ) ) );
	}

	/**
	 * Test is_entity() with an array.
	 *
	 * @since 1.0.0
	 */
	public function test_is_entity_array() {

		$array = array( 'id' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertTrue( $entity->call( 'is_entity', array( $array ) ) );
	}

	/**
	 * Test is_entity() with an array that isn't an entity.
	 *
	 * @since 1.0.0
	 */
	public function test_is_entity_array_not() {

		$array = array( 'not' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertFalse( $entity->call( 'is_entity', array( $array ) ) );
	}

	/**
	 * Test is_entity() with an non-entity value.
	 *
	 * @since 1.0.0
	 */
	public function test_is_entity_not() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertFalse( $entity->call( 'is_entity', array( 'not' ) ) );
	}

	/**
	 * Test get_attr_value() with an object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_attr_value_object() {

		$object = (object) array( 'id' => 1, 'a' => 'b' );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertEquals(
			'b'
			, $entity->call( 'get_attr_value', array( $object, 'a' ) )
		);
	}

	/**
	 * Test get_attr_value() with an object and an attr that isn't set.
	 *
	 * @since 1.0.0
	 */
	public function test_get_attr_value_not_set() {

		$object = (object) array( 'not' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertNull(
			$entity->call( 'get_attr_value', array( $object, 'a' ) )
		);
	}

	/**
	 * Test get_attr_value() with an array.
	 *
	 * @since 1.0.0
	 */
	public function test_get_attr_value_array() {

		$array = array( 'id' => 1, 'a' => 'b' );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertEquals(
			'b'
			, $entity->call( 'get_attr_value', array( $array, 'a' ) )
		);
	}

	/**
	 * Test get_attr_value() with an array and an attr that isn't set.
	 *
	 * @since 1.0.0
	 */
	public function test_get_attr_value_array_not_set() {

		$array = array( 'not' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertNull( $entity->call( 'get_attr_value', array( $array, 'a' ) ) );
	}

	/**
	 * Test get_entity_id() with an object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_id_object() {

		$object = (object) array( 'id' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertEquals( 1, $entity->call( 'get_entity_id', array( $object ) ) );
	}

	/**
	 * Test get_entity_id() with an array.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_id_array() {

		$array = array( 'id' => 1 );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertEquals( 1, $entity->call( 'get_entity_id', array( $array ) ) );
	}

	/**
	 * Test get_id_field().
	 *
	 * @since 1.0.0
	 */
	public function test_get_id_field() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertEquals( 'id', $entity->get_id_field() );
	}

	/**
	 * Test get_human_id() with an object.
	 *
	 * @since 1.0.0
	 */
	public function test_get_human_id_object() {

		$object = (object) array( 'id' => 1, 'title' => 'Title' );

		$mock = new WordPoints_Mock_Filter( $object );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );
		$entity->set( 'human_id_field', 'title' );

		$this->assertEquals( 'Title', $entity->get_human_id( 1 ) );
	}

	/**
	 * Test get_human_id() with an array.
	 *
	 * @since 1.0.0
	 */
	public function test_get_human_id_array() {

		$object = array( 'id' => 1, 'title' => 'Title' );

		$mock = new WordPoints_Mock_Filter( $object );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );
		$entity->set( 'human_id_field', 'title' );

		$this->assertEquals( 'Title', $entity->get_human_id( 1 ) );
	}

	/**
	 * Test get_human_id() with an invalid ID.
	 *
	 * @since 1.0.0
	 */
	public function test_get_human_id_invalid() {

		$mock = new WordPoints_Mock_Filter( false );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );
		$entity->set( 'human_id_field', 'title' );

		$this->assertNull( $entity->get_human_id( 1 ) );
	}

	/**
	 * Test exists().
	 *
	 * @since 1.0.0
	 */
	public function test_exists() {

		$object = array( 'id' => 1, 'title' => 'Title' );

		$mock = new WordPoints_Mock_Filter( $object );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );

		$this->assertTrue( $entity->exists( 1 ) );
	}

	/**
	 * Test exists() with an ID that doesn't exist.
	 *
	 * @since 1.0.0
	 */
	public function test_exists_not() {

		$mock = new WordPoints_Mock_Filter( false );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );

		$this->assertFalse( $entity->exists( 1 ) );
	}

	/**
	 * Test get_child().
	 *
	 * @since 1.0.0
	 */
	public function test_get_child() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		/** @var WordPoints_PHPUnit_Mock_Entity $entity */
		$entity = $entities->get( 'test' );

		$child = $entity->get_child( 'child' );

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity_Child', $child );
		$this->assertEquals( 'child', $child->get_slug() );
	}

	/**
	 * Test get_child() not registered.
	 *
	 * @since 1.0.0
	 */
	public function test_get_child_unregistered() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );

		/** @var WordPoints_PHPUnit_Mock_Entity $entity */
		$entity = $entities->get( 'test' );

		$child = $entity->get_child( 'child' );

		$this->assertFalse( $child );
	}

	/**
	 * Test get_child() with the value set.
	 *
	 * @since 1.0.0
	 */
	public function test_get_child_with_value() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity_Child'
		);

		$value = array( 'id' => 1 );

		/** @var WordPoints_PHPUnit_Mock_Entity $entity */
		$entity = $entities->get( 'test' );
		$entity->set_the_value( $value );

		$child = $entity->get_child( 'child' );

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity_Child', $child );
		$this->assertEquals( 'child', $child->get_slug() );
		$this->assertEquals( $value['id'], $child->get_the_value() );
	}

	/**
	 * Test get_child() with the value set but child doesn't implement the interface.
	 *
	 * @since 1.0.0
	 */
	public function test_get_child_with_value_not_child() {

		$this->mock_apps();

		$entities = wordpoints_entities();
		$entities->register( 'test', 'WordPoints_PHPUnit_Mock_Entity' );
		$entities->children->register(
			'test'
			, 'child'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$value = array( 'id' => 1 );

		/** @var WordPoints_PHPUnit_Mock_Entity $entity */
		$entity = $entities->get( 'test' );
		$entity->set_the_value( $value );

		$child = $entity->get_child( 'child' );

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity', $child );
		$this->assertEquals( 'child', $child->get_slug() );
		$this->assertNull( $child->get_the_value() );
	}

	/**
	 * Test set_the_value() with an ID.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_from_id() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertTrue( $entity->set_the_value( 1 ) );

		$this->assertEquals( 1, $entity->get_the_value() );
		$this->assertEquals( 1, $entity->get_the_id() );
		$this->assertEquals( 'test', $entity->get_the_attr_value( 'type' ) );
	}

	/**
	 * Test set_the_value() with an entity.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_from_entity() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertTrue(
			$entity->set_the_value( array( 'id' => 1, 'type' => 'test' ) )
		);

		$this->assertEquals( 1, $entity->get_the_value() );
		$this->assertEquals( 1, $entity->get_the_id() );
		$this->assertEquals( 'test', $entity->get_the_attr_value( 'type' ) );
	}

	/**
	 * Test set_the_value() with an invalid ID.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_from_id_invalid() {

		$mock = new WordPoints_Mock_Filter( false );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );

		$this->assertFalse( $entity->set_the_value( 1 ) );

		$this->assertNull( $entity->get_the_value() );
		$this->assertNull( $entity->get_the_id() );
		$this->assertNull( $entity->get_the_attr_value( 'type' ) );
	}

	/**
	 * Test set_the_value() with an invalid entity.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_from_entity_invalid() {

		$mock = new WordPoints_Mock_Filter( false );

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );
		$entity->set( 'getter', array( $mock, 'filter' ) );

		$this->assertFalse( $entity->set_the_value( array( 'type' => 'test' ) ) );

		$this->assertNull( $entity->get_the_value() );
		$this->assertNull( $entity->get_the_id() );
		$this->assertNull( $entity->get_the_attr_value( 'type' ) );
	}

	/**
	 * Test set_the_value() twice.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_twice() {

		$entity = new WordPoints_PHPUnit_Mock_Entity( 'test' );

		$this->assertTrue( $entity->set_the_value( 1 ) );

		$this->assertEquals( 1, $entity->get_the_value() );
		$this->assertEquals( 1, $entity->get_the_id() );
		$this->assertEquals( 'test', $entity->get_the_attr_value( 'type' ) );

		$mock = new WordPoints_Mock_Filter( false );

		$entity->set( 'getter', array( $mock, 'filter' ) );

		$this->assertFalse( $entity->set_the_value( array( 'type' => 'test' ) ) );

		$this->assertNull( $entity->get_the_value() );
		$this->assertNull( $entity->get_the_id() );
		$this->assertNull( $entity->get_the_attr_value( 'type' ) );
	}
}

// EOF
