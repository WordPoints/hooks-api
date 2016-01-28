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

		$this->assertInstanceOf( 'WordPoints_Hook_Hit_Logger', $fire->hit_logger );
	}

	/**
	 * Test marking the fire as a hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit() {

		$this->mock_apps();

		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$hit_id = $fire->hit();

		$this->assertInternalType( 'integer', $hit_id );
		$this->assertEquals( $fire->hit_id, $hit_id );

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );
	}

	/**
	 * Test marking the fire as a hit when it supersedes another hit.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding() {

		$this->mock_apps();

		// First we create a hit that can be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit of a different type that supersedes it.
		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

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

	/**
	 * Test a prior hit will only be marked as superseded if a different type.
	 *
	 * @since 1.0.0
	 */
	public function test_hit_superseding_not_same_type() {

		$this->mock_apps();

		// First we create a hit that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );

		$fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ) );

		// Then log another hit of the same type.
		$firer = new WordPoints_Hook_Firer( 'test_firer' );
		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ), 2 );

		$this->assertHitsLogged(
			array(
				'reaction_id' => $reaction->ID,
				'superseded_by' => $hit_id,
			)
			, 0
		);
	}

	/**
	 * Test that the superseded hit can be passed in on construction.
	 *
	 * @since 1.0.0
	 */
	public function test_construct_with_superseded_hit() {

		$this->mock_apps();

		// First we create two hits that could be superseded.
		$reaction = $this->factory->wordpoints->hook_reaction->create();
		$event_args = new WordPoints_Hook_Event_Args( array() );
		$firer = new WordPoints_Hook_Firer( 'test_firer' );

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$fire->hit();

		$fire = new WordPoints_Hook_Fire( $firer, $event_args, $reaction );
		$hit_id = $fire->hit();

		$this->assertHitsLogged( array( 'reaction_id' => $reaction->ID ), 2 );

		// Then log another hit of a different type that supersedes one of those.
		$firer = new WordPoints_Hook_Firer( 'another_firer' );
		$fire = new WordPoints_Hook_Fire(
			$firer
			, $event_args
			, $reaction
			, (object) array( 'id' => $hit_id )
		);

		$hit_id = $fire->hit();

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
