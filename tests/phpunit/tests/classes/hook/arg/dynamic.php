<?php

/**
 * Test case for WordPoints_Hook_Arg_Dynamic.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Arg_Dynamic.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Arg_Dynamic
 */
class WordPoints_Hook_Arg_Dynamic_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test getting the arg value.
	 *
	 * @since        1.0.0
	 *
	 * @dataProvider data_provider_hook_args
	 *
	 * @param string $entity_slug The slug of the entity to register.
	 * @param string $arg_slug    The slug to construct the hook arg with.
	 * @param array  $arg_index   The arg index to construct the action with.
	 */
	public function test_get_value( $entity_slug, $arg_slug, $arg_index ) {

		$this->mock_apps();

		wordpoints_entities()->register(
			$entity_slug
			, 'WordPoints_PHPUnit_Mock_Entity'
		);

		$entity_id = 13;

		$action = new WordPoints_PHPUnit_Mock_Hook_Action(
			'test_action'
			, array( $entity_id )
			, array( 'arg_index' => $arg_index )
		);

		$arg = new WordPoints_Hook_Arg_Dynamic( $arg_slug, $action );

		$this->assertEquals( $entity_id, $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity', $entity );

		$this->assertEquals( $entity_id, $entity->get_the_id() );
		$this->assertEquals( $entity->get_title(), $arg->get_title() );
	}

	/**
	 * Provides a list of sets hook arg configurations.
	 *
	 * @since 1.0.0
	 *
	 * @return array[]
	 */
	public function data_provider_hook_args() {

		$return = $basic = array(
			'entity' => array( 'test_entity', 'test_entity', array( 'test_entity' => 0 ) ),
			'alias' => array( 'test_entity', 'current:test_entity', array( 'current:test_entity' => 0 ) ),
		);

		foreach ( $basic as $slug => $data ) {

			$data[0] .= '-a';
			$data[1] .= '-a';

			$return[ "{$slug}_dynamic_generic" ] = $data;

			$data[2] = array( key( $data[2] ) . '-a' => 0 );

			$return[ "{$slug}_dynamic" ] = $data;
		}

		return $return;
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

		$arg = new WordPoints_Hook_Arg_Dynamic( 'test_entity' );

		$this->assertNull( $arg->get_value() );

		$entity = $arg->get_entity();

		$this->assertInstanceOf( 'WordPoints_PHPUnit_Mock_Entity', $entity );

		$this->assertNull( $entity->get_the_id() );
		$this->assertEquals( $entity->get_title(), $arg->get_title() );
	}
}

// EOF
