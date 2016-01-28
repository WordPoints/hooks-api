<?php

/**
 * Test case for WordPoints_Hook_Hit_Logger.
 *
 * @package wordpoints-hooks-api
 * @since   1.0.0
 */

/**
 * Tests WordPoints_Hook_Hit_Logger.
 *
 * @since 1.0.0
 *
 * @covers WordPoints_Hook_Hit_Logger
 */
class WordPoints_Hook_Hit_Logger_Test extends WordPoints_PHPUnit_TestCase_Hooks {

	/**
	 * Test logging a hit.
	 *
	 * @since 1.0.0
	 */
	public function test_log_hit() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$hit_id = $fire->hit_logger->log_hit();

		$this->assertInternalType( 'integer', $hit_id );

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );
	}

	/**
	 * Test logging a hit when it supersedes another hit.
	 *
	 * @since 1.0.0
	 */
	public function test_log_hit_superseding() {

		$this->mock_apps();

		// We need to log one hit that can be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit_logger->log_hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit of a different type that supersedes it.
		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit_logger->log_hit();

		$this->assertHitsLogged(
			array(
				'firer' => 'another_firer',
				'reaction_id' => $reaction->ID,
			)
		);

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
		);
	}
}

// EOF
