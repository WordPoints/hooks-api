<?php

/**
 * Test case for WordPoints_Hook_Reaction.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Reaction.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Reaction
 */
class WordPoints_Hook_Reaction_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test constructing class.
	 *
	 * @since 1.0.0
	 */
	public function test_construct() {

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage( 'test', false );

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 1, $storage );

		$this->assertEquals( 1, $reaction->ID );
		$this->assertEquals( 'test', $reaction->get_reactor_slug() );
		$this->assertFalse( $reaction->is_network_wide() );
	}

	/**
	 * Test constructing class with an invalid ID.
	 *
	 * @since 1.0.0
	 */
	public function test_construct_id_invalid() {

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage( 'test', false );

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 'invalid', $storage );

		$this->assertFalse( $reaction->ID );
	}

	/**
	 * Test that the getter only allows access to the ID.
	 *
	 * @since 1.0.0
	 */
	public function test_getter_only_ID() {

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage( 'test', false );

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 1, $storage );

		$this->assertNull( $reaction->reactor_slug );
	}
}

// EOF
