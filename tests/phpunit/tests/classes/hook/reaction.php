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

		$reactor = new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' );
		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test_storage'
			, $reactor
		);

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 1, $storage );

		$this->assertEquals( 1, $reaction->ID );
		$this->assertEquals( 'test_reactor', $reaction->get_reactor_slug() );
		$this->assertEquals( 'test_storage', $reaction->get_storage_group_slug() );
		$this->assertEquals(
			array( 'network' => 1, 'site' => 1 )
			, $reaction->get_context_id()
		);
	}

	/**
	 * Test constructing class with an invalid ID.
	 *
	 * @since 1.0.0
	 */
	public function test_construct_id_invalid() {

		$storage = new WordPoints_PHPUnit_Mock_Hook_Reaction_Storage(
			'test'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 'invalid', $storage );

		$this->assertFalse( $reaction->ID );
	}
}

// EOF
