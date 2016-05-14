<?php

/**
 * Test case for WordPoints_Hook_Fire.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Fire.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Fire
 */
class WordPoints_Hook_Fire_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test constructing the fire.
	 *
	 * @since 1.0.0
	 */
	public function test_construct() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$action_type = 'test_fire';

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, $action_type );

		$this->assertEquals( $action_type, $fire->action_type );
		$this->assertEquals( $event_args, $fire->event_args );
		$this->assertEquals( $reaction, $fire->reaction );

		$this->assertInstanceOf( 'WordPoints_Hook_Hit_Logger', $fire->hit_logger );
	}

	/**
	 * Test marking the fire as a hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit() {

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$action_type = 'test_fire';

		$fire = new WordPoints_Hook_Fire( $event_args, $reaction, $action_type );

		$hit_id = $fire->hit();

		$this->assertInternalType( 'integer', $hit_id );
		$this->assertEquals( $fire->hit_id, $hit_id );

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );
	}
}

// EOF
