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

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$this->assertEquals( $firer, $fire->firer );
		$this->assertEquals( $event_args, $fire->event_args );
		$this->assertEquals( $reaction, $fire->reaction );
	}
}

// EOF
