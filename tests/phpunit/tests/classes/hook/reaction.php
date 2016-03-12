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

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store( 'test_store' );

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 1, $store );

		$this->assertEquals( 1, $reaction->ID );
		$this->assertEquals( 'test_store', $reaction->get_store_slug() );
		$this->assertSame(
			array( 'site' => 1, 'network' => 1 )
			, $reaction->get_context_id()
		);

		$this->assertSame(
			array(
				'id' => 1,
				'store' => 'test_store',
				'context_id' => array( 'site' => 1, 'network' => 1 ),
			)
			, $reaction->get_guid()
		);
	}

	/**
	 * Test constructing class with an invalid ID.
	 *
	 * @since 1.0.0
	 */
	public function test_construct_id_invalid() {

		$store = new WordPoints_PHPUnit_Mock_Hook_Reaction_Store(
			'test'
			, new WordPoints_PHPUnit_Mock_Hook_Reactor( 'test_reactor' )
		);

		$reaction = new WordPoints_PHPUnit_Mock_Hook_Reaction( 'invalid', $store );

		$this->assertFalse( $reaction->ID );
	}
}

// EOF
