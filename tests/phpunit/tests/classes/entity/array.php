<?php

/**
 * Test case for WordPoints_Entity_Array.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Entity_Array.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Entity_Array
 */
class WordPoints_Entity_Array_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the entity slug.
	 *
	 * @since 1.0.0
	 */
	public function test_get_entity_slug() {

		$array = new WordPoints_Entity_Array( 'test_entity' );

		$this->assertEquals( 'test_entity', $array->get_entity_slug() );
	}

	/**
	 * Test setting the value.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$array = new WordPoints_Entity_Array( 'test_entity' );

		$array->set_the_value( array( 1, 2 ) );

		$entities = $array->get_the_entities();

		$this->assertInternalType( 'array', $entities );
		$this->assertCount( 2, $entities );

		$this->assertEquals( 1, $entities[0]->get_the_id() );
		$this->assertEquals( 2, $entities[1]->get_the_id() );
	}

	/**
	 * Test setting the value when the entity type isn't registered.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_not_registered() {

		$array = new WordPoints_Entity_Array( 'test_entity' );

		$array->set_the_value( array( 1, 2 ) );

		$entities = $array->get_the_entities();

		$this->assertInternalType( 'array', $entities );
		$this->assertCount( 0, $entities );
	}

	/**
	 * Test setting the value a second time replaces the former value.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_twice() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$array = new WordPoints_Entity_Array( 'test_entity' );

		$array->set_the_value( array( 1, 2 ) );

		$entities = $array->get_the_entities();

		$this->assertInternalType( 'array', $entities );
		$this->assertCount( 2, $entities );

		$this->assertEquals( 1, $entities[0]->get_the_id() );
		$this->assertEquals( 2, $entities[1]->get_the_id() );

		// The value should be updated, not appended.
		$array->set_the_value( array( 3, 4 ) );

		$entities = $array->get_the_entities();

		$this->assertInternalType( 'array', $entities );
		$this->assertCount( 2, $entities );

		$this->assertEquals( 3, $entities[0]->get_the_id() );
		$this->assertEquals( 4, $entities[1]->get_the_id() );

		// Now we deregister the entity so that the value will be empty.
		wordpoints_entities()->deregister( 'test_entity' );

		$array->set_the_value( array( 5, 6 ) );

		$entities = $array->get_the_entities();

		$this->assertInternalType( 'array', $entities );
		$this->assertCount( 0, $entities );
	}

	/**
	 * Test setting the value with invalid values.
	 *
	 * @since 1.0.0
	 */
	public function test_set_the_value_not_valid() {

		$this->mock_apps();

		wordpoints_entities()->register(
			'test_entity'
			, 'WordPoints_PHPUnit_Mock_Entity_Unsettable'
		);

		$array = new WordPoints_Entity_Array( 'test_entity' );

		$array->set_the_value( array( 1, 2 ) );

		$entities = $array->get_the_entities();

		$this->assertInternalType( 'array', $entities );
		$this->assertCount( 0, $entities );
	}
}

// EOF
